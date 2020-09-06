<?php
/**
 * D-Transport for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

/**
 * This file performs tasks over download items
 * that not had movements in last days
 */

require '../../../mainfile.php';

$common->ajax()->prepare();

$dtSettings = $common->settings()::module_settings('dtransport');

$last = $common->settings()::module_settings('dtransport', 'alertChecked');
$lapse = $common->settings()::module_settings('dtransport', 'hrs_alerts');
$lapse = $lapse * 3600;

/**
 * Verify the time lapse
 */
if($last >= (time() - $lapse)){
    echo 'success';
    die();
}

// Perform verification of items
$acceptedPeriodToAlert = time() - ($dtSettings->alert_days * 86400);
$acceptedPeriodToDeletion = time() - ($dtSettings->deletion_days * 86400);

// Load mailer configuration
$config_handler = xoops_getHandler('config');
$mailConfig = $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);

/**
 * First Step: we will verify if there are items that exceed the limit days to
 *      send alerts. If there are, then we will send an email containing information
 *      about the inactivity.
 */
$fieldsItems = [
    'id_soft',
    'name',
    'version',
    'created',
    'modified',
    'uid',
    'author_name',
    'author_email',
    'nameid'
];

$sql = "SELECT items.". implode(", items.", $fieldsItems) . ", alerts.alerted, alerts.lastactivity, alerts.mode, alerts.id_alert 
        FROM " . $xoopsDB->prefix("mod_dtransport_alerts") . " as alerts INNER JOIN " . $xoopsDB->prefix("mod_dtransport_items") . "
        as items ON alerts.id_soft = items.id_soft WHERE alerts.lastactivity < $acceptedPeriodToAlert AND alerts.alerted < 3 AND items.status = ''";

$result = $xoopsDB->query($sql);

if($xoopsDB->getRowsNum($result) > 0){

    $mailer = xoops_getMailer();
    $mailer->setHTML(true);
    $mailer->useMail();
    //$mailer = new RMMailer('text/html');
    $etpl = XOOPS_ROOT_PATH . '/modules/dtransport/templates/mail/' . $rmc_config['lang'] . '/alert.php';

    if (!file_exists($etpl))
        $etpl = XOOPS_ROOT_PATH . '/modules/dtransport/templates/mail/en/alert.php';

    $urlInfo = parse_url(XOOPS_URL);
    //$mailer->template($etpl);

    while($row = $xoopsDB->fetchArray($result)){

        $mailer->setBody('');
        $item = new Dtransport_Software();
        $item->assignVars($row);

        $common->template()->assign('siteAddress', $urlInfo['host']);
        $common->template()->assign('siteUrl', XOOPS_URL);
        $common->template()->assign('moduleUrl', XOOPS_URL . '/modules/dtransport');
        $common->template()->assign('itemName', $row['name']);
        $common->template()->assign('itemUrl', $item->permalink());
        $common->template()->assign('itemEdit', Dtransport_Functions::getInstance()->getURL('submit', ['edit' => $row['id_soft']]));
        $common->template()->assign('limitDays', $dtSettings->alert_days);
        $common->template()->assign('deleteDays', $dtSettings->deletion_days);
        $common->template()->assign('userName', $row['author_name']);
        $mailer->setSubject(__('Alert for download inactivity!!', 'dtransport'));
        $mailer->setToEmails($row['author_email']);
        $mailer->setFromEmail($mailConfig['from']);
        $mailer->setFromName($mailConfig['fromname']);
        $mailer->setBody($common->template()->render($etpl));

        $common->events()->trigger('dtransport.send.alert', $item);

        if ($row['mode'] <= 0) {
            $mailer->setFromUser(new XoopsUser(1));
            $mailer->usePM();
            $mailer->send();
            continue;
        }

        if($mailer->send(true)){
            $item->alert()->alerted = $item->alert()->alerted + 1;
            $item->alert()->save();
        } else {
            echo "Error";
            echo $mailer->getErrors();
        }

        $common->events()->trigger('dtransport.alert.sent', $item);

    }
}

/**
 * Second Step: we will check if there are items that exceed the limit days before delete items.
 *      If there are then we will mark items for deletion.
 */
$sql = "SELECT items.". implode(", items.", $fieldsItems) . ", alerts.alerted, alerts.lastactivity, alerts.mode, alerts.id_alert 
        FROM " . $xoopsDB->prefix("mod_dtransport_alerts") . " as alerts INNER JOIN " . $xoopsDB->prefix("mod_dtransport_items") . "
        as items ON alerts.id_soft = items.id_soft WHERE alerts.alerted >= 3 AND items.modified < $acceptedPeriodToDeletion";

$result = $xoopsDB->query($sql);

if($xoopsDB->getRowsNum($result) <= 0){
    echo 'success';
    die();
}

$deletedItems = [];
$sql = "UPDATE " . $xoopsDB->prefix('mod_dtransport_items') . " SET status = 'deletion', deletion = 1 WHERE id_soft = %u";

while($row = $xoopsDB->fetchArray($result)){

    $item = new Dtransport_Software();
    $item->setVars($row);

    if($dtSettings->deletion_mode == 'mark'){
        $item->setVar('deletion', 1);
        $item->setVar('status', 'deletion');
        $deletedItem = [
            'name' => $item->name,
            'status' => __('For deletion', 'dtransport'),
            'result' => 'success'
        ];

        if($xoopsDB->queryF(sprintf($sql, $row['id_soft']))){
            $common->events()->trigger('dtransport.maintenance.todelete', $item);
            $deletedItem['result'] = 'fail';
        };

        $deletedItems[] = $deletedItem;

        continue;

    }

    if($dtSettings->deletion_mode == 'delete'){
        $deletedItem = [
            'name' => $item->name,
            'status' => __('Deleted', 'dtransport'),
            'result' => 'success'
        ];

        $common->events()->trigger('dtransport.maintenance.delete', $item);

        if(false == $item->delete()){
            $deletedItem['result'] = 'fail';
        }

        $deletedItems[] = $deletedItem;

        continue;
    }

}

$mailer = new RMMailer('text/html');
$etpl = XOOPS_ROOT_PATH . '/modules/dtransport/templates/mail/' . $rmc_config['lang'] . '/delete-report.php';

if (!file_exists($etpl))
    $etpl = XOOPS_ROOT_PATH . '/modules/dtransport/templates/mail/en/delete-report.php';

$urlInfo = parse_url(XOOPS_URL);
$mailer->template($etpl);

$mailer->set_body('');

$mailer->assign('siteAddress', $urlInfo['host']);
$mailer->assign('siteUrl', XOOPS_URL);
$mailer->assign('moduleUrl', XOOPS_URL . '/modules/dtransport');
$mailer->assign('deletedItems', $deletedItems);
$mailer->assign('logDate', date(__('Y-m-d H:i:s', 'dtransport'), time()));
$mailer->assign('deletionMethod', $dtSettings->deletion_mode == 'mark' ? __('Mark for deletion', 'dtransport') : __('Delete inmediatly', 'dtransport'));
$mailer->set_subject(__('D-Transport maintenance report', 'dtransport'));
$mailer->set_to($xoopsConfig['adminmail']);
$mailer->set_from($mailConfig['from'], $mailConfig['fromname']);

if($mailer->send()){
    $item->alert()->alerted = $item->alert()->alerted + 1;
    $item->alert()->save();
}

$common->settings()->setValue('dtransport', 'alertChecked', time());
