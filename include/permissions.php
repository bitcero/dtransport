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

return [

    // Can submit downloads
    'submit-downloads' => array(
        'caption' => __('Submit new downloads', 'dtransport'),
        'default' => 'deny'
    ),

    // Can user assign groups to a download?
    'assign-groups' => array(
        'caption' => __('Specify groups permissions for downloads', 'dtransport'),
        'default' => 'deny'
    ),

    // Allowed to limit downloads hits per user
    'limit-downloads' => array(
        'caption' => __('Specify download limit number per user', 'dtransport'),
        'default' => 'deny'
    ),

    // Approve submitted downloads immediately
    'approve-items' => array(
        'caption' => __('Approve submitted downloads immediately', 'dtransport'),
        'default' => 'deny'
    ),

    // Approve submitted downloads immediately
    'approve-editions' => array(
        'caption' => __('Approve submitted modifications immediately', 'dtransport'),
        'default' => 'deny'
    ),

    // Create featured items
    'featured-items' => array(
        'caption' => __('Create featured downloads', 'dtransport'),
        'default' => 'deny'
    ),

    // Create password protected items
    'secure-items' => array(
        'caption' => __('Create password protected items', 'dtransport'),
        'default' => 'deny'
    ),

    // Delete downloads
    'delete-downloads' => array(
        'caption' => __('Delete their own download items', 'dtransport'),
        'default' => 'deny'
    ),

];