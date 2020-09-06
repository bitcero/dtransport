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


/**
 * Es necesario verificar si existe Common Utilities o si ha sido instalado
 * para evitar problemas en el sistema
 */
$amod = xoops_getActiveModules();
if(!in_array("rmcommon",$amod)){
    $error = "<strong>WARNING:</strong> D-Transport requires %s to be installed!<br />Please install %s before trying to use D-Transport";
    $error = str_replace("%s", '<a href="http://www.eduardocortes.mx/w/common-utilities/" target="_blank">Common Utilities</a>', $error);
    xoops_error($error);
    $error = '%s is not installed! This might cause problems with functioning of D-Transport and entire system. To solve, install %s or uninstall D-Transport and then delete module folder.';
    $error = str_replace("%s", '<a href="http://www.eduardocortes.mx/w/common-utilities/" target="_blank">Common Utilities</a>', $error);
    trigger_error($error, E_USER_WARNING);
    echo "<br />";
}

if(function_exists("load_mod_locale")) load_mod_locale ('dtransport');

if (!function_exists("__")){
    function __($text, $d){
        return $text;
    }
}
