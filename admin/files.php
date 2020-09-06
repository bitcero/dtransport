<?php
// $Id: files.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCSUBLOCATION','files');
include ('header.php');
$common->location = 'items';

/**
* @desc Visualiza todos los archivos de un software
**/
function showFiles(){
    global $xoopsModule, $xoopsSecurity, $tpl, $common, $cuIcons;

	$item = rmc_server_var($_REQUEST, 'item', 0);
	$edit = rmc_server_var($_REQUEST, 'edit', 0);
	$id = rmc_server_var($_REQUEST, 'id', 0);

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if($item<=0)
        redirectMsg('items.php', __('Before to view files, you must specify a download item!','dtransport'), RMMSG_INFO);

		$sw=new Dtransport_Software($item);

        if($sw->isNew())
            redirectMsg('items.php', __('Specified item does not exists!','dtransport'), RMMSG_WARN);

        $files = array(); // Files data container

		//Archivos sin grupo
		$sql="SELECT * FROM ".$db->prefix('mod_dtransport_files')." WHERE id_soft=$item AND `group`=0";
		$result=$db->queryF($sql);

		while($rows=$db->fetchArray($result)){
			$fl=new Dtransport_File();
			$fl->assignVars($rows);

			$files[] = array(
                'id'=>$fl->id(),
                'file'=>$fl->file(),
                'downs'=>$fl->hits(),
                'group'=>$fl->group(),
			    'default'=>$fl->isDefault(),
                'remote'=>$fl->remote(),
                'type'=>'files',
                'title'=>$fl->title()
            );

		}
		

		//Grupos pertenecientes al software
		$groups=$sw->filegroups();

		foreach ($groups as $k){
			$gr = new Dtransport_FileGroup($k);

			$files[] = array(
                'id'=>$gr->id(),
                'file'=>$gr->name(),
                'type'=>'group'
            );

			$sql="SELECT * FROM ".$db->prefix('mod_dtransport_files')." WHERE id_soft=$item AND `group`=$k";
			$result=$db->queryF($sql);
			while($rows=$db->fetchArray($result)){
				$fl=new Dtransport_File();
				$fl->assignVars($rows);

				$files[] = array(
                    'id'=>$fl->id(),
                    'file'=>$fl->file(),
                    'downs'=>$fl->hits(),
                    'group'=>$fl->group(),
				    'default'=>$fl->isDefault(),
                    'remote'=>$fl->remote(),
                    'type'=>'files',
                    'title'=>$fl->title()
                );
			
			}
		
		}
		
        $groups = array();

		//Lista de grupos
		$sql ="SELECT * FROM ".$db->prefix('mod_dtransport_groups')." WHERE id_soft=$item";
		$result=$db->queryF($sql);
		while($rows=$db->fetchArray($result)){
			$group=new Dtransport_FileGroup();
			$group->assignVars($rows);

			$groups[] = array(
                'id'=>$group->id(),
                'name'=>$group->name()
            );

		}

    $common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
    $common->breadcrumb()->add_crumb($sw->name, 'items.php?action=edit&id='.$sw->id());
    $common->breadcrumb()->add_crumb(__('Files', 'dtransport'));

	// Title
    $title = sprintf(__('Files for "%s"','dtransport'), $sw->getVar('name'));
    $tpl->assign('xoops_pagetitle', $title);

    Dtransport_Functions::itemsToolbar($sw);
    $tpl->add_style('admin.css','dtransport');

    include DT_PATH.'/include/js-strings.php';

    $tpl->add_script('admin.min.js', 'dtransport', ['id' => 'admin-js', 'footer' => 1]);

    xoops_cp_header();
	
    include $common->template()->path('admin/dtrans-files.php', 'module', 'dtransport');

	xoops_cp_footer();
}



/**
* @desc Formulario de archivos
**/
function formFiles($edit=0){
	global $tpl, $xoopsModule, $xoopsModuleConfig, $xoopsUser, $xoopsSecurity, $common, $cuIcons;

	$id = rmc_server_var($_GET, 'id', 0);
	$item = rmc_server_var($_GET, 'item', 0);


	//Verificamos si el software es válido
	if ($item<=0)
		redirectMsg('files.php', __('No download item ID has been provided!','dtransport'), RMMSG_WARN);

	//Verificamos si existe el software
	$sw = new Dtransport_Software($item);
	if ($sw->isNew())
		redirectMsg('files.php', __('Specified download item does not exists!','dtransport'), RMMSG_ERROR);

    $file_exists = true;
	
	if ($edit){
		//Verificamos si archivo es válido
		if ($id<=0)
			redirectMsg('./files.php?item='.$item, __('No file ID has been specified!','dtransport'), RMMSG_WARN);

		//Verificamos si existe archivo
		$fl = new Dtransport_File($id);
		if ($fl->isNew())
			redirectMsg('files.php?item='.$item, __('Specified file does not exists!','dtransport'), RMMSG_ERROR);

        if($sw->getVar('secure'))
            $dir = $xoopsModuleConfig['directory_secure'];
        else
            $dir = $xoopsModuleConfig['directory_insecure'];

        if(!$fl->remote()){

            if(!is_file($dir.'/'.$fl->file())){
                $file_exists = false;
                showMessage(sprintf(__('File %s does not exists! You need to upload this file again.','dtransport'), $dir .'/'.$fl->file()), RMMSG_WARN);
            }


        }

	}	

    $common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
    $common->breadcrumb()->add_crumb($sw->name, 'items.php?action=edit&id=' . $sw->id());
    $common->breadcrumb()->add_crumb(__('Files', 'dtransport'), 'files.php?item=' . $sw->id());
    $common->breadcrumb()->add_crumb(__('Add File', 'dtransport'));

	xoops_cp_location("<a href='./'>".$xoopsModule->name()."</a> &raquo; <a href='items.php'>".$sw->getVar('name')."</a> &raquo; <a href='files.php?item=".$sw->id()."'>".__('Files','dtransport')."</a> &raquo; ".($edit ? __('Edit file','dtransport') : __('New file','dtransport')));
    $tpl->assign('xoops_pagetitle', $xoopsModule->name()." &raquo; ".$sw->getVar('name')." &raquo; ".__('Files','dtransport')." &raquo; ".($edit ? __('Edit file','dtransport') : __('New file','dtransport')));

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $func = new Dtransport_Functions();
    $func->itemsToolbar($sw);

    $rmf = RMFunctions::get();
    $rmu = RMUtilities::get();

    // Uploader
    $tc = TextCleaner::getInstance();

    $groups = array();

    //Lista de grupos
    $sql ="SELECT * FROM ".$db->prefix('mod_dtransport_groups')." WHERE id_soft=$item";
    $result=$db->queryF($sql);
    while($rows=$db->fetchArray($result)){
        $group=new Dtransport_FileGroup();
        $group->assignVars($rows);

        $groups[] = array(
            'id'=>$group->id(),
            'name'=>$group->name()
        );

    }

    $tpl->add_style('cp.min.css','dtransport');
    //$tpl->add_style('files.css','dtransport');

    $tpl->add_script('dropzone.min.js','rmcommon', ['id' => 'dropzone-js', 'footer' => 1]);
    $tpl->add_script('admin.min.js','dtransport', ['id' => 'admin-js', 'footer' => 1]);
    $tpl->add_inline_script('var upload_error = 0;');
    $tpl->add_inline_script('var dtExts = ".' . implode(', .', $common->settings()->module_settings('dtransport', 'type_file')) . '";
    dtSize = ' . $common->settings()->module_settings('dtransport', 'size_file') .';');

    include DT_PATH.'/include/js-strings.php';

	xoops_cp_header();

    include $tpl->path("admin/dtrans-files-form.php", 'module','dtransport');

	xoops_cp_footer();

}

/**
* @desc Elimina el grupo especificado
**/
function deleteGroups(){

	global $xoopsModule,$util, $xoopsSecurity;

	$id = rmc_server_var($_REQUEST, 'id', array());
	$item = rmc_server_var($_REQUEST, 'item', 0);

    if (!$xoopsSecurity->check()){
        redirectMsg('files.php?item='.$item, __('Session token not valid!','dtransport'), RMMSG_WARN);
        die();
    }

	//Verificamos si el software es válido
	if ($item<=0){
		redirectMsg('files.php', __('Download item ID not provided!','dtransport'), RMMSG_WARN);
		die();
	}
	
	//Verificamos si existe el software
	$sw = new Dtransport_Software($item);
	if ($sw->isNew()){
		redirectMsg('files.php', __('Specified download item does not exists!','dtransport'), RMMSG_WARN);
		die();
	}

	//Verificamos si grupo es válido
	if ($id<=0){
		redirectMsg('files.php?item='.$item, __('Group id not provided!','dtransport'), RMMSG_ERROR);
		die();
	}

	//Verificamos si el grupo existe
	$group = new Dtransport_FileGroup($id);
	if ($group->isNew()){
		redirectMsg('files.php?item='.$item, __('Specified group does not exists!','dtransport'), RMMSG_ERROR);
		die();
	}

	if (!$group->delete()){
		redirectMsg('files.php?item='.$item, sprintf(__('Group %s could not be deleted!','dtransport'), '<strong>'.$group->name().'</strong>').'<br />'.$group->errors(),1);
		die();
	}else{
		redirectMsg('files.php?item='.$item, sprintf(__('Group %s deleted successfully!','dtransport'), '<strong>'.$group->name().'</strong>'),0);
		die();
	}

}


function defaultFile(){
    global $common;

    $id = $common->httpRequest()->get('id', 'integer', 0);
    $item = $common->httpRequest()->get('item', 'integer', 0);

    if($item <= 0){
        $common->uris()->redirect_with_message(
            __('No download item ID has been provided!', 'dtransport'), 'items.php', RMMSG_ERROR
        );
    }

    $sw = new Dtransport_Software($item);
    if($sw->isNew()){
        $common->uris()->redirect_with_message(
            __('Specified download item does not exists!', 'dtransport'), 'items.php', RMMSG_ERROR
        );
    }

    if($id <= 0){
        $common->uris()->redirect_with_message(
            __('No file ID has been provided!', 'dtransport'), 'files.php?item=' . $item, RMMSG_ERROR
        );
    }

    $fl = new Dtransport_File($id);
    if($fl->isNew()){
        $common->uris()->redirect_with_message(
            __('Specified file does not exists!', 'dtransport'), 'files.php?item=' . $item, RMMSG_ERROR
        );
    }

    $table = $common->db()->prefix("mod_dtransport_files");
    $common->db()->queryF("UPDATE $table SET `default`=0 WHERE id_soft=$item AND `default`=1");
    $ret = $common->db()->queryF("UPDATE $table SET `default`=1 WHERE id_soft=$item and id_file=$id");

    if($ret){
        $common->uris()->redirect_with_message(
            __('File updated successfully!', 'dtransport'), 'files.php?item=' . $item, RMMSG_SUCCESS
        );
    } else {
        $common->uris()->redirect_with_message(
            sprintf(__('Errors occurs while trying to update files: %s', 'dtransport'), $common->db()->error()), 'files.php?item=' . $item, RMMSG_ERROR
        );
    }
}


$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action){
	case 'new':
		formFiles();
	    break;
	case 'edit':
		formFiles(1);
	    break;
	case 'save':
		saveFiles();
	    break;
	case 'saveedit':
		saveFiles(1);
	    break;
	case 'savegroup':
		saveGroups();
	    break;
	case 'saveeditgr':
		saveGroups(1);
	    break;
	case 'updategroup':
		updateGroups(1);
	    break;
	case 'delete':
		deleteFiles();
	break;
	case 'deletegroup':
		deleteGroups();
	break;
	case 'default':
		defaultFile();
	    break;
	default:
		showFiles();

}

