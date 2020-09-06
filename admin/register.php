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

$common->ajax()->prepare();

$common->checkToken();

$api = $common->httpRequest()::post('api', 'string', '');
$email = $common->httpRequest()::post('email', 'string', '');
$key = $common->httpRequest()::post('key', 'string', '');

if('' == $api || '' == $email || '' == $key){
    $common->ajax()->notifyError(
        __('Please provide all data', 'dtransport')
    );
}

if(false == checkEmail($email)){
    $common->ajax()->notifyError(__('Please provide a valid email. This must be the email that you used when purchase your license.', 'dtransport'));
}

$url = $xoopsModule->getInfo('updateurl');
$siteId = urlencode(md5(crypt(XOOPS_LICENSE_KEY . XOOPS_URL, $common->settings->secretkey)));
$api = urlencode($api);
$email = urlencode($email);
$key = urlencode($key);

$query = "action=license&site=$siteId&id=dtransport&type=module&api=$api&email=$email&key=$key";

$response = json_decode($common->httpRequest()::load_url($url, $query, true), true);

if(null == $response || $response['type'] == 'error'){
    if(null == $response){
        $common->ajax()->notifyError(__('No response from licenses server', 'vcontrol'));
    }

    $common->ajax()->notifyError($response['message'], 1);
}

$common->settings()->setValue('dtransport', 'licenseData', $response['licenseData']);
$common->settings()->setValue('dtransport', 'branding', 0);
$info = ['email'=>urldecode($email),'serial'=>$response['chain'],'date'=>$response['date'],'expires'=>$response['expiration']];
file_put_contents(XOOPS_VAR_PATH . '/data/dtinfo.dt', $common->crypt()->encrypt(json_encode($info)));

$common->ajax()->response(
    __('D-Transport has been activated successfully! Please reload the page to start using the module.', 'dtransport'), 0, 1, [
        'notify' => [
            'type' => 'alert-success',
            'icon' => 'svg-dtransport-verify'
        ],
        'chain' => base64_decode($response['chain']),
        'date' => $common->timeFormat(__('%M% %d%, %Y% @ %h%:%i%:%s%', 'dtransport'))->format($response['date']),
        'api' => urldecode($api),
        'key' => urldecode($key),
        'email' => urldecode($email)
    ]
);