<?php
// $Id: getfile.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if ($id<=0){
	header('location: '.DT_URL);
	die();
}

$dtSettings = $common->settings()->module_settings('dtransport');

$file = new Dtransport_File($id);
if ($file->isNew() && $dtSettings->permalinks)
	$dtfunc->error_404();
elseif($file->isNew())
    redirect_header(DT_URL, 1, __('File not found!','dtransport'));

// Check if item exists
$item = new Dtransport_Software($file->software());
if ($item->isNew() || !$item->getVar('approved')){
	if($dtSettings->permalinks)
        $dtfunc->error_404();
    else
        redirect_header(DT_URL, 1, __('Software does not exists!','dtransport'));
}

// Check if users must be logged before to download
if($dtSettings->mustLogin && !$xoopsUser){
    $common->uris()->redirect_with_message(
        __('You must sign up before to download this file!', 'dtransport'),
        XOOPS_URL . '/user.php?op=main&xoops_redirect=' . urlencode($common->uris()->relative_url($item->permalink(0, 'download'))), RMMSG_WARN
    );
}

// Verify that user can download this item
if (!$item->canDownload($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS)){
    $common->uris()->redirect_with_message(
        __('Sorry, you don\'t have permission to download this file!','dtransport'),
        $item->permalink(),
        RMMSG_WARN
    );
}

// Check max number of download per user
if ($item->limits > 0 && $item->downloadsCount() >= $item->limits){
    $common->uris()->redirect_with_message(
        __('You have reached your download limit for this file!','dtransport'),
        $item->permalink(),
        RMMSG_ERROR
    );
}

// Verificamos si la descarga se debe realizar
$token = isset($_SESSION['dttoken']) ? $_SESSION['dttoken'] : '';

if($token=='' || !$xoopsSecurity->validateToken($token)){

    $_SESSION['dttoken'] = $xoopsSecurity->createToken();
    $xoopsOption['template_main'] = 'dt-get-file.tpl';
    $xoopsOption['module_subpage'] = 'getfile';

    include 'header.php';

    $common->breadcrumb()->add_crumb($item->getVar('name').' '.$item->getVar('version'), $item->permalink());
    $common->breadcrumb()->add_crumb($file->getVar('title'), '');

    $img = new RMImage();
    $img->load_from_params($item->getVar('image'));

    $xoopsTpl->assign('item', Dtransport_Functions::getInstance()->createItemData($item));

    Dtransport_Functions::getInstance()->addLangString([
        'headerTitle' => sprintf(__('Downloading %s', 'dtransport'), $item->name),
        'message' => sprintf(__('Your %s download will start shortly...', 'dtransport'), '<a href="'.$item->permalink().'">'.$item->getVar('name').'</a>'),
        'problems' => sprintf(__('Problems with the download? Please %s to download immediately.', 'dtransport'), '<a href="'.$file->permalink().'">'.__('use this link','dtransport').'</a>'),
        'version' => sprintf(__('Version: %s', 'dtransport'), $item->version),
        'fileName' => sprintf(__('File name: %s', 'dtransport'), $file->file),
        'size' => sprintf(__('Size: %s', 'dtransport'), $common->format()->bytes_format($file->size)),
    ]);
    Dtransport_Functions::getInstance()->assignLang();

    $common->template()->add_script('main.min.js', 'dtransport', ['footer' => '1', 'id' => 'dtransport-js']);
    $common->template()->add_inline_script('var down_message = "'.sprintf(__('Your %s download will start in {x} seconds...', 'dtransport'), '<a href=\''.$item->permalink().'\'>'.$item->getVar('name').'</a>').'";');
    $common->template()->add_inline_script('var timeCounter = '.$dtSettings->pause.";\nvar dlink = '".$file->permalink()."';");

    include 'footer.php';

    die();

}

// Comprobamos si el archivo es seguro o no
if (!$item->getVar('secure')){
	// Comprobamos si es un archivo remoto o uno local	
	if ($file->remote()){
		// Almacenamos las estadísticas
		$st = new DTStatistics();
		$st->setDate(time());
		$st->setFile($file->id());
		$st->setSoftware($item->id());
		$st->setUid($xoopsUser ? $xoopsUser->uid() : 0);
		$st->setIp($_SERVER['REMOTE_ADDR']);
		$st->save();
		$item->addHit();
		$file->addHit();
        unset($_SESSION['dttoken']);
		header('location: '.$file->file());
		die();

	} else {

        $dir = str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $dtSettings->directory_insecure);
		$dir = str_replace("\\","/",$dir);
		$dir = rtrim($dir, '/');

        $path = $dtSettings->directory_insecure;
		$path = str_replace("\\", "/", $path);
		$path = rtrim($path, '/');

		if (!file_exists($path.'/'.$file->file()))
			redirect_header(DT_URL.'/report.php?item='.$item->id()."&amp;error=0", 2, __('We\'re sorry but specified file does not exists!','dtransport'));
		
		$st = new DTStatistics();
		$st->setDate(time());
		$st->setFile($file->id());
		$st->setSoftware($item->id());
		$st->setUid($xoopsUser ? $xoopsUser->uid() : 0);
		$st->setIp($_SERVER['REMOTE_ADDR']);
		$st->save();

		$alert = new Dtransport_Alert($item->id());
        if(!$alert->isNew()){
		    $alert->setLastActivity(time());
		    $alert->save();
        }

		$item->addHit();
		$file->addHit();
        unset($_SESSION['dttoken']);
		header('location: '.$dir.'/'.$file->file());
		die();
	}
	
}

// Enviamos una descarga segura
$path = $dtSettings->directory_secure;
$path = str_replace("\\", "/", $path);
$path = rtrim($path, '/');

if (!file_exists($path.'/'.$file->file()))
	redirect_header(DT_URL.'/report.php?item='.$item->id()."&amp;error=0", 2, __('We\'re sorry but selected file does not exists!','dtransport'));

$st = new DTStatistics();
$st->setDate(time());
$st->setFile($file->id());
$st->setSoftware($item->id());
$st->setUid($xoopsUser ? $xoopsUser->uid() : 0);
$st->setIp($_SERVER['REMOTE_ADDR']);
$st->save();

$alert = new Dtransport_Alert($item->id());
if(!$alert->isNew()){
    $alert->setLastActivity(time());
    $alert->save();
}
unset($_SESSION['dttoken']);
$item->addHit();
$file->addHit();
header('Content-type: '.$file->mime());
header('Cache-control: no-store');
header('Expires: '.gmdate("D, d M Y H:i:s",time()+31536000).'GMT');
header('Content-disposition: filename='.urlencode($file->file()));
header('Content-Lenght: '.filesize($path.'/'.$file->file()));
header('Last-Modified: '.gmdate("D, d M Y H:i:s",filemtime($path.'/'.$file->file())).'GMT');
readfile($path.'/'.$file->file());
die();
