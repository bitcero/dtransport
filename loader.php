<?php
/**
 * D-Trnsport for XOOPS
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

require '../../mainfile.php';
//include 'header.php';

// Check inactivity
Dtransport_Functions::cronJob();

$dtSettings = $common->settings()->module_settings('dtransport');

// Constantes del Módulo
if(false == defined('DT_PATH')){
    define('DT_PATH', XOOPS_ROOT_PATH . '/modules/dtransport');
    define('DT_URL', $dtSettings->permalinks ? XOOPS_URL . '/' . trim($dtSettings->htbase, "/") : XOOPS_URL . '/modules/dtransport');
}

include 'include/fe-js-strings.php';

// Xoops Module Header
$dtfunc = Dtransport_Functions::getInstance();

$rmf = RMFunctions::get();
$rmu = RMUtilities::get();

$url = $common->uris()->current_url();

$rpath = parse_url($url);
$xpath = parse_url(XOOPS_URL);

// Comprobar si el host es correcto
if ($rpath['host'] != $xpath['host']) {
    /**
     * @todo Agregar header 303
     */
    header("location: " . DT_URL);
    die();
}

if (isset($xpath['path']) && substr($rpath['path'], 0, strlen($xpath['path'])) != $xpath['path'])
    $dtfunc->error_404();

if ($dtSettings->permalinks) {

    $params = trim(str_replace(@$xpath['path'] . '/' . trim($dtSettings->htbase, '/'), '', rtrim($rpath['path'], "/")), '/');
    $search = array('category', 'publisher', 'recents', 'popular', 'rated', 'updated');

    if ($params == '')
        $params = array();
    else
        $params = explode("/", trim($params));

    if (empty($params)) {
        require_once 'home.php';
        die();
    }

    switch ($params[0]) {
        case 'category':

            $params = explode("page", implode("/", array_slice($params, 1)));
            $path = $params[0];
            $page = isset($params[1]) ? trim($params[1], '/') : 1;

            require 'category.php';

            break;

        case 'feature':

            if (count($params) != 2)
                $dtfunc->error_404();

            $feature = $params[1];
            require 'include/cpanel/features.php';
            dt_return_feature();
            break;

        case 'download':

            if (count($params) > 2)
                $dtfunc->error_404();

            $id = $params[1];

            require 'getfile.php';

            break;

        case 'mine':
        case 'recent':
        case 'updated':
        case 'popular':
        case 'rated':

            $explore = $params[0];
            $page = 1;

            if (count($params) > 3)
                $dtfunc->error_404();

            if (isset($params[1])) {
                $params = array_slice($params, 1);

                if ($params[0] != 'page' || !is_numeric($params[1]))
                    $dtfunc->error_404();

                $page = $params[1];
            }

            require 'explore.php';

            break;

        case 'tag':

            $tag = $params[1];
            $page = 1;

            if (count($params) > 4)
                $dtfunc->error_404();

            if (isset($params[2])) {
                $params = array_slice($params, 2);

                if ($params[0] != 'page' || !is_numeric($params[1]))
                    $dtfunc->error_404();

                $page = $params[3];
            }

            require 'tags.php';

            break;

        case 'platform':

            $os = $params[1];
            if (count($params) > 4)
                $dtfunc->error_404();

            if (isset($params[2])) {
                $params = array_slice($params, 2);

                if ($params[0] != 'page' || !is_numeric($params[1]))
                    $dtfunc->error_404();

                $page = $params[3];
            }

            require 'platforms.php';

            break;

        case 'license':

            $lic = $params[1];
            $page = 1;

            if (count($params) > 4)
                $dtfunc->error_404();

            if (isset($params[2])) {
                $params = array_slice($params, 2);

                if ($params[0] != 'page' || !is_numeric($params[1]))
                    $dtfunc->error_404();

                $page = $params[3];
            }

            require 'licenses.php';
            break;

        case 'submit':
        case 'edit':

            if (count($params) > 3)
                $dtfunc->error_404();

            $action = isset($params[1]) ? $params[1] : rmc_server_var($_REQUEST, 'action', '');
            $id = isset($params[2]) ? $params[2] : rmc_server_var($_REQUEST, 'id', 0);

            require 'submit.php';

            break;

        case 'cp':

            if (count($params) > 5)
                $dtfunc->error_404();

            $action = '';
            $page = isset($params[1]) && $params[1] == 'page' ? $params[2] : 1;

            if (isset($params[1]) && $params[1] != 'page') {
                $action = $params[1];
                $id = $params[2];
            }
            require 'cpanel.php';

            break;

        default:

            if (count($params) > 2)
                $dtfunc->error_404();

            $id = trim($params[0]);
            $action = isset($params[1]) ? trim($params[1]) : '';

            require 'item.php';
            break;

    }

} else {

    $section = $common->httpRequest()->request('s', 'string', 'home');

    switch ($section) {
        case 'home':
            require_once 'home.php';
            break;

        case 'category':

            $page = $common->httpRequest()->get('page', 'integer', 1);
            $id = $common->httpRequest()->get('id', 'integer', 1);

            require 'category.php';

            break;

        case 'feature':

            $feature = $common->httpRequest()->get('id', 'integer', 0);

            if ($feature <= 0) {
                $dtfunc->error_404();
            }

            require 'include/cpanel/features.php';
            dt_return_feature();
            break;

        case 'download':

            $id = $common->httpRequest()->get('id', 'integer', 0);

            require 'getfile.php';

            break;

        case 'mine':
        case 'recent':
        case 'updated':
        case 'popular':
        case 'rated':

            $explore = $section;
            $page = $common->httpRequest()->get('page', 'integer', 1);

            require 'explore.php';

            break;

        case 'tag':

            $tag = $common->httpRequest()->get('tag', 'integer', 0);
            $page = $common->httpRequest()->get('page', 'integer', 1);

            if ($tag <= 0) {
                $dtfunc->error_404();
            }

            require 'tags.php';

            break;

        case 'platform':

            $os = $common->httpRequest()->get('os', 'integer', 0);
            $page = $common->httpRequest()->get('page', 'integer', 1);

            if ($os <= 0) {
                $dtfunc->error_404();
            }

            require 'platforms.php';

            break;

        case 'license':

            $lic = $common->httpRequest()->get('lic', 'integer', 0);
            $page = $common->httpRequest()->get('page', 'integer', 1);

            if ($lic <= 0) {
                $dtfunc->error_404();
            }

            require 'licenses.php';
            break;

        case 'submit':

            $action = $common->httpRequest()->request('action', 'string', '');
            $id = $common->httpRequest()->request('id', 'integer', '');

            require 'submit.php';

            break;

        case 'cp':

            $action = $common->httpRequest()->request('action', 'string', '');
            $page = $common->httpRequest()->request('page', 'integer', 1);
            $id = $common->httpRequest()->request('id', 'integer', 0);

            require 'cpanel.php';

            break;

        default:

            $id = $common->httpRequest()->get('id', 'integer', 0);
            $action = $common->httpRequest()->get('action', 'string', '');

            if ($id <= 0)
                $dtfunc->error_404();

            require 'item.php';
            break;

    }

}
