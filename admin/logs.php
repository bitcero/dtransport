<?php
// $Id: logs.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCSUBLOCATION','logs');
include ('header.php');

$common->location = 'items';

/**
* @desc Visualiza todos los logs existentes para un determinado software
**/
function showLogs(){
	global $tpl,$xoopsConfig,$xoopsModule, $functions, $xoopsSecurity, $common, $cuIcons;

	$item=isset($_REQUEST['item']) ? intval($_REQUEST['item']) : 0;
	$sw=new Dtransport_Software($item);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $tc = TextCleaner::getInstance();
    $tf = new RMTimeFormatter(0, __('%m%-%d%-%Y%','dtransport'));

	$sql ="SELECT * FROM ".$db->prefix('mod_dtransport_logs')." WHERE id_soft=$item";
	$result=$db->queryF($sql);

    $logs = [];
    
	while($rows=$db->fetchArray($result)){
		$log = new Dtransport_Log();
		$log->assignVars($rows);

		$logs[] = array(
            'id'=>$log->id(),
            'title'=>$log->title(),
            'log'=>$tc->truncate($tc->clean_disabled_tags($log->log()),80),
		    'date'=>$tf->format($log->date())
        );
	
	}

    $common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
    $common->breadcrumb()->add_crumb($sw->name, 'items.php?action=edit&id=' . $sw->id());
    $common->breadcrumb()->add_crumb(__('Versions Log', 'dtransport'));

    $functions->itemsToolbar($sw);

    $tpl->add_style('admin.css','dtransport');

    $tpl->add_script('admin.min.js','dtransport', ['id' => 'admin-js', 'footer' => 1]);

    include DT_PATH.'/include/js-strings.php';

	xoops_cp_header();

    include $tpl->path('admin/dtrans-logs.php','module','dtransport');

	xoops_cp_footer();

}


/**
* @desc Formulario de Logs
**/
function dt_form_logs($edit=0){
	global $xoopsModule,$xoopsConfig;

	$id = rmc_server_var($_GET, 'id', 0);
	$item = rmc_server_var($_GET, 'item', 0);

	//Verificamos si el software es válido
	if ($item<=0)
		redirectMsg('items.php', __('Download item ID has not been provided!','dtransport'), RMMSG_WARN);

	//Verificamos si existe el software
	$sw = new Dtransport_Software($item);
	if ($sw->isNew())
		redirectMsg('items.php', __('Specified download item does not exists!','dtransport'),1);

	if ($edit){
		//Verificamos si log es válido
		if ($id<=0)
			redirectMsg('logs.php?item='.$item, __('Log item ID has not been provided!','dtransport'), RMMSG_WARN);

		//Verificamos si log existe
		$log = new Dtransport_Log($id);
		if ($log->isNew())
			redirectMsg('logs.php?item='.$item, __('Specified item log does not exists!','dtranport'),1);

	}
	
	$dtf = new Dtransport_Functions();
    $dtf->itemsToolbar($sw);

	//xoops_cp_location("<a href='./'>".$xoopsModule->name()."</a> &raquo; <a href='./items.php'>".sprintf(__('"%s" Logs','dtransport'),$sw->getVar('name'))."</a> &raquo; ".($edit ? __('Edit Log','dtransport') : __('New Log','dtransport')));
	xoops_cp_header();

	$form =new RMForm($edit ? sprintf(__('Edit Change Log of "%s"','dtransport'), $sw->getVar('name')) : sprintf(__('New Log for "%s"','dtransport'),$sw->getVar('name')),'frmlog','logs.php');

	$form->addElement(new RMFormLabel(__('Download Item','dtransport'),$sw->getVar('name')));
	$form->addElement(new RMFormText(__('Log title','dtransport'),'title',50,100,$edit ? $log->title() : ''),true);
	$form->addElement(new RMFormEditor(__('Log content','dtransport'),'log','90%','350px',$edit ? $log->getVar('log','e') : ''),true);
	
	$form->addElement(new RMFormHidden('action',$edit ? 'saveedit' : 'save'));
	$form->addElement(new RMFormHidden('item',$item));
	$form->addElement(new RMFormHidden('id',$id));

	$buttons =new RMFormButtonGroup();
	$buttons->addButton('sbt',_SUBMIT,'submit');
	$buttons->addButton('cancel',_CANCEL,'button', 'onclick="window.location=\'logs.php?item='.$item.'\';"');

	$form->addElement($buttons);

	$form->display();

	xoops_cp_footer();
}


/**
* @desc Almacena los datos del log en la base de datos
**/
function dt_save_log($edit=0){
    global $xoopsSecurity;

    $query = '';
	foreach($_POST as $k=>$v){
		$$k=$v;
        if($k=='XOOPS_TOKEN_REQUEST'||$k='action') continue;
        $query = $query==''?$k.'='.urlencode($v):"&$k=".urlencode($v);
	}

    //Verificamos si el software es válido
    if ($item<=0)
        redirectMsg('items.php', __('Download item ID not provided!','dtransport'), RMMSG_WARN);

    //Verificamos si existe el software
    $sw = new Dtransport_Software($item);
    if ($sw->isNew())
        redirectMsg('items.php', __('Specified download item does not exists!','dtransport'), RMMSG_WARN);

	if (!$xoopsSecurity->check())
		redirectMsg('logs.php?item='.$item, __('Session token not valid!','dtransport'), RMMSG_ERROR);

	if ($edit){

        $action = 'action=edit';

		// Edición del registro
		if ($id<=0)
			redirectMsg('logs.php?item='.$item, __('Item log ID not provided!','dtransport'),1);

		$lg = new Dtransport_Log($id);
		if ($lg->isNew())
			redirectMsg('logs.php?item='.$item, __('Specified log does not exists!','dtransport'),1);

	}else{
        $action = 'action=new';
        $lg = new Dtransport_Log();
	}
	
	$lg->setSoftware($item);
	$lg->setTitle($title);
	$lg->setLog($log);
	$lg->setDate(time());

	if (!$lg->save())
		redirectMsg('logs.php?'.$query.'&'.$action, __('Item log could not be saved!','dtransport').'<br />'.$lg->error(), RMMSG_ERROR);
	else
		redirectMsg('logs.php?item='.$item, __('Database updated successfully!','dtransport'), RMMSG_SAVED);

}


/**
* @desc Elimina el Log especificado
**/
function dt_delete_log(){
	global $xoopsModule,$xoopsSecurity;

	$ids = rmc_server_var($_POST, 'ids', array());
    $item = rmc_server_var($_POST, 'item', 0);
	
	//Verificamos si el software es válido
    if ($item<=0)
        redirectMsg('items.php', __('Download item ID not provided!','dtransport'), RMMSG_WARN);

    //Verificamos si existe el software
    $sw = new Dtransport_Software($item);
    if ($sw->isNew())
        redirectMsg('items.php', __('Specified download item does not exists!','dtransport'), RMMSG_WARN);

	//Verificamos si nos proporcionaron algun log
	if (empty($ids))
		redirectMsg('logs.php?item='.$item, __('You must select at least one log!','dtransport'), RMMSG_ERROR);

	
    if (!$xoopsSecurity->check())
        redirectMsg('logs.php?item='.$item, __('Session token not valid!','dtransport'), RMMSG_ERROR);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
	$sql = "DELETE FROM ".$db->prefix("mod_dtransport_logs")." WHERE id_log IN(".implode(",",$ids).");";

    if($db->queryF($sql))
        redirectMsg('logs.php?item='.$item, __('Item logs deleted successfully!','dtransport'), RMMSG_SUCCESS);
    else
        redirectMsg('logs.php?item='.$item, __('Logs could not be deleted!','dtransport').'<br />'.$db->error(), RMMSG_ERROR);

}


$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action){
	case 'new':
		dt_form_logs();
	    break;
	case 'edit':
		dt_form_logs(1);
	    break;
	case 'save':
		dt_save_log();
	    break;
	case 'saveedit':
		dt_save_log(1);
	    break;
	case 'delete':
		dt_delete_log();
	    break;
	default:
		showLogs();
}

