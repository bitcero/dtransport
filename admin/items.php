<?php
/**
 * D-Transport: Downloads Manager
 *
 * Copyright © 2015 Red Mexico http://www.eduardocortes.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Red Mexico http://www.eduardocortes.mx
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 * @url          http://www.eduardocortes.mx
 */

include('header.php');

$common->location = 'items';

/**
 * @desc Muestra todos lo elementos registrados
 **/
function dt_show_items()
{
    define('RMCSUBLOCATION', 'downitems');
    global $xoopsModule, $xoopsSecurity, $common;

    $search = rmc_server_var($_REQUEST, 'search', '');
    $sort = rmc_server_var($_REQUEST, 'sort', 'id_soft');
    $mode = rmc_server_var($_REQUEST, 'mode', 1);
    $sort = $sort == '' ? 'id_soft' : $sort;
    $catid = rmc_server_var($_REQUEST, 'cat', 0);
    $type = rmc_server_var($_REQUEST, 'type', '');

    //Barra de Navegación
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $tsof = $db->prefix("mod_dtransport_items");
    $tedited = $db->prefix("mod_dtransport_edited");

    // Prepare SQL query
    if ($catid > 0) {
        $trel = $db->prefix("mod_dtransport_catitem");

        $presql = "SELECT COUNT(*) FROM $tsof as s, $trel as c";
        // "SELECT COUNT(*) FROM $tsof as s, $trel as c" . ($type != 'edit' ? ", $tedited as edited" : '') . "
        $sql = " WHERE c.cat=$catid AND s.id_soft=c.soft";
        $sqlJoin = ' AND ';

    } else {

        $presql = "SELECT COUNT(*) FROM $tsof as s";
        //$sql = "SELECT COUNT(*) FROM $tsof as s" . ($type != 'edit' ? ", $tedited as edited" : '');
        $sql = '';
        $sqlJoin = ' WHERE ';

    }

    switch($type){
        case 'wait':
            $sql .= $sqlJoin . 's.approved=0';
            break;
        case 'edit':
            $sql .= $sqlJoin . 's.status="verify"';
            break;
        case 'delete':
            $sql .= $sqlJoin . 's.deletion=1';
    }

    $sql1 = '';
    $search = trim($search);

    if ($search != '') {

        $sql1 .= ($sql1 == '' ? ($catid > 0 || $type == 'wait' ? " AND " : " WHERE ") : " OR ") . " name LIKE '%$search%' ";

    }

    $sql2 = " ORDER BY s.$sort " . ($mode ? "DESC" : "ASC");

    //$sql .= $sql1.$sql2;

    list($num) = $db->fetchRow($db->queryF($presql . $sql . $sql1 . $sql2));

    $page = rmc_server_var($_REQUEST, 'page', 1);
    $limit = 15;

    $nav = new RMPageNav($num, $limit, $page);
    $nav->target_url("items.php?search=$search&amp;sort=$sort&amp;mode=$mode&amp;cat=$catid&amp;type=$type&page={PAGE_NUM}");
    $navpage = $nav->render(false, true);
    $start = $nav->start();

    //Fin de barra de navegación
    $catego = new Dtransport_Category($catid);

    if ($type != 'edit') {
        //$sql1 .= ($catid > 0 || $type == 'wait' ? ' AND ' : ' WHERE ') . ' edited.id_soft = s.id_soft ';
        $sql = "SELECT s.*, edited.id_item as inEdition FROM $tsof as s LEFT JOIN $tedited as edited ON s.id_soft = edited.id_soft " . ($catid > 0 ? ",  $trel as c" : '') . $sql . $sql1 . $sql2;
    } else {
        $sql = "SELECT s.* FROM $tsof as s " . ($catid > 0 ? ",  $trel as c" : '') . $sql . $sql1 . $sql2;
    }

    //$sql = "SELECT s.*, edited.id_soft as inEdition FROM $tsof as s, $tedited as edited " . $sql . $sql1 . $sql2;

    //$sql = str_replace("COUNT(*)", "s.*" . ($type == 'edit' ? ' edited.id_item' : ''), $sql);

    $sql .= " LIMIT $start,$limit";
    $result = $db->queryF($sql);
    $items = array();

    $timeFormat = new RMTimeFormatter(0, '%m%-%d%-%Y%');

    while ($rows = $db->fetchArray($result)) {

        $sw = new Dtransport_Software();
        $sw->assignVars($rows);
        $img = new RMImage($sw->getVar('image'));
        $user = new XoopsUser($sw->getVar('uid'));

        $items[] = [
            'id' => ($type == 'edit' ? $sw->id_soft : $sw->id()),
            'inEdition' => $rows['inEdition'] > 0 ? true : false,
            'status' => $rows['status'],
            'name' => $sw->getVar('name'),
            'screens' => $sw->getVar('screens'),
            'image' => $img->get_smallest(),
            'secure' => $sw->getVar('secure'),
            'approved' => $sw->getVar('approved'),
            'user' => [
                'uname' => $user->getVar('uname'),
                'uid' => $user->getVar('uid')
            ],
            'created' => $timeFormat->format($sw->getVar('created')),
            'modified' => $timeFormat->format($sw->getVar('modified')),
            'link' => $sw->permalink(),
            'featured' => $sw->getVar('featured'),
            'daily' => $sw->getVar('daily'),
            'password' => $sw->getVar('password') != '',
            'deletion' => $sw->getVar('delete'),
            'hits' => $sw->getVar('hits'),
            'desc' => $sw->getVar('shortdesc'),
            'version' => $sw->getVar('version'),
            'votes' => $sw->getVar('votes'),
            'comments' => $sw->getVar('comments'),
            'rating' => $sw->getVar('rating'),
            'deletion' => $sw->deletion
        ];
    }

    //Lista de categorías
    $categories = array();
    Dtransport_Functions::getCategories($categos, 0, 0, array(), true);
    foreach ($categos as $k) {
        $cat = $k['object'];
        $categories[] = array('id' => $cat->id(), 'name' => str_repeat('--', $k['jumps']) . ' ' . $cat->name());
    }

    switch ($type) {
        case 'wait':
            $loc = __('Pending Downloads', 'dtransport');
            break;
        case 'edit':
            $loc = __('Edited Downloads', 'dtransport');
            break;
        default:
            $loc = __('Downloads Management', 'dtransport');
            break;
    }

    $tpl = RMTemplate::get();
    $tpl->add_style('cp.min.css', 'dtransport');
    $tpl->add_script('admin.min.js', 'dtransport');
    $tpl->add_script('items.min.js', 'dtransport');

    include DT_PATH . '/include/js-strings.php';

    $bc = RMBreadCrumb::get();
    $bc->add_crumb($loc);

    xoops_cp_header();

    include RMTemplate::get()->get_template('admin/dtrans-items.php', 'module', 'dtransport');

    xoops_cp_footer();

}


/**
 * @desc Formulario de Elementos
 **/
function formItems($edit = 0)
{
    global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $rmc_config, $xoopsSecurity, $functions, $cuIcons, $common;

    define('RMCSUBLOCATION', 'newitem');

    // Get layout data
    $id = RMHttpRequest::get('id', 'integer', 0);
    $page = RMHttpRequest::get('page', 'integer', 0);
    $search = rmc_server_var($_REQUEST, 'search', '');
    $sort = rmc_server_var($_REQUEST, 'sort', 'id_soft');
    $mode = intval(rmc_server_var($_REQUEST, 'mode', 0));
    $catid = intval(rmc_server_var($_REQUEST, 'car', 0));
    $type = rmc_server_var($_REQUEST, 'type', '');

    $ev = RMEvents::get();

    $params = '?page=' . $page . '&search=' . $search . '&sort=' . $sort . '&mode=' . $mode . '&cat=' . $catid . '&type=' . $type;

    if ($edit) {
        //Verificamos que el software sea válido
        if ($id <= 0) {
            redirectMsg('items.php' . $params, __('Download item has not been specified!', 'dtransport'), 1);
            die();
        }

        $sw = new Dtransport_Software($id);

        if ($sw->isNew()) {
            $common->uris()::redirect_with_message(
                __('Download item does not exists!', 'dtransport'),
                'items.php', RMMSG_ERROR
            );
        }

        // Check if there are an item in edition
        if ('verify' == $sw->status) {
            $edited = new Dtransport_SoftwareEdited($sw->id(), 'item');

            if(false == $edited->isNew()){

                $sw->setVars($edited->data);

                $sw->setCategories($edited->data['categories']);
                $sw->setLicences($edited->data['licenses']);
                $sw->setPlatforms($edited->data['platforms']);
                $sw->setTags($edited->tagsData());

            }

        }

    } else {
        $sw = new Dtransport_Software();
        $location = __('New Download Item', 'dtransport');
    }

    $form = new RMForm('', '', '');
    $ed = new RMFormEditor('', 'desc', '99%', '300px', $edit ? $sw->getVar('description', $rmc_config['editor_type'] != 'tiny' ? 'e' : 's') : '', $rmc_config['editor_type']);
    $ed->addClass('required');

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    //Lista de categorías
    $categos = array();
    $swcats = $sw->categories();
    Dtransport_Functions::getCategories($categos, 0, 0, array(), false);
    foreach ($categos as $row) {
        $cat = new Dtransport_Category();
        $cat->assignVars($row);
        $categories[] = array(
            'id' => $cat->id(),
            'name' => $cat->name(),
            'parent' => $cat->parent(),
            'active' => $cat->active(),
            'description' => $cat->desc(),
            'indent' => $row['jumps'],
            'selected' => $edit ? in_array($cat->id(), $swcats) : ''
        );
    }
    unset($categos);

    // Licencias
    $sql = "SELECT * FROM " . $db->prefix('mod_dtransport_licences');
    $result = $db->queryF($sql);
    $lics = array();
    $lics[] = array(
        'id' => 0,
        'name' => __('Other license', 'dtransport'),
        'selected' => !$edit || in_array(0, $sw->licences()) ? 1 : 0
    );
    while ($row = $db->fetchArray($result)) {
        $lic = new Dtransport_License();
        $lic->assignVars($row);
        $lics[] = array(
            'id' => $lic->id(),
            'name' => $lic->name(),
            'selected' => $edit ? in_array($lic->id(), $sw->licences()) : ''
        );
    }
    unset($lic);

    // Plataformas
    $sql = "SELECT * FROM " . $db->prefix('mod_dtransport_platforms');
    $result = $db->queryF($sql);
    $oss = array();
    $oss[] = array(
        'id' => 0,
        'name' => __('Other platform', 'dtransport'),
        'selected' => !$edit || in_array(0, $sw->platforms()) ? 1 : 0
    );
    while ($row = $db->fetchArray($result)) {
        $os = new Dtransport_Platform();
        $os->assignVars($row);
        $oss[] = array(
            'id' => $os->id(),
            'name' => $os->name(),
            'selected' => $edit ? in_array($os->id(), $sw->platforms()) : ''
        );
    }
    unset($os);

    // Allowed groups
    $field = new RMFormGroups('', 'groups', 1, 1, 1, $edit ? $sw->getVar('groups') : array(1, 2));
    $groups = $field->render();

    // Tags
    if(!$edited || $edited->isNew()){
        $ftags = $sw->tags(true, false);
        $tags = array();
        foreach ($ftags as $tag) {
            $tags[] = $tag->getVar('tag');
        }
        unset($ftags);
    } elseif($edited) {
        $tags = $edited->tagsData();
    }


    $common->template()->add_style('cp.min.css', 'dtransport', ['id' => 'dtransport-css']);
    $common->template()->add_script('itemsform.min.js', 'dtransport', ['footer' => 1, 'id' => 'form-js']);
    $common->template()->add_script('jquery.validate.min.js', 'rmcommon', ['id' => 'validate-js', 'footer' => 1]);
    $common->template()->add_body_class('dt-body-form');

    include DT_PATH . '/include/js-strings.php';

    $bc = RMBreadCrumb::get();
    $bc->add_crumb(__('Downloads management', 'dtransport'), 'items.php');
    $bc->add_crumb($edit ? __('Editing item', 'dtransport') : __('New item', 'dtransport'));

    // Toolbar for editing elements
    if (false == $sw->isNew()) {

        Dtransport_Functions::itemsToolbar($sw);

    }

    // Featured download
    $field = new RMFormYesNo('', 'mark', $edit ? $sw->getVar('featured') : 1);
    $featured = $field->render();

    // Descarga segura
    $field = new RMFormYesno('', 'secure', $edit ? $sw->getVar('secure') : 0);
    $secure = $field->render();

    // Approved
    $field = new RMFormYesNo('', 'approved', $edit ? $sw->getVar('approved') : 1);
    $approved = $field->render();

    /**
     * Additional fields for items
     * Third elements can intercept this event and return new fields
     */
    Dtransport_Functions::$additionalFields = $common->events()->trigger('dtransport.item.form.fields', Dtransport_Functions::$additionalFields, $sw);

    xoops_cp_header();
    include RMTemplate::get()->get_template('admin/dtrans-form-items.php', 'module', 'dtransport');

    xoops_cp_footer();

}

/**
 * desc Elimina de la base de datos los elementos
 **/
function dt_delete_items()
{
    global $xoopsModuleConfig, $xoopsConfig, $xoopsModule, $xoopsSecurity, $rmc_config, $xoopsUser, $common;

    $ids = rmc_server_var($_POST, 'ids', array());
    $page = rmc_server_var($_POST, 'page', 1);
    $search = rmc_server_var($_POST, 'search', '');
    $sort = rmc_server_var($_POST, 'sort', 'id_soft');
    $mode = rmc_server_var($_POST, 'mode', 1);
    $cat = rmc_server_var($_POST, 'cat', 0);
    $type = rmc_server_var($_POST, 'type', '');

    $params = '?pag=' . $page . '&search=' . $search . '&sort=' . $sort . '&mode=' . $mode . '&cat=' . $cat . '&type=' . $type;

    //Verificamos que el software sea válido
    if (!is_array($ids) && $ids <= 0)
        redirectMsg('items.php' . $params, __('You must select at least one download item to delete!', 'dtransport'), RMMSG_WARN);

    if (!is_array($ids))
        $ids = array($ids);

    if (!$xoopsSecurity->check())
        redirectMsg('items.php' . $params, __('Session token expired!', 'dtransport'), RMMSG_ERROR);

    $errors = '';

    $mailer = new RMMailer('text/html');
    $etpl = DT_PATH . '/templates/mail/' . $rmc_config['lang'] . '/deletion.php';
    if (!file_exists($etpl))
        $etpl = DT_PATH . '/templates/mail/'  . $rmc_config['lang'] . '/deletion_en.php';

    $mailer->template($etpl);
    $mailer->assign('siteurl', XOOPS_URL);
    $mailer->assign('dturl', $xoopsModuleConfig['permalinks'] ? XOOPS_URL . '/' . trim($xoopsModuleConfig['htbase'], '/') : DT_URL);
    $mailer->assign('downcp', $xoopsModuleConfig['permalinks'] ? XOOPS_URL . '/' . trim($xoopsModuleConfig['htbase'], '/') . '/cp/' : DT_URL . '/?p=cpanel');
    $mailer->assign('dtname', $xoopsModule->name());
    $mailer->assign('sitename', $xoopsConfig['sitename']);

    foreach ($ids as $id) {
        $sw = new Dtransport_Software($id);

        if ($sw->isNew()) continue;

        $common->events()->trigger('dtransport.before.delete.item', $sw);

        if (!$sw->delete()) {
            $errors .= $sw->errors();
            $common->events()->trigger('dtransport.error.delete.item', $sw);
            continue;
        }

        $common->events()->trigger('dtransport.item.deleted', $sw);

        $xu = new XoopsUser($sw->getVar('uid'));
        $mailer->add_to($xu->getVar('email'));
        $mailer->assign('uname', $xu->name() != '' ? $xu->name() : $xu->uname());
        $mailer->assign('download', $sw->getVar('name'));
        $mailer->assign('email', $xu->getVar('email'));
        $mailer->assign('method', $xu->getVar('notify_method'));
        $mailer->set_subject(sprintf(__('Your download %s has been deleted!', 'dtransport'), $sw->getVar('name')));
        if ($xu->getVar('notify_method') == 1) {
            $mailer->set_from_xuser($xoopsUser);
            $mailer->send_pm();
        } else
            $mailer->send();
    }

    if ($errors != '')
        redirectMsg('items.php' . $params, __('Errors ocurred while trying to delete selected downloads!', 'dtransport') . '<br />' . $errors, RMMSG_ERROR);

    redirectMsg('items.php' . $params, __('Downloads deleted successfully!', 'dtransport'), RMMSG_SUCCESS);
}

/**
 * @desc Permite aprobar o no un elemento
 **/
function dt_change_status($data, $value = 0)
{
    global $xoopsSecurity;

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $ids = rmc_server_var($_POST, 'ids', array());
    $page = rmc_server_var($_POST, 'page', 1);
    $limit = rmc_server_var($_POST, 'limit', 15);
    $search = rmc_server_var($_POST, 'search', '');
    $sort = rmc_server_var($_POST, 'sort', 'id_soft');
    $mode = rmc_server_var($_POST, 'mode', 0);
    $cat = rmc_server_var($_POST, 'cat', 0);

    $params = 'page=' . $page . '&limit=' . $limit . '&search=' . $search . '&sort=' . $sort . '&mode=' . $mode . '&cat=' . $cat;

    if (!$xoopsSecurity->check()) {
        redirectMsg('./items.php?' . $params, __('Session token expired!', 'dtransport'), RMMSG_ERROR);
        die();
    }

    //Verificamos si se proporciono algún elemento
    if (!is_array($ids) || empty($ids)) {
        redirectMsg('./items.php?' . $params, __('You must select at least one item to modify!', 'dtransport'), RMMSG_WARN);
        die();
    }

    $sql = "UPDATE " . $db->prefix("mod_dtransport_items") . " SET $data=$value WHERE id_soft IN (" . implode(",", $ids) . ")";

    if (!$db->queryF($sql)) {
        redirectMsg('./items.php?' . $params, __('Errors ocurred while trying to update database!', 'dtransport') . '<br />' . $errors, RMMSG_ERROR);
        die();
    } else {
        redirectMsg('./items.php?' . $params, __('Database updated successfully!', 'dtransport'), RMMSG_SUCCESS);
        die();
    }

}

$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action) {
    case 'new':
        formItems();
        break;
    case 'edit':
        formItems(1);
        break;
    case 'delete':
        dt_delete_items();
        break;
    case 'bulk_approve':
        dt_change_status('approve', 1);
        break;
    case 'bulk_unapproved':
        dt_change_status('approve', 0);
        break;
    case 'bulk_featured':
        dt_change_status('featured', 1);
        break;
    case 'bulk_unfeatured':
        dt_change_status('featured', 0);
        break;
    case 'bulk_daily':
        dt_change_status('daily', 1);
        break;
    case 'bulk_undaily':
        dt_change_status('daily', 0);
        break;
    case 'bulk_secure':
        dt_change_status('secure', 1);
        break;
    case 'bulk_nosecure':
        dt_change_status('secure', 0);
        break;
    default:
        dt_show_items();
        break;
}
