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

function smarty_function_ourRate($params, &$smarty){
    global $common;

    $rate = $common->httpRequest()->array_value('rate', $params, 'integer', 0);
    $rate = $rate < 0 ? 0 : $rate;
    $rate = $rate > 10 ? 10 : $rate;

    $legends = [
        0 => __('Not rated', 'dtransport'),
        1 => __('Very bad', 'dtransport'),
        2 => __('Bad', 'dtransport'),
        3 => __('Not so bad', 'dtransport'),
        4 => __('To improve', 'dtransport'),
        5 => __('Acceptable', 'dtransport'),
        6 => __('Useful', 'dtransport'),
        7 => __('Good', 'dtransport'),
        8 => __('Very good', 'dtransport'),
        9 => __('Excelent', 'dtransport'),
        10 => __('Must have', 'dtransport'),
    ];

    return $legends[$rate];

}