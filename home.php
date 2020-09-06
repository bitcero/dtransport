<?php
// $Id: home.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-index.tpl';
$xoopsOption['module_subpage'] = 'index';

include 'header.php';

//include XOOPS_ROOT_PATH.'/header.php';

if($dtSettings->dest_download){
    $xoopsTpl->assign('featured_items', $dtfunc->get_items(0, 'featured', $dtSettings->limit_destdown));
}

// Descargas recientes
$xoopsTpl->assign('recent_items', $dtfunc->get_items(0, 'recent', $dtSettings->limit_recents));
// Descargas mejor valoradas
$xoopsTpl->assign('rated_items', $dtfunc->get_items(0, 'rated', $dtSettings->limit_recents));
// Descargas actualizadas
$xoopsTpl->assign('updated_items', $dtfunc->get_items(0, 'updated', $dtSettings->limit_recents));
// Descargas el día
if($dtSettings->daydownload){
    $xoopsTpl->assign('daily_items', $dtfunc->get_items(0, 'daily', $dtSettings->limit_daydownload));
}

if($dtSettings->permalinks){
    $xoopsTpl->assign('moreRecentLink', Dtransport_Functions::moduleURL() . '/recent/');
    $xoopsTpl->assign('moreRatedLink', Dtransport_Functions::moduleURL() . '/rated/');
    $xoopsTpl->assign('moreUpdatedLink', Dtransport_Functions::moduleURL() . '/updated/');
} else {
    $xoopsTpl->assign('moreRecentLink', Dtransport_Functions::moduleURL() . '?s=recent');
    $xoopsTpl->assign('moreRatedLink', Dtransport_Functions::moduleURL() . '?s=rated');
    $xoopsTpl->assign('moreUpdatedLink', Dtransport_Functions::moduleURL() . '?s=updated');
}

Dtransport_Functions::getInstance()->addLangString([
    'recents' => __('New Downloads','dtransport'),
    'bestRated' => __('Best Rated','dtransport'),
    'updated' => __('Updated','dtransport'),
    'featured' => __('<strong>Featured</strong> Downloads','dtransport'),
    'inCategory' => __('In <a href="%s">%s</a>','dtransport'),
    'download' => __('Download','dtransport'),
    'rateSite' => $xoopsConfig['sitename'],
    'categories' => __('Categories','dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport'),
    'viewMore' => __('View More +', 'dtransport')
]);

include 'footer.php';