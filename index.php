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

require '../../mainfile.php';

$dtSettings = $common->settings()->module_settings('dtransport');

// Constantes del Módulo
define('DT_PATH', XOOPS_ROOT_PATH . '/modules/dtransport');
define('DT_URL', $dtSettings->permalinks ? XOOPS_URL . '/' . trim($dtSettings->htbase, "/") : XOOPS_URL . '/modules/dtransport');

if($dtSettings->permalinks){
    header('location:' . DT_URL);
    die();
}

require('loader.php');