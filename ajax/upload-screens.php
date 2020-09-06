<?php
// $Id: upload-screens.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';

//$common->ajax()->prepare();

/**
 * Send error message to client
 */
function error($message){
    $data['error'] = 1;
    $data['message'] = __('Error:','dtransport').' '.$message;
    echo json_encode($data);
    die();
}


function dt_upload_screenshots(){
    global $xoopsSecurity;

    $item = rmc_server_var($_REQUEST, 'item', 0);
    $data = rmc_server_var($_REQUEST, 'data', '');

    $dtSettings = RMSettings::module_settings('dtransport');

    $tc = TextCleaner::getInstance();
    
    $data = explode("|", $tc->decrypt($data));

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $ses = new XoopsSessionHandler($db);
    session_decode($ses->read($data[1]));

    $_SERVER['HTTP_USER_AGENT'] = trim($data[0]);

    if(!$xoopsSecurity->check(false))
        error(__('Session token not valid!','dtransport'));

    if($item<=0)
        error(__('Download item ID not provided!','dtransport'));

    $sw = new Dtransport_Software($item);
    if($sw->isNew())
        error(__('Specified download item does not exists!','dtransport'));

    if($sw->getVar('screens')>=$dtSettings->limit_screen)
        error(__('You have reached the limit screens number for this download item!','dtransport'));

    // Directorio de almacenamiento
    $dir = XOOPS_UPLOAD_PATH.'/screenshots';
    if (!is_dir($dir))
        mkdir($dir, 511);

    $dir .= '/'.date('Y', time());
    if (!is_dir($dir))
        mkdir($dir, 511);

    $dir .= '/'.date('m',time());
    if (!is_dir($dir))
        mkdir($dir, 511);

    if (!is_dir($dir.'/ths'))
        mkdir($dir.'/ths', 511);

    if(!is_dir($dir))
        error(__('Directory for store screenshots does not exists!','dtransport'));

    include RMCPATH.'/class/uploader.php';

    $uploader = new RMFileUploader($dir, $dtSettings->image * 1024, array('jpg','gif','png'));

    $err = array();
    if (!$uploader->fetchMedia('Filedata'))
        error($uploader->getErrors());

    if (!$uploader->upload())
        error($uploader->getErrors());

    // Saving image
    require_once XOOPS_ROOT_PATH.'/modules/dtransport/class/screenshot.class.php';
    $img = new Dtransport_Screenshot();
    $img->setDesc('');
    $img->setTitle($uploader->getSavedFileName());
    $img->setImage($uploader->getSavedFileName());
    $img->setDate(time());
    $img->setSoftware($item);

    if(!$img->save()){
        unlink($dir.'/'.$img->image());
        error(__('Screenshot could not be saved!','dtransport'));
    }

    // Resize image
    $thumb = explode(":",$dtSettings->size_ths);
    $big = explode(":",$dtSettings->size_image);
    $sizer = new RMImageResizer($dir.'/'.$img->getVar('image'), $dir.'/ths/'.$img->getVar('image'));

    // Thumbnail
    if(!isset($thumb[2]) || $thumb[2]=='crop'){
        $sizer->resizeAndCrop($thumb[0], $thumb[1]);
    } else {
        $sizer->resizeWidthOrHeight($thumb[0], $thumb[1]);
    }

    // Full size image
    $sizer->setTargetFile($dir.'/'.$img->image());
    if(!isset($big[2]) || $big[2]=='crop'){
        $sizer->resizeAndCrop($big[0], $big[1]);
    } else {
        $sizer->resizeWidthOrHeight($big[0], $big[1]);
    }

    $ret = array(
        'image'  => $uploader->getSavedFileName(),
        'dir'   => str_replace(XOOPS_UPLOAD_PATH, XOOPS_UPLOAD_URL, $dir),
        'token' => $xoopsSecurity->createToken(),
        'type'  => $uploader->getMediaType(),
        'error' => 0,
        'id'    => $img->id()
    );
    echo json_encode($ret);
    die();
}

function dt_delete_screen(){
    global $xoopsSecurity, $xoopsConfig, $xoopsModule, $xoopsUser;

    $rmf = RMFunctions::get();
    $xoopsModule = $rmf->load_module('dtransport');

    include_once '../../../include/cp_header.php';

    $func = new Dtransport_Functions();

    if(!$xoopsSecurity->check())
        $func->dt_send_message(__('Session token not valid!','dtransport'), 1, 0);

    $id = rmc_server_var($_POST, 'id', 0);
    $sc = new Dtransport_Screenshot($id);
    if($sc->isNew())
        $func->dt_send_message(__('Specified screenshot does not exists!','dtransport'), 1, 1);

    if(!$sc->delete())
        $func->dt_send_message(__('Screenshot could not be deleted!','dtransport').'<br />'.$sc->errors(), 1, 1);

    $ret = array(
        'id' => $sc->id()
    );

    $func->dt_send_message($ret, 0, 1);
}


$action = rmc_server_var($_REQUEST, 'action', '');

switch($action){
    case 'upload':
        dt_upload_screenshots();
        break;
    case 'get-info':
        dt_get_information();
        break;
    case 'save-screen-data':
        dt_save_screen_info();
        break;
    case 'delete-screen':
        dt_delete_screen();
        break;
}