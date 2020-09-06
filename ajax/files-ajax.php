<?php
// $Id: files-ajax.php 216 2013-02-03 21:48:29Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../admin/header.php';

global $xoopsLogger;
error_reporting(0);
$xoopsLogger->renderingEnabled = false;
$xoopsLogger->activated = false;

$functions = new Dtransport_Functions();
$db = XoopsDatabaseFactory::getDatabaseConnection();

/**
 * Almacena los datos del grupo en la base de datos
 **/
function dt_save_group($edit=0){
    global $xoopsSecurity, $functions, $db;

    foreach ($_POST as $k=>$v){
        $$k=$v;
    }

    if (!$xoopsSecurity->check())
        $functions->dt_send_message(__('Session token not valid!','dtransport'), 1, 0);

    //Verificamos si el software es válido
    if ($item<=0)
        $functions->dt_send_message(__('Download item ID has not been specified!','dtransport'), 1, 1);

    //Verificamos si existe el software
    $sw = new Dtransport_Software($item);
    if ($sw->isNew())
        $functions->dt_send_message(__('Specified item does not exists!','dtransport'), 1, 1);

    if ($edit){
        //Verificamos si grupo es válido
        if ($id<=0)
            $functions->dt_send_message(__('A group ID has not been specified!','dtransport'), 1, 1);

        //Verificamos si el grupo existe
        $group = new Dtransport_FileGroup($id);
        if ($group->isNew())
            $functions->dt_send_message(__('Specified group does not exists!','dtransport'), 1, 1);

        //Verificamos si existe el nombre del grupo
        $sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_groups')." WHERE name='".$name."' AND id_soft=".$item." AND id_group<>".$group->id();
        list($num) = $db->fetchRow($db->queryF($sql));

    }else{

        //Verificamos si existe el nombre del grupo
        $sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_groups')." WHERE name='".$name."' AND id_soft=".$item;
        list($num) = $db->fetchRow($db->queryF($sql));

        $group = new Dtransport_FileGroup();
    }

    if ($num>0)
        $functions->dt_send_message(__('Another group with same name exists already!','dtransport'), 1, 1);

    $group->setName($name);
    $group->setSoftware($item);


    if ($group->save()){
        $ret = array(
            'message' => __('Database updated successfully!','dtransport'),
            'name'  => $name,
            'item'  => $item,
            'id'    => $group->id(),
            'action'=> $edit?'edit':'create'
        );
        $functions->dt_send_message($ret, 0, 1);
    }else{
        $functions->dt_send_message(__('Database could not be updated','dtransport').'<br />',$group->errors(), 1, 1);
    }

}

/**
 * Delete files from hard disk
 */
function dt_delete_hfile(){
    global $common;

    $file = $common->httpRequest()->post('file', 'string', '');
    $secure = $common->httpRequest()->post('secure', 'integer', 0);

    $common->checkToken();

    $dtSettings = RMSettings::module_settings('dtransport');

    if($secure){
        $dir = rtrim($dtSettings->directory_secure,'/');
    } else{
        $dir = rtrim($dtSettings->directory_insecure,'/');
    }


    if(!is_file($dir.'/'.$file)){
        $common->ajax()->notifyError(__('Specified file does not exists!','dtransport'));
    }

    if(!unlink($dir.'/'.$file)){
        $common->ajax()->notifyError(__('File %s could not be deleted! Please try again.','dtransport'));
    }

    $ret = array(
        'message' => sprintf(__('File %s was deleted successfully!','dtransport'), $file)
    );

    $common->ajax()->response(
        sprintf(__('File %s was deleted successfully!','dtransport'), $file), 0, 1, [
            'notify' => [
                'type' => 'alert-success',
                'icon' => 'svg-rmcommon-ok-circle'
            ]
        ]
    );

}

function dt_get_identifier(){
    global $functions, $xoopsUser;

    $id = rmc_server_var($_POST, 'identifier', '');

    if($id=='')
        $functions->dt_send_message(__('Identifier could not be verified!','dtransport'),1 , 0);

    $tc = TextCleaner::getInstance();
    $data = explode("|", $tc->decrypt($id));

    $rmu = RMFunctions::get();

    if(session_id()!=$data[0] && $xoopsUser->uid()!=$data[1] || !$xoopsUser->isAdmin($rmu->load_module('dtransport')))
        $functions->dt_send_message(__('Critical error!','dtransport'), 1, 0);

    $ret = array('message'=>__('Verified','dtransport'));
    $functions->dt_send_message($ret, 0, 1);
}


/**
 * Save a new or edited file to database
 */
function dt_save_file($edit = 0){
    global $common;

    $common->ajax()->prepare();

    foreach ($_POST as $k=>$v){
        $$k=$v;
    }

    $common->checkToken();

    //Verificamos si el software es válido
    if ($item<=0){
        $common->ajax()->notifyError(__('Item download ID not provided!','dtransport'));
    }

    //Verificamos si existe el software
    $sw = new Dtransport_Software($item);
    if ($sw->isNew()){
        $common->ajax()->notifyError(__('Specified item download does not exists!','dtransport'));
    }

    if ($edit){

        //Verificamos si archivo es válido
        if ($id<=0){
            $common->ajax()->notifyError(__('File ID has not been provided!','dtransport'));
        }

        //Verificamos si existe archivo
        $fl = new Dtransport_File($id);
        if ($fl->isNew()){
            $common->ajax()->notifyError(__('Specified file does not exists!','dtransport'));
        }

        // Si es un archivo remoto eliminamos el archivo actual
        if(!$fl->remote() && $remote){
            $dtSettings = RMSettings::module_settings('dtransport');
            $dir = $sw->getVar('secure') ? $dtSettings->directory_secure : $dtSettings->directory_insecure;

            if(file_exists($dir.'/'.$fl->file()))
                unlink($dir.'/'.$fl->file());

            unset($dir, $dtSettings, $rmu);
        }

    }else{

        $fl=new Dtransport_File();

    }
    
    if($remote){
        $head = array_change_key_case(get_headers($file, TRUE));
        $size = $head['content-length'];
    }

    $fl->setSoftware($item);
    $fl->setRemote($remote);
    $fl->setFile($file);
    $fl->setDefault($default);
    $fl->setGroup($group);
    $fl->setDate(time());
    $fl->setTitle(trim($title));
    $fl->setMime($mime);
    $fl->setSize($size);

    if (false == $fl->save()){
        $common->ajax()->notifyError(__('File could not be saved!','dtransport').'<br />'.$fl->errors());
    }else{

        if($fl->isDefault()) {
            $common->db()->queryF("UPDATE ".$common->db()->prefix("mod_dtransport_files")." SET `default`=0 WHERE id_soft=".$sw->id()." AND id_file !=".$fl->id());
        }

        showMessage(sprintf(__('File %s saved successfully!','dtransport'), $fl->title()), RMMSG_SUCCESS);

        $common->ajax()->response(
            sprintf(__('File %s saved successfully!','dtransport'), $fl->title()), 0, 1, [
                'notify' => [
                    'type' => 'alert-success',
                    'icon' => 'svg-rmcommon-ok-circle'
                ]
            ]
        );

    }
}


function dt_reasign_file(){
    global $xoopsSecurity, $functions;

    if(!$xoopsSecurity->check())
        $functions->dt_send_message(__('Session token not valid!','dtransport'), 1, 0);

    $id = rmc_server_var($_POST, 'id', 0);
    $idgroup = rmc_server_var($_POST, 'idgroup', 0);
    $item = rmc_server_var($_POST, 'item', 0);

    $file = new Dtransport_File($id);
    if($file->isNew())
        $functions->dt_send_message(__('Specified file does not exists!','dtransport'), 1, 1);

    if($idgroup>0){
        $group = new Dtransport_FileGroup($idgroup);
        if($group->isNew())
            $functions->dt_send_message(__('Specified group does not exists!','dtransport'), 1, 1);
    }

    if($file->software()!=$item)
        $functions->dt_send_message(__('This file seems not belong to specified download item!','dtransport'),1, 1);

    $file->setGroup($idgroup);

    if($file->save())
        $functions->dt_send_message(array('message'=>__('File reasigned successfully!','dtransport')), 0, 1);
    else
        $functions->dt_send_message(__('File could not be reasigned!','dtransport'), 1, 1);

}

/**
 * Elimina archivos de la base de datos y el disco duro
 */
function dt_delete_file(){
    global $xoopsSecurity, $functions;

    if(!$xoopsSecurity->check())
        $functions->dt_send_message(__('Session token not valid!','dtransport'), 1, 0);

    $id = rmc_server_var($_POST, 'id', 0);
    $item = rmc_server_var($_POST, 'item', 0);

    $file = new Dtransport_File($id);
    if($file->isNew())
        $functions->dt_send_message(__('Specified file does not exists!','dtransport'), 1, 1);

    $sw = new Dtransport_Software($item);
    if ($sw->isNew())
        $functions->dt_send_message(__('Specified item download does not exists!','dtransport'), 1, 1);

    if($file->software()!=$item)
        $functions->dt_send_message(__('This file seems not belong to specified download item!','dtransport'),1, 1);

    if(!$file->delete())
        $functions->dt_send_message(__('File could not be deleted!','dtransport'), 1, 1);

    $dtSettings = RMSettings::module_settings('dtransport');

    $dir = $sw->getVar('secure') ? $dtSettings->directory_secure : $dtSettings->directory_insecure;

    unlink($dir.'/'.$file->file());

    $functions->dt_send_message(__('File deleted successfully!','dtransport'), 0, 1);

}


$action = rmc_server_var($_REQUEST, 'action', '');

switch($action){
    case 'save-group':
        dt_save_group(0);
        break;
    case 'update-group':
        dt_save_group(1);
        break;
    case 'delete_hfile':
        dt_delete_hfile();
        break;
    case 'delete-file':
        dt_delete_file();
        break;
    case 'identifier':
        dt_get_identifier();
        break;
    case 'save-file':
        dt_save_file(0);
        break;
    case 'save-edit':
        dt_save_file(1);
        break;
    case 'reasign-file':
        dt_reasign_file();
        break;
}
