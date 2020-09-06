<?php
/**
 * D-Transport for XOOPS
 *
 * Copyright © 2015 Eduardo Cortés http://www.eduardocortes.mx
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
 * @url          http://www.eduardocortes.mx
 */

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    exit;
}

/**
 * This file contains all instructions to publish a submitted download item
 * immediately.
 */

/**
 * Check nameId
 */
if('' == $nameId){
    $nameId = $isEdition ? $item->getVar('nameid') : TextCleaner::sweetstring($name);
}

$item->setVar('name', $name);
$item->setVar('nameid', $nameId);
$item->setVar('version', $version);
$item->setVar('shortdesc', $shortDescription);
$item->setVar('description', $description);
$item->setVar('image', $image);
$item->setVar('logo', $logo);
$item->setVar('limits', $downLimit);
if (false == $isEdition) {
    $item->setVar('created', time());
}
$item->setVar('modified', time());
$item->setVar('uid', $xoopsUser->uid());

// Verify permissions for secured items
if($common->privileges()->verify('dtransport', 'secure-items', '', false)){
    $item->setVar('secure', $password != '' ? 1 : $secure);
    $item->setVar('password', $password);
}

// Verify permissions to assign allowed groups
if($common->privileges()->verify('dtransport', 'assign-groups', '', false)){
    $item->setVar('groups', $groups);
} else {
    $item->setVar('groups', $dtSettings->groups_default);
}

// Verify permissions to set item as featured
if($common->privileges()->verify('dtransport', 'featured-items', '', false)){
    $item->setVar('featured', $mark);
} else {
    $item->setVar('featured', 0);
}

$item->setVar('approved', $approved);

$item->setVar('author_name', $authorName);
$item->setVar('author_email', $authorEmail);
$item->setVar('author_url', $authorUrl);
$item->setVar('author_contact', $authorContact);

$item->setVar('langs', $languages);
$item->setVar('deletion', 0);

// Categories
$item->setCategories($catids);
// Licences
$item->setLicences($lics);
// Platforms
$item->setPlatforms($platforms);
// Tags
$item->setTags($tags);

// Check if exists another download with same name
$sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_dtransport_items")." WHERE name='$name' AND nameid='".$item->getVar('nameid')."'";
if($edit) $sql .= " AND id_soft != ".$item->id();

list($num) = $db->fetchRow($db->query($sql));

if($num>0){

    if($dtSettings->permalinks){
        $redirectUrl = DT_URL . '/submit/';
    } else {
        $redirectUrl = DT_URL . '/?s=submit';
    }

    $common->uris()->redirect_with_message(
        __('Another download item with same name already exists! Please try again.', 'dtransport'),
        $redirectUrl, RMMSG_WARN
    );
}

$common->events()->trigger('dtransport.saving.item', $item);
/*$uris = $common->uris();

if($item->save()){
    $uris::redirect_with_message(
        __('Item saved successfully', 'dtransport'),
        DT_URL . ($dtSettings->permalinks ? '/cp/' : '?s=cp'),
        RMMSG_SUCCESS
    );
}*/