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

include '../admin/header.php';

global $xoopsLogger;
$xoopsLogger->renderingEnabled = false;
$xoopsLogger->activated = false;

/**
 * Send a message in json format
 * @param string Message to be sent
 * @param int Indicates if message is an error
 * @param int Indicates if token must be sent
 */
function dt_send_message($message, $e = 0, $t = 1){
    global $xoopsSecurity;

    if($e){
        $data = array(
            'message' => $message,
            'error' => 1,
            'token' => $t?$xoopsSecurity->createToken():''
        );
    } else {

        $data = array(
            'error' => 0,
            'token' => $t?$xoopsSecurity->createToken():'',
        );
        $data = array_merge($data, $message);
    }

    echo json_encode($data);
    die();

}

/**
 * Save new o existing download
 * @param int Indicates if edit or create a download
 */
function dt_save_download($edit = 0){
    global $xoopsSecurity, $functions, $xoopsUser;

    $contact = 0;
    $logo = '';

    foreach($_POST as $k => $v){
        ${$k} = RMHttpRequest::array_value($k, $_POST, gettype($v), null );
    }

    $message = isset($message) ? $message : '';

    // Check xoops security
    if(!$xoopsSecurity->check())
        dt_send_message(__('Session token not valid!','dtransport'), 1,0);

    // Check data
    if($name=='')
        dt_send_message(__('Name must be specified!','dtransport'), 1, 1);

    if($shortdesc=='' || $desc=='')
        dt_send_message(__('A short description and full description must be specified for this download!', 'dtransport'), 1, 1);

    if(empty($catids))
        dt_send_message(__('Select a category to assign this download!', 'dtransport'), 1, 1);

    if(empty($lics))
        dt_send_message(__('A license must be selected at least!', 'dtransport'), 1, 1);

    if(empty($platforms))
        dt_send_message(__('A platform must be selected at least!', 'dtransport'), 1, 1);

    if(empty($groups))
        dt_send_message(__('A group must be selected at least!', 'dtransport'), 1, 1);

    if($edit){

        if($id<=0)
            dt_send_message(__('You must specified a download item to be edited!', 'dtransport'), 1, 1);

        $down = new Dtransport_Software($id);
        if($down->isNew())
            dt_send_message(__('Specified download item does not exists!', 'dtransport'), 1, 1);

        $previosApproval = $down->approved;
        $previousStatus = $down->status;

    } else {

        $down = new Dtransport_Software();
        $previosApproval = 0;
        $previousStatus = '';

    }

    $tc = TextCleaner::getInstance();

    if($nameid != ''){
        $nameid = $nameid;
    } else {
        $nameid = $tc->sweetstring($name);
    }

    $down->setVar('name', $name);
    $down->setVar('nameid', Dtransport_Functions::getNameId($nameid, $down));
    $down->setVar('version', $version);
    $down->setVar('shortdesc', $shortdesc);
    $down->setVar('description', $desc);
    $down->setVar('image', $image);
    $down->setVar('logo', $logo);
    $down->setVar('limits', $limits);
    if(!$edit) $down->setVar('created', time());
    $down->setVar('modified', time());
    $down->setVar('uid', $user);
    $down->setVar('password', $password);
    $down->setVar('secure', $password!='' ? 1 : $secure);
    $down->setVar('groups', $groups);
    $down->setVar('approved', $approved);

    if($approved){
        $down->setVar('status', '');
    }

    $down->setVar('featured', $mark);
    $down->setVar('siterate', $siterate);
    $down->setVar('author_name', $author);
    $down->setVar('author_email', $email);
    $down->setVar('author_url', $url);
    $down->setVar('author_contact', $contact);
    $down->setVar('langs', $langs);
    $down->setVar('deletion', 0);

    // Categories
    $down->setCategories($catids);
    // Licences
    $down->setLicences($lics);
    // Platforms
    $down->setPlatforms($platforms);
    // Tags
    $down->setTags($tags);

    // Alert
    if($alert){
        $down->createAlert();
        $down->setLimit($limitalert);
        $down->setMode($mode);
        $down->alert()->lastactivity = time();
    }

    global $xoopsDB;
    $db = $xoopsDB;
    // Check if exists another download with same name
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_dtransport_items")." WHERE name='$name' AND nameid='".$down->getVar('nameid')."'";
    if($edit) $sql .= " AND id_soft<>".$down->id();

    list($num) = $db->fetchRow($db->query($sql));
    if($num>0)
        dt_send_message(__('Another item with same name already exists!','dtransport'), 1, 1);

    $ev = RMEvents::get();
    $ev->run_event('dtransport.saving.item', $down);

    // Save item
    if(!$down->save() && $down->isNew()){
        dt_send_message(__('Item could not be saved!','dtransport').'<br />'.$down->errors(), 1, 1);
    }elseif(!$down->save() && !$down->isNew()){
        deleteEdited($down);
        dt_send_message(__('Item saved but with some errors!','dtransport').'<br />'.$down->errors(), 1, 1);
    }

    deleteEdited($down);

    if(($previosApproval != $down->approved && $xoopsUser->uid() != $down->uid) || 'verify' == $previousStatus){
        Dtransport_Functions::sendApprovalConfirmation($down, $message);
    }

    $functions->save_meta('down', $down->id());

    $ev->trigger('dtransport.item.saved', $down);

    $ret['id'] = $down->id();
    $ret['created'] = $down->getVar('created');
    $ret['modified'] = $down->getVar('modified');
    $ret['link'] = $down->permalink(1);
    $ret['message'] = $edit ? __('Changes saved successfully!','dtransport') : __('Item created successfully!','dtransport');
    dt_send_message($ret, 0, 1);

}

/**
 * Verify if there are a previous edited item and delte it
 * @param Dtransport_Software $id
 */
function deleteEdited(Dtransport_Software $item){
    $edited = new Dtransport_SoftwareEdited($item->id(), 'item');

    if(false == $edited->isNew() && $item->approved){
        $edited->delete();
    }
}

/**
 * Change the name used in permalinks
 */
function dt_change_nameid(){

    global $xoopsSecurity;

    if(!$xoopsSecurity->check())
        dt_send_message(__('Session token expired!','dtransport'), 1, 0);

    $id = rmc_server_var($_POST, 'id', '');
    if($id<=0)
        dt_send_message(__('No item ID has been provided!','dtransport'), 1, 1);

    $sw = new Dtransport_Software($id);
    if($sw->isNew())
        dt_send_message(__('Provided item ID is not valid!','dtransport'), 1, 1);

    $nameid = rmc_server_var($_POST, 'nameid', '');
    if($nameid=='')
        dt_send_message(__('Please provide new permalink name!','dtransport'), 1, 1);

    $tc = TextCleaner::getInstance();
    $nameid = $tc->sweetstring($nameid);

    global $xoopsDB;
    $db = $xoopsDB;

    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_dtransport_items")." WHERE nameid='$nameid' AND id_soft<>$id";
    list($num) = $db->fetchRow($db->query($sql));

    if($num>0)
        dt_send_message(__('Another item with same permalink name already exists! Please provide another one.','dtransport'), 1, 1);

    $sql = "UPDATE ".$db->prefix("mod_dtransport_items")." SET nameid='$nameid' WHERE id_soft=$id";

    if(!$db->queryF($sql))
        dt_send_message(__('New name could not be saved!','dtransport').'<br />'.$db->error(), 1, 1);

    $sw->setVar('nameid', $nameid);
    $ret['link'] = $sw->permalink(1);
    $ret['nameid'] = $nameid;
    $ret['message'] = __('Changes saved successfully!','dtransport');
    dt_send_message($ret, 0, 1);

}

/**
 * Change secure status for items
 */
function dt_change_data($type, $value=0){
    global $xoopsSecurity;

    if(!$xoopsSecurity->check())
        dt_send_message(__('Session token not valid!','dtransport'),1,0);

    $id = rmc_server_var($_POST, 'id', 0);
    if($id<=0)
        dt_send_message(__('No item ID has been provided!','dtransport'), 1, 1);

    $sw = new Dtransport_Software($id);
    if($sw->isNew())
        dt_send_message(__('Provided item ID is not valid!','dtransport'), 1, 1);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "UPDATE ".$db->prefix("mod_dtransport_items")." SET $type=$value WHERE id_soft=$id";
    if(!$db->queryF($sql))
        dt_send_message(__('Data could not be changed!','dtransport').'<br />'.$db->error(), 1, 1);

    dt_send_message(array(
        'message'=>__('Data changed successfully!','dtransport'),
        'value'=>$value,
        'name'=>$sw->getVar('name'),
        'id'=>$sw->id(),
        'link'=>$sw->permalink()
    ), 0, 1);

}


$action = RMHttpRequest::request( 'action', 'string', '' );

switch($action){
    case 'save':
        dt_save_download();
        break;
    case 'saveedit':
        dt_save_download(1);
        break;
    case 'permaname':
        dt_change_nameid();
        break;
    case 'lock':
        dt_change_data('secure',1);
        break;
    case 'unlock':
        dt_change_data('secure',0);
        break;
    case 'approved':
        dt_change_data('approved',1);
        break;
    case 'unapproved':
        dt_change_data('approved', 0);
        break;
    case 'featured':
        dt_change_data('featured', 1);
        break;
    case 'unfeatured':
        dt_change_data('featured', 0);
        break;
    case 'daily':
        dt_change_data('daily', 1);
        break;
    case 'undaily':
        dt_change_data('daily', 0);
        break;
}