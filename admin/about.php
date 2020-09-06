<?php
/**
 * D-Transport for XOOPS
 * More info at Eduardo Cortés Website (www.eduardocortes.mx)
 *
 * Copyright © 2017 Eduardo Cortés (https://www.eduardocortes.mx)
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

include ('header.php');
$common->location = 'about';

$common->breadcrumb()->add_crumb(__('About D-Transport', 'dtransport'));
$common->template()->add_attribute('body', ['class' => 'dt-about']);
$common->template()->add_script('admin.min.js', 'dtransport', ['footer' => 1]);
include_once DT_PATH . '/include/js-strings.php';

if(false !== ($info = file_get_contents(XOOPS_VAR_PATH.'/data/dtinfo.dt'))){
    $info = json_decode($common->crypt()->decrypt($info));
    $info->serial = base64_decode($info->serial);
} else {
    $info->email = 'Not registered yet';
    $info->serial = 'Not registered yet';
    $info->date = 'Not registered yet';
    $info->expires = 'Not registered yet';
}

$common->template()->header();
?>

<div id="about-container">
    <a href="https://www.eduardocortes.mx/d-transport/" target="_blank"><img src="../images/logo.png" alt="D-Transport"></a>
    <h4><?php echo $common->format()::version($xoopsModule->getInfo('rmversion'), true); ?></h4>
    <hr>
    <p>
        <a href="https://www.eduardocortes.mx/d-transport/" target="_blank">D-Transport</a> is a module designed and developed by <a href="https://www.eduardocortes.mx">Eduardo Cortés</a> to be
        used with <a src="http://www.xoops.org" target="_blank">XOOPS</a> and <strong><a href="http://www.rmcommon.com">Common Utilities</a></strong>.
    </p>
    <hr>
    <div class="license-info">
        <p><strong><?php echo $common->format()::version($xoopsModule->getInfo('rmversion'), true); ?></strong></p>
        <p>
            Licensed to:<br>
            <em><?php echo $info->email; ?></em>
        </p>
        <p>
            Activation key:<br>
            <em><?php echo $info->serial; ?></em>
        </p>
        <p>
            Activated on:<br>
            <em><?php echo $common->timeFormat(__('%M% %d%, %Y% @ %h%:%i%:%s%', 'dtransport'))->format($info->date); ?></em>
        </p>
        <p>
            Expires on:<br>
            <em><?php echo $common->timeFormat(__('%M% %d%, %Y% @ %h%:%i%:%s%', 'dtransport'))->format($info->expires); ?></em>
        </p>
    </div>
    <div class="copy-notices">
        &copy; 2017 Eduardo Cortés
    </div>
</div>

<?php
$common->template()->footer();