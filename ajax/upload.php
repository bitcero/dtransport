<?php
// $Id: upload.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';

$common->ajax()->prepare();

//$action = $common->httpRequest()->request('action', 'string', '');
$item = $common->httpRequest()->request('item', 'integer', 0);

$dtSettings = RMSettings::module_settings('dtransport');

$tc = TextCleaner::getInstance();

$db = XoopsDatabaseFactory::getDatabaseConnection();

$common->checkToken();

if($item<=0){
    $common->ajax()->notifyError(__('Download item ID not provided!','dtransport'));
}

//require_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtsoftware.class.php';

$sw = new Dtransport_Software($item);
if($sw->isNew()){
    $common->ajax()->notifyError(__('Specified download item does not exists!','dtransport'));
}

if($sw->getVar('secure')){
    $dir = $dtSettings->directory_secure;
    if(!is_dir($dir)){
        if(false == mkdir($dir, 511)) {
            $common->ajax()->notifyError(__('Directory for protected downloads does not exists!','dtransport'));
        }
    }
} else {
    $dir = $dtSettings->directory_insecure;
    if(!is_dir($dir)){
        if(false == mkdir($dir, 511)){
            $common->ajax()->notifyError(__('Directory for downloads does not exists!','dtransport'));
        }
    }
}

include RMCPATH.'/class/uploader.php';

$uploader = new RMFileUploader($dir, $dtSettings->size_file * 1024 * 1024, $dtSettings->type_file);

$err = array();
if (!$uploader->fetchMedia('file')){
    $common->ajax()->notifyError($uploader->getErrors());
}

if (!$uploader->upload()){
    $common->ajax()->notifyError($uploader->getErrors());
}

$ret = array(
    'file'  => $uploader->getSavedFileName(),
    'dir'   => $uploader->getSavedDestination(),
    'token' => $data[4],
    'size'  => $common->format()->bytes_format($uploader->getMediaSize()),
    'fullsize' => $uploader->getMediaSize(),
    'mime'  => $uploader->getMediaType(),
    'secure'=> $sw->getVar('secure')?__('Protected Download','dtransport'):__('Normal Download','dtransport'),
    'error' => 0,
    'notify' => [
        'type' => 'alert-success',
        'icon' => 'svg-rmcommon-ok-circle'
    ]
);

$common->ajax()->response(
    __('File upload successfully!', 'dtransport'), 0, 1, $ret
);
