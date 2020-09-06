<?php
// $Id: screens.php 209 2013-01-29 04:03:49Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCSUBLOCATION', 'screens');
include('header.php');
$common->location = 'items';

/**
 * @des Visualiza todas las pantallas existentes
 **/
function showScreens()
{
    global $xoopsModule,
           $xoopsSecurity,
           $tpl,
           $functions,
           $xoopsModule,
           $xoopsModuleConfig,
           $xoopsUser,
           $xoopsConfig,
           $common, $cuIcons;

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $tc = TextCleaner::getInstance();

    $item = RMHttpRequest::request('item', 'integer', 0);

    if ($item <= 0)
        redirectMsg('items.php', __('Download item ID not provided!', 'dtransport'), RMMSG_WARN);


    $sw = new Dtransport_Software($item);

    $sql = "SELECT * FROM " . $db->prefix('mod_dtransport_screens') . " WHERE id_soft=$item";
    $result = $db->queryF($sql);

    while ($rows = $db->fetchArray($result)) {
        $sc = new Dtransport_Screenshot();
        $sc->assignVars($rows);

        $screens[] = array(
            'id' => $sc->id(),
            'title' => $sc->title(),
            'desc' => substr($tc->clean_disabled_tags($sc->desc()), 0, 80) . "...",
            'image' => $sc->image
        );


    }

    // CSS Styles
    $tpl->add_style('admin.css', 'dtransport');
    $tpl->add_style('screens.css', 'dtransport');
    $tpl->add_style('uploadify.css', 'rmcommon');

    // Javascripts
    $tpl->add_script('screens.min.js', 'dtransport', ['id' => 'screens-js', 'footer' => 1]);

    Dtransport_Functions::itemsToolbar($sw);

    $tc = TextCleaner::getInstance();
    $rmf = RMFunctions::get();

    $common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
    $common->breadcrumb()->add_crumb($sw->name, 'items.php?action=edit&id=' . $sw->id());
    $common->breadcrumb()->add_crumb(__('Screenshots', 'dtransport'));

    $tpl->assign('xoops_pagetitle', sprintf(__("%s Screenshots", 'dtransport'), $sw->getVar('name')));
    include DT_PATH . '/include/js-strings.php';

    xoops_cp_header();

    include $tpl->get_template('admin/dtrans-screens.php', 'module', 'dtransport');

    xoops_cp_footer();

}

/**
 * @desc Formulario de Pantallass
 **/
function formScreens($edit = 0)
{
    global $xoopsModule, $xoopsConfig, $db, $xoopsModuleConfig;

    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $item = isset($_REQUEST['item']) ? intval($_REQUEST['item']) : 0;

    //Verificamos que el software sea válido
    if ($item <= 0) {
        redirectMsg('./screens.php', _AS_DT_ERR_ITEMVALID, 1);
        die();
    }
    //Verificamos que el software exista
    $sw = new Dtransport_Software($item);
    if ($sw->isNew()) {
        redirectMsg('./screens.php', _AS_DT_ERR_ITEMEXIST, 1);
        die();
    }

    //Verificamos el limite de pantallas a almacenar
    if ($xoopsModuleConfig['limit_screen'] <= $sw->getVar('screens')) {
        redirectMsg('./screens.php?item=' . $item, _AS_DT_ERRCOUNT, 1);
        die();

    }

    if ($edit) {

        //Verificamos si pantalla es válida
        if ($id <= 0) {
            redirectMsg('./screens.php?item=' . $item, _AS_DT_ERR_SCVALID, 1);//
            die();
        }

        //Verificamos que la pantalla exista
        $sc = new Dtransport_Screenshot($id);
        if ($sc->isNew()) {
            redirectMsg('./screens.php?item=' . $item, _AS_DT_ERR_SCEXIST, 1);
            die();
        }

    }


    xoops_cp_location("<a href='./'>" . $xoopsModule->name() . "</a> &raquo; <a href='./items.php'>" . _AS_DT_SW . "</a> &raquo; " . ($edit ? _AS_DT_EDITSCREEN : _AS_DT_NEWSCREEN));
    xoops_cp_header();

    $form = new RMForm($edit ? sprintf(_AS_DT_EDITSCREENS, $sw->getVar('name')) : sprintf(_AS_DT_NEWSCREENS, $sw->getVar('name')), 'frmscreen', 'screens.php');
    $form->setExtra("enctype='multipart/form-data'");

    $form->addElement(new RMFormLabel(_AS_DT_ITEM, $sw->getVar('name')));


    $form->addElement(new RMFormText(_AS_DT_TITLE, 'title', 50, 100, $edit ? $sc->title() : ''), true);
    $form->addElement(new RMFormEditor(_AS_DT_DESC, 'desc', '100%', '100px', $edit ? $sc->desc() : '', 'textarea'));
    $form->addElement(new RMFormFile(_AS_DT_IMAGE, 'image', 45, $xoopsModuleConfig['image'] * 1024), $edit ? '' : true);

    if ($edit) {
        $img = "<img src='" . XOOPS_URL . "/uploads/dtransport/ths/" . $sc->image() . "' border='0' />";
        $form->addElement(new RMFormLabel(_AS_DT_IMAGEACT, $img));
    }

    $form->addElement(new RMFormHidden('op', $edit ? 'saveedit' : 'save'));
    $form->addElement(new RMFormHidden('id', $id));
    $form->addElement(new RMFormHidden('item', $item));
    $buttons = new RMFormButtonGroup();
    $buttons->addButton('sbt', _SUBMIT, 'submit');
    $buttons->addButton('cancel', _CANCEL, 'button', 'onclick="window.location=\'screens.php?item=' . $item . '\';"');

    $form->addElement($buttons);

    $form->display();


    xoops_cp_footer();
}


/**
 * Saves screenshots data
 * @param int $edit
 */
function saveScreens()
{
    global $db, $common;

    $item = $common->httpRequest()->post('item', 'integer', 0);
    $images = $common->httpRequest()->post('images', 'array', []);

    $common->ajax()->prepare();

    $common->checkToken();

    //Verificamos que el software sea válido
    if ($item <= 0) {
        $common->ajax()->notifyError(__('You must specify an item ID', 'dtransport'));
        die();
    }
    //Verificamos que el software exista
    $sw = new Dtransport_Software($item);
    if ($sw->isNew()) {
        $common->ajax()->notifyError(__('Specified item does not exists!', 'dtransport'));
        die();
    }

    $settings = $common->settings()->module_settings('dtransport');
    //Verificamos el limite de pantallas a almacenar
    if ($settings->limit_screen <= $sw->screens) {
        $common->ajax()->notifyError(__('You have reached the maximum number of screenshots allowed for this item!', 'dtransport'));
        die();

    }

    $errors = [];
    $done = [];

    // Iterate over images array
    foreach($images as $image){
        $sc = new Dtransport_Screenshot();
        $sc->title = $image['title'];
        $sc->desc = $image['description'];
        $sc->image = $image['link'];
        $sc->hits = 0;
        $sc->date = time();
        $sc->id_soft = $sw->id();

        if(false == $sc->save()){
            $errors[] = $sc->errors();
        } else {
            $image['id'] = $sc->id();
            $image['thumbnail'] = $common->resize()->resize($image['link'], ['width' => 200, 'height' => 200])->url;
            $done[] = $image;
        }
    }

    // If process fails...
    if(false == empty($errors)){
        $common->ajax()->response(
            sprintf(__('Errors occurs while trying to save images: %s', 'dtransport'), implode(' ', $errors)), 0, 1, [
                'notify' => [
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-error'
                ],
                'images' => $done
            ]
        );
    }

    // All is done!
    $common->ajax()->response(
        __('Images saved successfully', 'dtransport'), 0, 1, [
            'notify' => [
                'type' => 'alert-success',
                'icon' => 'svg-rmcommon-ok-circle'
            ],
            'images' => $done
        ]
    );

}

/**
 * @desc Elimina pantallas de la base de datos
 **/
function deleteScreens()
{
    global $common;


    $common->ajax()->prepare();

    $ids = $common->httpRequest()->post('ids', 'array', []);
    $item = $common->httpRequest()->post('item', 'integer', 0);

    $common->checkToken();

    //Verificamos que el software sea válido
    if ($item <= 0) {
        $common->ajax()->notifyError(__('No download item has been specified!', 'dtransport'));
    }
    //Verificamos que el software exista
    $sw = new Dtransport_Software($item);
    if ($sw->isNew()) {
        $common->ajax()->notifyError(__('Specified item does not exists!', 'dtransport'));
    }


    //Verificamos si nos proporcionaron alguna pantalla
    if (empty($ids)) {
        $common->ajax()->notifyError(__('No image has been specified!', 'dtransport'));
    }

    $errors = [];
    $images = [];
    foreach ($ids as $k) {

        //Verificamos que la pantalla exista
        $sc = new Dtransport_Screenshot($k);
        if ($sc->isNew()) {
            continue;
        }

        if (!$sc->delete()) {
            $errors[] = $sc->errors();
        } else {
            $images[] = $k;
        }

    }

    if (false == empty($errors)) {
        $common->ajax()->response(
            sprintf(__('Some errors occurs while trying to delete images: %s', 'dtransport'), implode(" ", $errors)), 0, 1, [
                'notify' => [
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-alert'
                ],
                'images' => $images
            ]
        );
    } else {
        $common->ajax()->response(
            __('Images deleted successfully', 'dtransport'), 0, 1, [
                'notify' => [
                    'type' => 'alert-success',
                    'icon' => 'svg-rmcommon-ok-circle'
                ],
                'images' => $images
            ]
        );
    }

}

/**
 * Load image information
 */
function dt_get_information(){
    global $common;

    $common->ajax()->prepare();

    $func = new Dtransport_Functions();

    $common->checkToken();

    $id = rmc_server_var($_GET, 'id', 0);
    $sc = new Dtransport_Screenshot($id);
    if($sc->isNew()){
        $common->ajax()->notifyError(__('Specified image does not exists!', 'dtransport'));
    }

    $ret = array(
        'title' => $sc->title(),
        'description' => $sc->desc(),
        'id' => $sc->id()
    );

    $common->ajax()->response(
        __('Image information', 'dtransport'), 0, 1, $ret
    );

}

function dt_save_screen_info(){
    global $common;

    $common->ajax()->prepare();

    $func = new Dtransport_Functions();

    $common->checkToken();

    $id = $common->httpRequest()->post('id', 'integer', 0);
    $title = $common->httpRequest()->post('title', 'string', '');
    $desc = $common->httpRequest()->post('desc', 'string', '');

    $sc = new Dtransport_Screenshot($id);
    if($sc->isNew()){
        $common->ajax()->notifyError(__('Specified screenshot does not exists!','dtransport'));
    }

    if($title==''){
        $common->ajax()->notifyError(__('You must provide a title for this screenshot!','dtransport'));
    }

    $sc->setTitle($title);
    $sc->setDesc($desc);

    if(false == $sc->save()){
        $common->ajax()->notifyError(__('Screenshot changes could not be saved!','dtransport').'<br />'.$sc->errors());
    }

    $common->ajax()->response(
        __('Information saved successfully!', 'dtransport'), 0, 1, [
            'notify' => [
                'type' => 'alert-success',
                'icon' => 'svg-rmcommon-ok-circle'
            ],
            'title' => $sc->title,
            'description' => $sc->desc,
            'id' => $sc->id()
        ]
    );

}


$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';
switch ($op) {
    case 'new':
        formScreens();
        break;
    case 'edit':
        formScreens(1);
        break;
    case 'save':
        saveScreens();
        break;
    case 'delete-screen':
        deleteScreens();
        break;
    case 'image-info':
        dt_get_information();
        break;
    case 'save-screen-data':
        dt_save_screen_info();
        break;
    default:
        showScreens();

}
