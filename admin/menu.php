<?php
// $Id: menu.php 201 2013-01-27 06:47:22Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (function_exists('load_mod_locale')) load_mod_locale('dtransport');

global $common;

$item = 0;

if ('items' == $common->location || 'screens' == $common->location) {
    $item = RMHttpRequest::get('item', 'integer', 0);
    $item = $item <= 0 ? RMHttpRequest::get('id', 'integer', 0) : $item;
}

//Inicio
$adminmenu[] = array(
    'title' => __('Dashboard', 'dtransport'),
    'link' => "./admin/index.php",
    'icon' => "icon icon-meter",
    'location' => "dashboard"
);

//Categorias
$adminmenu[] = array(
    'title' => __('Categories', 'dtransport'),
    'link' => "./admin/categories.php",
    'icon' => "icon icon-folder text-amber",
    'location' => "categories"
);

//Licencias
$adminmenu[] = array(
    'title' => __('Licenses', 'dtransport'),
    'link' => "admin/licenses.php",
    'icon' => "icon icon-file-text3 text-purple",
    'location' => 'licenses'
);

//Plataformas
$adminmenu[] = array(
    'title' => __('Platforms', 'dtransport'),
    'link' => "admin/platforms.php",
    'icon' => "icon icon-cog text-brown",
    'location' => 'platforms'
);

/**
 * Additional download options
 */
$options = array();
$options[] = array(
    'title' => __('All downloads', 'dtransport'),
    'link' => 'admin/items.php',
    'selected' => 'items',
    'icon' => 'icon icon-menu'
);
$options[] = array(
    'title' => __('Pending', 'dtransport'),
    'link' => 'admin/items.php?type=wait',
    'selected' => 'itemswait',
    'icon' => 'icon icon-hour-glass'
);
$options[] = array(
    'title' => __('Edited', 'dtransport'),
    'link' => 'admin/items.php?type=edit',
    'selected' => 'itemsedited',
    'icon' => 'icon icon-pencil'
);
$options[] = array(
    'title' => __('Deleted', 'dtransport'),
    'link' => 'admin/items.php?type=delete',
    'selected' => 'items-deleted',
    'icon' => 'icon icon-bin2 text-deep-orange'
);

if ($item > 0) {

    $options[] = array(
        'divider' => true
    );

    //Pantallas
    $options[] = array(
        'title' => __('Screenshots', 'dtransport'),
        'link' => "./admin/screens.php" . ($item > 0 ? '?item=' . $item : ''),
        'icon' => "icon icon-camera",
        'selected' => 'screenshots'
    );

    //Caracteristicas
    $options[] = array(
        'title' => __('Features', 'dtransport'),
        'link' => "./admin/features.php" . ($item > 0 ? '?item=' . $item : ''),
        'icon' => "icon icon-cog",
        'selected' => 'features'
    );

    //Archivos
    $options[] = array(
        'title' => __('Files', 'dtransport'),
        'link' => "admin/files.php" . ($item > 0 ? '?item=' . $item : ''),
        'icon' => "icon icon-download3",
        'selected' => 'files'
    );

    //Logs
    $options[] = array(
        'title' => __('Logs', 'dtransport'),
        'link' => "admin/logs.php" . ($item > 0 ? '?item=' . $item : ''),
        'icon' => "icon icon-calendar",
        'selected' => 'logs'
    );

    //Statistics
    $options[] = array(
        'title' => __('Statistics', 'dtransport'),
        'link' => "admin/statistics.php" . ($item > 0 ? '?item=' . $item : ''),
        'icon' => "icon icon-stats-dots",
        'selected' => 'statistics'
    );
}

$options[] = array(
    'divider' => true
);
$options[] = array(
    'title' => __('New Download', 'dtransport'),
    'link' => 'admin/items.php?action=new',
    'selected' => 'newitem',
    'icon' => 'icon icon-plus'
);

//Elementos
$adminmenu[] = array(
    'title' => __('Downloads', 'dtransport'),
    'link' => "./admin/items.php",
    'icon' => "icon icon-cloud-download text-green",
    'location' => 'items',
    'options' => $options
);

$adminmenu[] = array(
    'title' => __('About', 'dtransport'),
    'link' => "./admin/about.php",
    'icon' => "svg-rmcommon-info text-info",
    'location' => 'about'
);


