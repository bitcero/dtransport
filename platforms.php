<?php
// $Id: platforms.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-platforms.tpl';
$xoopsOption['module_subpage'] = 'platforms';

include 'header.php';

$os = new Dtransport_Platform($os);

$common->breadcrumb()->add_crumb($os->getVar('name'), $os->permalink());

// Descargas en esta etiqueta
$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_platsoft')." AS a INNER JOIN ".$db->prefix('mod_dtransport_items')." b ON (a.id_platform=".$os->id()." AND a.id_soft=b.id_soft) WHERE b.approved='1' AND b.deletion=0";
list($num) = $db->fetchRow($db->query($sql));

$limit = $dtSettings->xpage;
$limit = $limit<=0 ? 10 : $limit;

$tpages = ceil($num / $limit);

if ($tpages<$page && $tpages>0){
    header('location: '.DT_URL.($dtSettings->permalinks?'/platform/'.$os->nameId():'/?p=platform&os='.$os->id()));
    die();
}

$p = $page>0 ? $page-1 : $page;
$start = $num<=0 ? 0 : $p * $limit;

$nav = new RMPageNav($num, $limit, $page);
$nav->target_url(DT_URL.($dtSettings->permalinks?'/platform/'.$os->nameId().'/page/{PAGE_NUM}/':'/?p=platform&amp;os='.$os->id().'&amp;page={PAGE_NUM}'));
$xoopsTpl->assign('pagenav', $nav->render(true));

// Seleccionamos los registros
$sql = str_replace('COUNT(*)', 'b.*', $sql);
$sql .= " ORDER BY created DESC";

$sql .= " LIMIT $start, $limit";

$result = $db->query($sql);

while ($row = $db->fetchArray($result)){
    $item = new Dtransport_Software();
    $item->assignVars($row);
    $xoopsTpl->append('download_items', $dtfunc->createItemData($item));
}

// Datos de la etiqueta
$xoopsTpl->assign('platform', array('id'=>$os->id(),'name'=>$os->name(),'link'=>$os->permalink()));

$xoopsTpl->assign('xoops_pagetitle', sprintf(__('Downloads for %s', 'dtransport'), $os->name()));

if($dtSettings->inner_dest_download){
    $xoopsTpl->assign('featured_items', $dtfunc->items_by(array($os->id()), 'platforms', 0, 'featured', 0, $dtSettings->limit_destdown));
}

// Descargas el día
if($dtSettings->inner_daydownload){
    $xoopsTpl->assign('daily_items', $dtfunc->items_by($os->id(), 'platforms', 0, 'daily', 0, $dtSettings->limit_daydownload));
    $xoopsTpl->assign('daily_width', floor(100/($dtSettings->limit_daydownload)));
}

Dtransport_Functions::getInstance()->addLangString([
    'featured' => __('<strong>Featured</strong> Downloads','dtransport'),
    'inCategory' => __('In <a href="%s">%s</a>','dtransport'),
    'download' => __('Download','dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport'),
    'inOs' => sprintf(__('<strong>Downloads</strong> for "%s"', 'dtransport'), $os->name())
]);

include 'footer.php';
