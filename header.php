<?php
// $Id: header.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../header.php';
global $common;

$dtSettings = $common->settings()->module_settings('dtransport');

// Constantes del Módulo
$xoopsTpl->assign('dt_url', DT_URL);
$xoopsTpl->assign('dt_img_url', XOOPS_URL.'/modules/dtransport');

$dtfunc->checkAlert();

$xoopsTpl->assign('dtSettings', $common->settings()->module_settings('dtransport'));
$xoopsTpl->assign('dtTplPath', XOOPS_ROOT_PATH . '/modules/dtransport/templates/sets');

// Add styles
$common->template()->add_style('main-' . $dtSettings->tplset . '.min.css', 'dtransport', ['id' => 'dtmain-css']);

Dtransport_Functions::getInstance()->cpanelHeader();

