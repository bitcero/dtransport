<?php
/**
 * D-Transport: Downloads Manager
 *
 * Copyright © 2015 - 2017 Red Mexico http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés http://www.eduardocortes.mx
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

load_mod_locale('dtransport');
ob_start();
?>

var DT_ERROR = 'error';
var DT_OK = 'ok';
var DT_MSG = '';
var DT_URL = '<?php echo XOOPS_URL; ?>/modules/dtransport';

var jsLang = {
    'checkForm': '<?php _e('Verifying form fields...','dtransport'); ?>',
    'errForm': '<?php _e('There are some errors in form fields. Please verify those fields marked with red color','dtransport'); ?>',
    'okForm': '<?php _e('Form fields verified successfully!','dtransport'); ?>',
    savingDown: '<?php _e('Saving Download...','dtransport'); ?>',
    applying: '<?php _e('Applying changes. Please wait a second...','dtransport'); ?>',
    cancel: '<?php _e('Cancel','dtransport'); ?>',
    normal: '<?php _e('Normal','dtransport'); ?>',
    secure: '<?php _e('Protected','dtransport'); ?>',
    noSelectMsg: '<?php _e('Before to run this action, you must select at least one item!','dtransport'); ?>',
    noSelectFeature: '<?php _e('You must select at least one feature before to perform this action!','dtransport'); ?>',
    noCheckedScreen: '<?php _e('You must select at least one image!','dtransport'); ?>',
    confirmDeletion: '<?php _e('Do you really want to delete selected items?','dtransport'); ?>',
    confirmFeatureDeletion: '<?php _e('Do you really want to delete selected features?','dtransport'); ?>',
    groupName: '<?php _e('Provide a group name!','dtransport'); ?>',
    deleteFile: '<?php _e('Do you really want to delete this file? You will not be able to recover it!','dtransport'); ?>',
    noFile: '<?php _e('Before to save details select a file!','dtransport'); ?>',
    noURL: '<?php _e('Before to save details provide a file URL!','dtransport'); ?>',
    noTitle: '<?php _e('You must provide a title for this item','dtransport'); ?>',
    edit: '<?php _e('Edit','dtransport'); ?>',
    delete: '<?php _e('Delete','dtransport'); ?>',
    updateGroup: '<?php _e('Update Group','dtransport'); ?>',
    createGroup: '<?php _e('Create Group','dtransport'); ?>',
    saveData: '<?php _e('Save Changes','dtransport'); ?>',
    titleField: '<?php _e('Title:','dtransport'); ?>',
    descField: '<?php _e('Description:','dtransport'); ?>',
    deleteScreen: '<?php _e('Do you really want to delete selected images (screenshots)?','dtransport'); ?>',
    deleteField: '<?php _e('Delete Field','dtransport'); ?>',
    alreadyName: '<?php _e('There is another field with same name.','dtransport'); ?>',
    errorName: '<?php _e('You must specify a name for this custom field!','dtransport'); ?>',
    insertScreen: '<?php _e('Insert Screenshots','dtransport'); ?>',
    nowActive: '<?php _e("D-Transport is now active!", 'dtransport'); ?>',
    activationInfo: '<?php _e("Your copy of D-Transport has been registered and activated with next information.", 'dtransport'); ?>',
    email: '<?php _e("Email:", 'dtransport'); ?>',
    serial: '<?php _e("Activation key:", 'dtransport'); ?>',
    activationDate: '<?php _e("Activation date:", 'dtransport'); ?>',
    saveInfo: '<?php _e("Please keep the data in a safe place for future reference.", 'dtransport'); ?>',
    reloadNow: '<?php _e("Reload page now.", 'dtransport'); ?>',
};

<?php

$strings = ob_get_clean();

$tpl = RMTemplate::get();
$tpl->add_inline_script(preg_replace( "/\r|\n|\s\s+/", "", $strings ));
unset($strings);