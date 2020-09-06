<?php
// $Id: features.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCSUBLOCATION','features');
include ('header.php');
$common->location = 'items';

/**
* @desc Visualiza las caracteríticas existentes de un elemento especificado
**/
function dt_show_features(){
	global $common, $cuIcons;

	$item = $common->httpRequest()->request('item', 'integer', 0);

	$sw = new Dtransport_Software($item);
	
	if ($sw->isNew() && $item>0)
		redirectMsg('items.php', __('Specified download item does not exists!','dtransport'), RMMSG_WARN);

    $db = XoopsDatabaseFactory::getDatabaseConnection();

	$sql = "SELECT * FROM ".$db->prefix('mod_dtransport_features')." WHERE id_soft=$item";
	$result = $db->query($sql);

    $features = array();
    $tf = new RMTimeFormatter(0,"%T% %d%, %Y% - %h%:%i%");

	while ($rows=$db->fetchArray($result)){
		$ft = new Dtransport_Feature();
		$ft->assignVars($rows);

		$features[] = array(
            'id'=>$ft->id(),
            'title'=>$ft->title(),
            'created'=>$tf->format($ft->created()),
            'modified'=>$tf->format($ft->modified()),
            'software'=>$sw->getVar('name')
        );
	
	}

    Dtransport_Functions::itemsToolbar($sw);
    $common->template()->assign('xoops_pagetitle', sprintf(__('Features of "%s"','dtransport'), $sw->getVar('name')));

    // Styles
    $common->template()->add_style('admin.css','dtransport', ['id' => 'dtransport-css']);

    // scripts
    $common->template()->add_script('admin.min.js','dtransport', ['id' => 'admin-js', 'footer' => 1]);

    // Load editor scripts
    include_once RMCPATH . '/class/form.class.php';
    include_once DT_PATH . '/include/js-strings.php';
    $editor = new RMFormEditor('');
    $editor->render();

    $common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
    $common->breadcrumb()->add_crumb($sw->name, 'items.php?action=edit&amp;id=' . $sw->id());
	$common->breadcrumb()->add_crumb(__('Features', 'dtransport'));

	xoops_cp_header();

    include $common->template()->path('admin/dtrans-features.php','module','dtransport');

	xoops_cp_footer();

}

/**
* @desc Formulario de características
**/
function dt_form_features($edit=0){
	global $common;

    $common->ajax()->prepare();

    $common->checkToken();

    $id = $common->httpRequest()->get('id', 'integer', 0);
    $item = $common->httpRequest()->get('item', 'integer', 0);

	// Check that ID has been provided
	if ($item<=0){
        $common->ajax()->notifyError(__('Download item ID not provided!','dtransport'));
    }

	// Check that provided ID is valid
	$sw = new Dtransport_Software($item);
	if ($sw->isNew()){
        $common->ajax()->notifyError(__('Specified download item does not exists!','dtransport'));
    }

	if ($edit){

		if ($id<=0){
            $common->ajax()->notifyError(__('Feature ID not specified!','dtransport'));
        }

		// Check that feature exists
		$ft = new Dtransport_Feature($id);
		if ($ft->isNew()){
            $common->ajax()->notifyError(__('Specified feature does not exists!','dtransport'));
        }

	} else {
        $ft = new Dtransport_Feature();
    }

    $common->template()->assign('ft', $ft); // Feature object
    $common->template()->assign('sw', $sw); // Download item object

    $common->ajax()->response(
        $edit ? __('Edit Feature', 'dtrsnport') : __('Add Feature', 'dtransport'), 0, 1, [
            'content' => $common->template()->render('admin/dtrans-form-features.php', 'module', 'dtransport'),
            'openDialog' => 1,
            'icon' => 'svg-rmcommon-gear',
            'width' => 'medium',
            'windowId' => 'modal-features',
            'color' => 'orange'
        ]
    );

}

/**
* @desc Almacena la característica en la base de datos
**/
function dt_save_features($edit=0){
	global $common;

    $common->ajax()->prepare();

    $common->checkToken();

    $title = $common->httpRequest()->post('title', 'string', '');
    $nameId = $common->httpRequest()->post('nameid', 'string', '');
    $content = $common->httpRequest()->post('content', 'string', '');
    $id = $common->httpRequest()->post('id', 'integer', 0);
    $item = $common->httpRequest()->post('item', 'integer', 0);

    // Check if download item is valid
    if ($item<=0){
        $common->ajax()->notifyError(__('Download item ID not provided!','dtransport'));
    }

    // Check if download item exists
    $sw = new Dtransport_Software($item);
    if ($sw->isNew()){
        $common->ajax()->notifyError(__('Specified download item does not exists!','dtransport'));
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();

	if ($edit){

        if ($id<=0){
            $common->ajax()->notifyError(__('Feature ID not specified!','dtransport'));
        }

        // Check if feature exists
        $ft = new Dtransport_Feature($id);
        if ($ft->isNew()){
            $common->ajax()->notifyError(__('Specified feature does not exists!','dtransport'));
        }

	}else{

		$ft = new Dtransport_Feature();

	}

    $tc = TextCleaner::getInstance();

    if(trim($nameId)==''){
        $nameId = $tc->sweetstring($title);
    } else {
        $nameId = $tc->sweetstring($nameId);
    }

    //$returnString = 'title=' . urlencode($title) . '&nameid=' . urlencode($nameId) . '&content=' . urlencode($content) . '&item=' . $item . '&id=' . $id;

    // Check if a previous similar feature already exists
    $sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_features')." WHERE (title='$title' OR nameid='$nameId' AND id_feat!=".$ft->id()." AND id_soft=".$item;
    list($num) = $db->fetchRow($db->queryF($sql));
    if ($num>0){
        $common->ajax()->notifyError(__('Another feature with same title already exists!','dtransport'));
    }

	$ft->id_soft = $item;
	$ft->title = $title;
	$ft->content = $content;
	if (!$edit){
        $ft->created = time();
    }
	$ft->modified = time();
	$ft->nameid = $nameId;

	if ($ft->save()){

        $feature = [
            'id' => $ft->id(),
            'title' => $ft->title,
            'created' => $common->timeFormat('%T% %d%, %Y% - %h%:%i%')->format($ft->created),
            'modified' => $common->timeFormat('%T% %d%, %Y% - %h%:%i%')->format($ft->modified),
        ];

        $common->ajax()->response(
            __('Featured saved successfully!','dtransport'), 0, 1, [
                'closeWindow' => '#modal-features',
                'notify' => [
                    'type' => 'alert-success',
                    'icon' => 'svg-rmcommon-ok-circle'
                ],
                'feature' => $feature
            ]
        );
    } else {
        $common->ajax()->notifyError(sprintf(__('Feature could not be saved: %s','dtransport'), $ft->errors));
    }

}

/**
* @desc Elimina la característica especificada de la base de datos
**/
function deleteFeatures(){
	global $common;

    $common->ajax()->prepare();
    $common->checkToken();

	$ids = $common->httpRequest()->post('ids', 'array',[]);
	$item = $common->httpRequest()->post('item', 'integer', 0);

	
	// Check
	if ($item<=0){
        $common->ajax()->notifyError(__('Download item not specified!', 'dtransport'));
	}

	//Verificamos que el software exista
	$sw=new Dtransport_Software($item);
	if ($sw->isNew()){
        $common->ajax()->notifyError(__('Specified download item does not exists!', 'dtransport'));
	}

	//Verificamos si nos proporcionaron alguna caracteristica
	if (empty($ids)){
        $common->ajax()->notifyError(__('You must specified at least one feature to delete!', 'dtransport'));
	}

    $errors = [];
    $features = [];

    foreach ($ids as $k){

        //Verificamos si la característica es válida
        if ($k<=0){
            continue;
        }

        //Verificamos si la caracteristica existe
        $ft=new Dtransport_Feature($k);
        if ($ft->isNew()){
            continue;
        }

        if (!$ft->delete()){
            $errors[] = sprintf(__('Feature %s could not be deleted: %s', 'dtransport'), $ft->title, $ft->errors());
        } else {
            $features[] = $k;
        }

    }

    if (empty($errors)){
        $common->ajax()->response(
            __('Features has been deleted successfully!', 'dtransport'), 0, 1, [
                'notify' => [
                    'type' => 'alert-success',
                    'icon' => 'svg-rmcommon-ok-circle'
                ],
                'ids' => $features
            ]
        );
    }else{
        $common->ajax()->response(
            sprintf(__('Some errors occurs whiel trygin to delete features: %s', 'dtransport'), implode(" ", $errors)), 1, 1, [
                'notify' => [
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-error'
                ],
                'ids' => $features
            ]
        );
    }

}


/**
* @desc Cambia a nueva una característica
**/
function newFeatures(){

	$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$item = isset($_REQUEST['item']) ? intval($_REQUEST['item']) : 0;
	
	//Verificamos si se proporcionó una caracteristica
	if (!is_array($ids) || empty($ids)){
		redirectMsg('./features.php?item='.$item,_AS_DT_ERRFEAT,1);
		die();
	}

	$errors='';
	foreach ($ids as $k){

		//Verificamos si la característica es válida
		if ($k<=0){
			$errors.=sprintf(_AS_DT_ERRFEATVAL,$k);
			continue;
		}

		//Verificamos si la caracteristica existe
		$ft=new Dtransport_Feature($k);
		if ($ft->isNew()){
			$errors.=sprintf(_AS_DT_ERRFEATEX,$k);
			continue;			
		}

		$ft->setShowNew(!$ft->showNew());
		$ft->setModified(time());	
		
		if (!$ft->save()){
			$errors.=sprintf(_AS_DT_ERRFEATSAVE,$k);
		}
	
	}

	if ($errors!=''){
		redirectMsg('./features.php?item='.$item,_AS_DT_ERRORS.$errors,1);
		die();
	}else{
		redirectMsg('./features.php?item='.$item,_AS_DT_DBOK,0);
		die();
	}


}



$action = $common->httpRequest()->request('action', 'string', '');

switch ($action){
	case 'new':
		dt_form_features();
	    break;
	case 'edit':
		dt_form_features(1);
	    break;
	case 'save':
		dt_save_features();
	    break;
	case 'save-edited':
		dt_save_features(1);
	    break;
	case 'delete':
		deleteFeatures();
	break;
	case 'newfeat':
		newFeatures();
	break;
	default:
		dt_show_features();
}

