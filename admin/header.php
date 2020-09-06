<?php
// $Id: header.php 212 2013-02-02 18:11:17Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


include_once '../../../include/cp_header.php';


/**
* Verificamos el directorio para los archivos de imagen
**/
if (!file_exists(XOOPS_UPLOAD_PATH.'/dtransport')){
	mkdir(XOOPS_UPLOAD_PATH.'/dtransport',511);
}

/**
* Verificamos el directorio ths para imagenes miniatura
**/
if (!file_exists(XOOPS_UPLOAD_PATH.'/dtransport/ths')){
	mkdir(XOOPS_UPLOAD_PATH.'/dtransport/ths',511);
}


$dtSettings = $common->settings()->module_settings('dtransport');
$common->template()->assign('dtSettings', $dtSettings);
/**
* Verificamos la existencia de directorio de descargas no seguras
**/
if (!file_exists($dtSettings->directory_insecure)){
	if (!mkdir($dtSettings->directory_insecure,511)){
		showMessage(sprintf(__('Directory "%s" to store downloads does not exists yet!','dtransport'),$dtSettings->directory_insecure), RMMSG_WARN);
	}
}

/**
* Verificamos la existencia de directorio de descargas seguras
**/
if (!file_exists($dtSettings->directory_secure)){
	if (!mkdir($dtSettings->directory_secure,511)){
		showMessage(sprintf(__('Protected directory \"%s\" does not exists!','dtransport'),$dtSettings->directory_secure), RMMSG_WARN);
	}
}

// Constants
define('DT_PATH', XOOPS_ROOT_PATH.'/modules/dtransport');
define('DT_URL', XOOPS_URL.'/modules/dtransport');

// Dashboard styles
$common->template()->add_style('cp.min.css', 'dtransport', ['id' => 'dtransport-js']);

$tpl = RMTemplate::get();
$functions = new Dtransport_Functions();
