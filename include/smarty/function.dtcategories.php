<?php
/**
 * D-Transport for Xoops
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

function smarty_function_dtcategories($params, &$smarty){

    $categories = array_key_exists('categories', $params) ? $params['categories'] : [];
    $in = array_key_exists('in', $params) ? $params['in'] : 1;

    if (empty($categories)){
        return '';
    }

    $render = [];

    foreach($categories as $category){
        $render[] = '<a href="' . $category['link'] . '">' . $category['name'] . '</a>';
    }

    if(1==$in){
        return sprintf(__('in %s','dtransport'), implode(", ", $render));
    } else {
        return implode(", ", $render);
    }

}