<?php
// $Id: item.php 211 2013-02-01 16:53:27Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if ($id == '') {
    header('location: ' . DT_URL);
    die();
}

$item = new Dtransport_Software($id);
$dtFunc = new Dtransport_Functions();

if ($item->getVar('approved')) {
    $canview = true;
} else {
    $canview = $xoopsUser->isAdmin() || ($xoopsUser->uid() == $item->getVar('uid'));
}

if ($item->isNew() || !$canview) {
    redirect_header(DT_URL, 2, __('Specified item does not exists!', 'dtransport'));
    die();
}

if ($item->getVar('delete'))
    redirect_header(DT_URL, 2, __('This item is not available for download at this moment!', 'dtransport'));

// Download default file
if ($action == 'download') {

    $file = $item->file();
    if (!$file)
        redirect_header($item->permalink(), 0, __('Internal Error! Please try again later', 'dtransport'));

    header("location: " . $file->permalink());
    die();

}

$xoopsOption['template_main'] = 'dt-item.tpl';
$xoopsOption['module_subpage'] = 'item';

include 'header.php';

$xoopsTpl->assign('dtrans_option', 'details');

$common->breadcrumb()->add_crumb($item->getVar('name') . ' ' . $item->getVar('version'), $item->permalink());

$canDownload = $item->canDownload($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS);

// Enlaces del elemento
$data = array();
$data['id'] = $item->id();
$data['link'] = $item->permalink();
$data['screens'] = $item->permalink(0, 'screens');
$data['download'] = $canDownload ? $item->permalink(0, 'download') : '';
$data['features'] = $item->permalink(0, 'features');
$data['logs'] = $item->permalink(0, 'logs');

// Datos generales
$data['name'] = $item->getVar('name');
$data['version'] = $item->getVar('version');

// Imagen por defecto
$img = new RMImage();

$data['image'] = $item->getVar('image');

if ('' != $item->logo) {
    $data['logo'] = $item->logo;
}

if($item->rating > 0){
    $rating = $item->rating / $item->votes;
} else {
    $rating = 0;
}
$data['rating'] = $rating > 0 ? number_format($rating,1) : 0;
$data['usersRating'] = $dtFunc->usersRating($item->getVar('votes'), $item->getVar('rating'));
$data['votes'] = $item->getVar('votes');

// Licencias
$data['licenses'] = array();
foreach ($item->licences(true) as $lic) {
    $data['licenses'][] = array(
        'url' => $lic->link(),
        'name' => $lic->name(),
        'link' => $lic->permalink()
    );
}

//  Plataformas
$data['platforms'] = array();
foreach ($item->platforms(true) as $os) {
    $data['platforms'][] = array(
        'name' => $os->name(),
        'link' => $os->permalink()
    );
}

$tf = new RMTimeFormatter(0, '%T% %d%, %Y%'); // Time formatter

$data['created'] = $tf->format($item->getVar('created'));
$data['update'] = $item->getVar('created') > 0 ? $tf->format($item->getVar('modified')) : '';
$data['author'] = array(
    'name' => $item->getVar('author_name'),
    'url' => $item->getVar('author_url'),
    'email' => $item->getVar('author_email'),
    'contact' => $item->getVar('author_contact'),
);
$data['langs'] = $item->getVar('langs');
$data['downs'] = $item->getVar('hits');
$data['version'] = $item->getVar('version');
$data['updated'] = $item->getVar('modified') > $item->getVar('created') && $item->getVar('modified') > (time() - ($dtSettings->update * 86400));
$data['new'] = !$data['updated'] && $item->getVar('created') > (time() - ($dtSettings->new * 86400));
$data['description'] = $item->description;
$data['shortdesc'] = $item->shortdesc;
$data['siterate'] = $item->siterate;
$data['localRating'] = $dtfunc->localRating($item->getVar('siterate'));

$fg = $item->fileGroups(true);
$data['filegroups'] = array();
foreach ($fg as $g) {
    $files = $g->files(true);
    $data['filegroups'][$g->id()]['name'] = $g->name();
    foreach ($files as $file) {
        $data['filegroups'][$g->id()]['files'][] = array(
            'file' => $file->file(),
            'size' => $rmu->formatBytesSize($file->size()),
            'date' => $tf->format($file->date()),
            'title' => $file->title(),
            'remote' => $file->remote(),
            'hits' => $file->hits(),
            'link' => $file->permalink(),
            'default' => $file->default
        );
    }
}

// Imágenes de la Descarga
$imgs = $item->screens(true);
$data['screens'] = array();
foreach ($imgs as $img) {
    $data['screens'][] = array(
        'id' => $img->id(),
        'title' => $img->title(),
        'image' => $img->image
    );
}
unset($imgs, $img);

//Etiquetas
$tags = $item->tags(true, false);
$relatedTags = array();
$data['tags'] = array();
foreach ($tags as $id => $tag) {
    $data['tags'][] = array(
        'id' => $tag->id(),
        'name' => $tag->tag,
        'link' => $tag->permalink()
    );
    $relatedTags[] = $tag->id();
}

unset($tags, $otag, $tag);

// Categories
$cats = $item->categories(true);

$data['categories'] = array();
foreach ($cats as $ocat) {
    $data['categories'][] = array(
        'id' => $ocat->id(),
        'name' => $ocat->name(),
        'link' => $ocat->permalink()
    );
}
unset($ocat, $cats, $cat);

// Características
$chars = $item->features(true);
$data['features'] = array();
foreach ($chars as $feature) {
    $updated = $feature->modified() > $feature->created() && $feature->modified() > (time() - ($dtSettings->update * 86400));
    $new = !$updated && $feature->created() > (time() - ($dtSettings->new * 86400));
    $data['features'][] = array(
        'id' => $feature->id(),
        'title' => $feature->title(),
        'updated' => $updated,
        'nameid' => $feature->nameid(),
        'content' => $feature->content(),
        'link' => $feature->permalink(),
        'metas' => $dtfunc->get_metas('feat', $feature->id())
    );
}
unset($chars, $feature);

// Logs
$logs = $item->logs(true, 1);
$data['logs'] = array();
foreach ($logs as $log) {
    $data['logs'][] = array(
        'id' => $log->id(),
        'title' => $log->title(),
        'content' => $log->log(),
        'date' => formatTimestamp($log->date(), 's')
    );
}
unset($logs, $log);

$data['metas'] = $dtfunc->get_metas('down', $item->id());

$data['approved'] = $item->getVar('approved');

$xoopsTpl->assign('item', $data);

// Usuario
$dtUser = new XoopsUser($item->getVar('uid'));
$xoopsTpl->assign('dtUser', array('id' => $dtUser->uid(), 'uname' => $dtUser->uname(), 'avatar' => $dtUser->getVar('user_avatar')));


if ($dtSettings->daydownload) {
    $xoopsTpl->assign('daily_items', $dtfunc->get_items(0, 'daily', $dtSettings->limit_daydownload));
    $xoopsTpl->assign('daily_width', floor(100 / ($dtSettings->limit_daydownload)));
    Dtransport_Functions::getInstance()->addLangString('daydown', __('<strong>Day</strong> Downloads', 'dtransport'));
}

// Desargas relacionadas
if ($dtSettings->active_relatsoft) {
    Dtransport_Functions::getInstance()->addLangString('related', __('<strong>Related</strong> Downloads', 'dtransport'));
    $xoopsTpl->assign('related_items', $dtfunc->items_by($relatedTags, 'tags', $item->id(), 'RAND()', 0, $dtSettings->limit_relatsoft));
}

if (!$item->getVar('approved')) {
    Dtransport_Functions::getInstance()->addLangString('noapproved', __('This item has not been approved yet! You can view this information but other users can not.', 'dtransport'));
}

// Lenguaje
Dtransport_Functions::getInstance()->addLangString([
    'new' => __('New', 'dtransport'),
    'updated' => __('Updated', 'dtransport'),
    'date' => __('Date', 'dtransport'),
    'author' => __('Author:', 'dtransport'),
    'version' => __('Version:', 'dtransport'),
    'createdon' => __('Created on:', 'dtransport'),
    'updatedon' => __('Updated on:', 'dtransport'),
    'langs' => __('Languages:', 'dtransport'),
    'platforms' => __('Supported platforms:', 'dtransport'),
    'license' => __('License:', 'dtransport'),
    'ratings' => __('Download Ratings', 'dtransport'),
    'siterate' => __('Our Rating', 'dtransport'),
    'rateuser' => __('Users', 'dtransport'),
    'yourrate' => __('Your Rate:', 'dtransport'),
    'votes' => __('%u votes', 'dtransport'),
    'downnow' => __('Download Now!', 'dtransport'),
    'download' => __('Download', 'dtransport'),
    'screenshots' => __('Screenshots', 'dtransport'),
    'tags' => __('Tags:', 'dtransport'),
    'published' => __('Categories:', 'dtransport'),
    'downopts' => __('Download Options', 'dtransport'),
    'details' => __('Download details', 'dtransport'),
    'logs' => __('Logs', 'dtransport'),
    'features' => __('Item <strong>Features</strong>', 'dtransport'),
    'changes' => __('Recent <strong>Changes</strong>', 'dtransport'),
    'comments' => __('Comments', 'dtransport'),
    'choose' => __('You can choose between next download options if you prefer another file type or another location.', 'dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport'),
    'screensCount' => sprintf(__('%u images', 'dtransport'), count($data['screens']) + 1)
]);

// Download options labels
Dtransport_Functions::getInstance()->addLangString([
    'title' => __('Title', 'dtransport'),
    'size' => __('Size', 'dtransport'),
    'hits' => __('Hits', 'dtransport')
]);

$xoopsTpl->assign('xoops_pagetitle', $item->getVar('name') . ($item->getVar('version') != '' ? " " . $item->getVar('version') : '') . " &raquo; " . $xoopsModule->name());

$ratings = array();
$max = $xoopsModuleConfig['max_rating'];
$increment = $xoopsModuleConfig['usedec'] ? "0.1" : 1;

for ($i = 0; $i <= $max; $i = $i + $increment) {
    $ratings[] = $i;
}
$xoopsTpl->assign('ratings', $ratings);
$xoopsTpl->assign('xoops_token', $xoopsSecurity->getTokenHTML());

// el usuario ha votado?
$userVote = $item->getUserVote();
if ($userVote === false)
    $userVote = 5;

$rmTpl->add_head_script("var dt_max = $max;\nvar dt_steps = $increment;\nvar dt_vote = $userVote;\nvar dtURL = '" . XOOPS_URL . "/modules/dtransport'");

// Ubicación Actual

$common->template()->add_style('js-widgets.css','rmcommon', ['id' => 'ui-css']);
$common->template()->add_script('main.min.js', 'dtransport', ['id' => 'dtransport-js', 'footer' => 1]);

$metas = $data['metas'];
$rmf->add_keywords_description(isset($metas['description']) ? $metas['description'] : $item->getVar('shortdesc'), isset($metas['keywords']) ? $metas['keywords'] : '');

// Lightbox plugins
if ($rmf->plugin_installed("lightbox")) {
    $lightbox = RMLightbox::get();
    $lightbox->add_element("a.item-images");
    $lightbox->render();
}

/**
 * Comments
 */
$comments = $common->comments()->load([
    'object' => 'dtransport',
    'type' => 'module',
    'identifier' => 'item=' . $item->id(),
    'assign' => false,
    'url' => DT_URL . '/post.php'
]);

$xoopsTpl->assign('comments', $comments);

// Comments form
$xoopsTpl->assign('comments_form', $common->comments()->form([
    'url' => DT_URL . '/post.php',
    'object' => 'dtransport',
    'type' => 'module',
    'identifier' => 'item=' . $item->id(),
    'file' => DT_PATH . '/class/dtransportcontroller.php'
]));

Dtransport_Functions::getInstance()->assignLang();

include 'footer.php';

