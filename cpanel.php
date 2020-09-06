<?php
// $Id: cpanel.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!$xoopsUser)
    redirect_header(XOOPS_URL . '/user.php?xoops_redirect=' . urlencode(str_replace(XOOPS_URL, '', DT_URL) . ($dtSettings->permalinks ? '/cp/' : '/?s=cp')), 1, __('Please login before to access this page!', 'dtransport'));

if (!$dtSettings->send_download) {
    header('Location: ' . DT_URL);
    die();
}

if ($action != '' && 'pending' != $action) {

    //Verificamos si el elemento es válido
    if ($id == '' && $id <= 0)
        redirect_header(DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?s=cp'), 2, __('Item not found. Please try again!', 'dtransport'));

    //Verificamos si el elemento existe
    $item = new Dtransport_Software($id);
    if ($item->isNew())
        redirect_header(DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?s=cp'), 2, __('Item not found. Please try again!', 'dtransport'));

    if ($item->getVar('uid') != $xoopsUser->uid())
        redirect_header(DT_URL, 1, __('You can not edit this download item!', 'dtransport'));

}

// Add JS
$common->template()->add_script('main.min.js', 'dtransport', ['id' => 'dtransport-js', 'footer' => 1]);
$common->template()->add_inline_script('dtApp.url = "' . DT_URL . '"; dtApp.permalinks = ' . ($dtSettings->permalinks ? 'true' : 'false') . '; dtApp.init();', 1);

switch ($action) {

    case 'screens':
        $op = '';

        if($dtSettings->permalinks){
            if (count($params) >= 4) {
                $params = array_slice($params, 3);
                $op = $params[0];
                $screen = $params[1];
            }
        } else {
            $screen = $common->httpRequest()->request('screen', 'integer', 0);
            $op = $common->httpRequest()->request('op', 'string', '');
        }

        require 'include/cpanel/screens.php';

        switch ($op) {
            case 'save':
                dt_save_screens($screen > 0 ? 1 : 0);
                break;
            case 'delete':
                dt_delete_screen();
                break;
            default:
                dt_screens($op == 'edit' && $screen > 0 ? 1 : 0);
                break;
        }
        break;

    case 'features':

        $op = '';

        if($dtSettings->permalinks){
            if (count($params) >= 4) {
                $params = array_slice($params, 3);
                $op = $params[0];
                $feature = $params[1];
            }
        } else {
            $feature = $common->httpRequest()->request('feature', 'integer', 0);
            $op = $common->httpRequest()->request('op', 'string', '');
        }

        require 'include/cpanel/features.php';

        switch ($op) {
            case 'save':
                dt_save_feature($feature > 0 ? 1 : 0);
                break;
            case 'delete':
                dt_delete_feature();
                break;
            default:
                dt_show_features($op == 'edit' && $feature > 0 ? 1 : 0);
                break;
        }

        break;

    case 'logs':

        $op = '';

        if($dtSettings->permalinks){
            if (count($params) >= 4) {
                $params = array_slice($params, 3);
                $op = $params[0];
                $log = $params[1];
            }
        } else {
            $log = $common->httpRequest()->request('log', 'integer', 0);
            $op = $common->httpRequest()->request('op', 'string', '');
        }

        require 'include/cpanel/logs.php';

        switch ($op) {
            case 'save':
                dt_save_log($log > 0 ? 1 : 0);
                break;
            case 'delete':
                dt_delete_log();
                break;
            default:
                dt_show_logs($op == 'edit' && $log > 0 ? 1 : 0);
                break;
        }

        break;

    case 'files':

        $op = '';
        if($dtSettings->permalinks){
            if (count($params) >= 4) {
                $params = array_slice($params, 3);
                $op = $params[0];
                $file = $params[1];
            }
        } else {
            $file = $common->httpRequest()->request('file', 'integer', 0);
            $op = $common->httpRequest()->request('op', 'string', '');
        }

        require 'include/cpanel/files.php';

        switch ($op) {
            case 'save':
                dt_save_file($file > 0 ? 1 : 0);
                break;
            case 'delete':
                dt_delete_file();
                break;
            default:
                dt_show_files($op == 'edit' && $file > 0 ? 1 : 0);
                break;
        }

        break;

    case 'delete':

        /**
         * Presents a screen to ask for a deletion before to run
         */
        $xoopsOption['template_main'] = 'dt-cpanel-delete.tpl';
        $xoopsOption['module_subpage'] = 'cp-delete';

        //global $xoopsTpl;

        $down = new Dtransport_Software($id);

        if($down->isNew()){
            $common->uris()::redirect_with_message(
                __('Specified download item does not exists!', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp'),
                RMMSG_ERROR
            );
        }

        if($down->uid != $xoopsUser->getVar('uid')){
            $common->uris()::redirect_with_message(
                __('Sorry, only the owner of this download item can request for its deletion.', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp'),
                RMMSG_ERROR
            );
        }

        include 'header.php';

        $xoopsTpl->assign('item', [
            'id' => $down->id(),
            'name' => $down->name,
            'image' => $down->logo != '' ? $down->logo : $down->image,
            'link' => $down->permalink(),
            'description' => $down->shortdesc
        ]);

        $xoopsTpl->assign('formAction', Dtransport_Functions::getInstance()->getURL('cp', ['delete-now' => $down->id()]));

        Dtransport_Functions::getInstance()->addLangString([
            'itemInfo' => __('Download item information', 'dtransport'),
            'imSure' => __('Yes, I\'m sure', 'dtransport'),
            'cancel' => __('Cancel', 'dtransport'),
            'confirmDelete' => sprintf(__('Do you really want to delete "%s"? This action can not be undone.', 'dtransport'), '<strong>' . $down->name . '</strong>'),
        ]);

        include 'footer.php';

        break;

    case 'delete-now':

        if($common->privileges()::verify('dtransport', 'delete-downloads', '', false)){

            $mailer = new RMMailer('text/html');
            $etpl = DT_PATH . '/templates/mail/' . $common->settings->lang . '/deletion.php';
            if (!file_exists($etpl))
                $etpl = DT_PATH . '/templates/mail/'  . $common->settings->lang . '/deletion_en.php';

            $common->events()->trigger('dtransport.before.delete.item', $item);

            if (!$item->delete()) {

                $common->events()->trigger('dtransport.error.delete.item', $item);
                $common->uris()::redirect_with_message(
                    __('We could not delete this download item. Please try again or contact the administrator.', 'dtransport'),
                    Dtransport_Functions::getInstance()->getURL('cp'), RMMSG_ERROR
                );
            }

            $common->events()->trigger('dtransport.item.deleted', $item);

            $mailer->template($etpl);
            $mailer->assign('siteurl', XOOPS_URL);
            $mailer->assign('dturl', $xoopsModuleConfig['permalinks'] ? XOOPS_URL . '/' . trim($xoopsModuleConfig['htbase'], '/') : DT_URL);
            $mailer->assign('downcp', $xoopsModuleConfig['permalinks'] ? XOOPS_URL . '/' . trim($xoopsModuleConfig['htbase'], '/') . '/cp/' : DT_URL . '/?p=cpanel');
            $mailer->assign('dtname', $xoopsModule->name());
            $mailer->assign('sitename', $xoopsConfig['sitename']);

            $xu = new XoopsUser($item->getVar('uid'));
            $mailer->add_to($xu->getVar('email'));
            $mailer->assign('uname', $xu->name() != '' ? $xu->name() : $xu->uname());
            $mailer->assign('download', $item->getVar('name'));
            $mailer->assign('email', $xu->getVar('email'));
            $mailer->assign('method', $xu->getVar('notify_method'));
            $mailer->set_subject(sprintf(__('Your download %s has been deleted!', 'dtransport'), $item->getVar('name')));
            if ($xu->getVar('notify_method') == 1) {
                $mailer->set_from_xuser($xoopsUser);
                $mailer->send_pm();
            } else
                $mailer->send();

            $common->uris()::redirect_with_message(
                __('Download item deleted successfully.', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp'), RMMSG_SUCCESS
            );

        } else {

            $item->setVar('deletion', 1);
            $item->setVar('status', 'deletion');
            if ($item->save()) {
                $common->uris()::redirect_with_message(
                    sprintf(__('Item marked to deletion successfully! From now and on, "%s" will be unavailable for download while administrators delete it.', 'dtransport'), $item->getVar('name')),
                    DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?p=cp'),
                    RMMSG_WARN
                );
            } else {
                $common->uris()::redirect_with_message(
                    __('Item could not be marked to deletion! Please try again.', 'dtransport'),
                    DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?p=cp'),
                    RMMSG_ERROR
                );
            }

        }

        break;

    case 'canceldel':

        $item->setVar('deletion', 0);
        if ($item->save())
            redirect_header(DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?p=cp'), 2, sprintf(__('Item restored successfully!', 'dtransport'), $item->getVar('name')));
        else
            redirect_header(DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?p=cp'), 2, __('Item could not be restored! Please try again.', 'dtransport'));

    case 'pending':
    default:

        $xoopsOption['template_main'] = 'dt-cpanel.tpl';
        $xoopsOption['module_subpage'] = 'cp-list';

        include 'header.php';

        Dtransport_Functions::getInstance()->cpanelHeader();

        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_dtransport_items") . " WHERE uid=" . $xoopsUser->uid();

        if('pending' == $action){
            $sql .= " AND (status = 'verify' || approved != 1)";
        }

        $sql .= " ORDER BY `created` DESC";
        list($num) = $db->fetchRow($db->query($sql));

        $limit = 15;
        $tpages = ceil($num / $limit);
        if ($tpages < $page && $tpages > 0) {
            header('location: ' . DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?s=cp'));
            die();
        }

        $p = $page > 0 ? $page - 1 : $page;
        $start = $num <= 0 ? 0 : $p * $limit;

        $nav = new RMPageNav($num, $limit, $page);
        $nav->target_url(DT_URL . ($dtSettings->permalinks ? '/cp/page/{PAGE_NUM}/' : '/?s=cp&amp;page={PAGE_NUM}'));
        $xoopsTpl->assign('pagenav', $nav->render(true));

        $sql = str_replace("COUNT(*)", '*', $sql) . " LIMIT $start, $limit";
        $result = $db->query($sql);

        $tf = new RMTimeFormatter('', __("%T%-%d%-%Y%", 'dtransport'));

        while ($row = $db->fetchArray($result)) {
            $item = new Dtransport_Software();
            $item->assignVars($row);
            $xoopsTpl->append('items', array(
                'id' => $item->id(),
                'name' => $item->getVar('name'),
                'links' => array(
                    'permalink' => $item->permalink(),
                    'edit' => $dtSettings->permalinks ? DT_URL . '/submit/edit/' . $item->id() : DT_URL . '/?s=submit&amp;action=edit&amp;id=' . $item->id(),
                    'delete' => $dtSettings->permalinks ? DT_URL . '/cp/delete/' . $item->id() : DT_URL . '/?s=cp&amp;action=delete&amp;id=' . $item->id(),
                    'files' => $dtSettings->permalinks ? DT_URL . '/cp/files/' . $item->id() : DT_URL . '/?s=cp&amp;action=files&amp;id=' . $item->id(),
                    'features' => $dtSettings->permalinks ? DT_URL . '/cp/features/' . $item->id() : DT_URL . '/?s=cp&amp;action=features&amp;id=' . $item->id(),
                    'logs' => $dtSettings->permalinks ? DT_URL . '/cp/logs/' . $item->id() : DT_URL . '/?s=cp&amp;action=logs&amp;id=' . $item->id(),
                    'screens' => $dtSettings->permalinks ? DT_URL . '/cp/screens/' . $item->id() : DT_URL . '/?s=cp&amp;action=screens&amp;id=' . $item->id(),
                    'canceldel' => $dtSettings->permalinks ? DT_URL . '/cp/canceldel/' . $item->id() : DT_URL . '/?s=cp&amp;action=canceldel&amp;id=' . $item->id()
                ),
                'secure' => $item->getVar('secure'),
                'approved' => $item->getVar('approved'),
                'created' => array('time' => $item->getVar('created'), 'formated' => $tf->format($item->getVar('created'))),
                'modified' => array('time' => $item->getVar('modified'), 'formated' => $tf->format($item->getVar('modified'))),
                'hits' => $item->getVar('hits'),
                'deletion' => $item->getVar('delete'),
                'status' => $item->status,
                'deletion' => $item->deletion
            ));
        }

        // Idioma
        Dtransport_Functions::getInstance()->addLangString([
            'id' => __('ID', 'dtransport'),
            'name' => __('Name', 'dtransport'),
            'created' => __('Created', 'dtransport'),
            'modified' => __('Last Modification', 'dtransport'),
            'status' => __('Status', 'dtransport'),
            'hits' => __('Hits', 'dtransport'),
            'noItems' => __('There are not download items created yet', 'dtransport'),
            'approved' => __('Approved', 'dtransport'),
            'notApproved' => __('Not approved', 'dtransport'),
            'options' => __('Options', 'dtransport'),
            'waitingVerify' => __('Waiting for approval', 'dtransport'),
            'forDelete' => __('Waiting deletion by administrators', 'dtransport'),
            'cancelDelete' => __('Cancel deletion request', 'dtransport')
        ]);

        $xoopsTpl->assign('lang_id', __('ID', 'dtransport'));
        $xoopsTpl->assign('lang_name', __('Name', 'dtransport'));
        $xoopsTpl->assign('lang_protected', __('Protected', 'dtransport'));
        $xoopsTpl->assign('lang_approved', __('Approved', 'dtransport'));
        $xoopsTpl->assign('lang_created', __('Created', 'dtransport'));
        $xoopsTpl->assign('lang_modified', __('Modified', 'dtransport'));
        $xoopsTpl->assign('lang_hits', __('Hits', 'dtransport'));
        $xoopsTpl->assign('lang_edit', __('Edit', 'dtransport'));
        $xoopsTpl->assign('lang_delete', __('Delete', 'dtransport'));
        $xoopsTpl->assign('lang_todelete', __('Waiting Deletion', 'dtransport'));
        $xoopsTpl->assign('lang_files', __('Files', 'dtransport'));
        $xoopsTpl->assign('lang_features', __('Features', 'dtransport'));
        $xoopsTpl->assign('lang_logs', __('Logs', 'dtransport'));
        $xoopsTpl->assign('lang_screens', __('Screenshots', 'dtransport'));
        $xoopsTpl->assign('lang_canceldel', __('Cancel Deletion', 'dtransport'));

        include 'footer.php';
        break;

}