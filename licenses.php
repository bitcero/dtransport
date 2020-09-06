<?php
// $Id: licenses.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-licenses.tpl';
$xoopsOption['module_subpage'] = 'licenses';

include 'header.php';

$lic = new Dtransport_License($lic);

$common->breadcrumb()->add_crumb($lic->getVar('name'), $lic->permalink());

// Descargas en esta licencia
$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_licsoft')." AS a INNER JOIN ".$db->prefix('mod_dtransport_items')." b ON (a.id_lic=".$lic->id()." AND a.id_soft=b.id_soft) WHERE b.approved='1' ANd b.deletion=0";
list($num) = $db->fetchRow($db->query($sql));

$limit = $dtSettings->xpage;
$limit = $limit<=0 ? 10 : $limit;

$tpages = ceil($num / $limit);

if ($tpages<$page && $tpages>0){
    header('location: '.DT_URL.($dtSettings->permalinks?'/license/'.$lic->nameId():'/?p=license&lic='.$lic->id()));
    die();
}

$p = $page>0 ? $page-1 : $page;
$start = $num<=0 ? 0 : $p * $limit;

$nav = new RMPageNav($num, $limit, $page);
$nav->target_url(DT_URL.($dtSettings->permalinks?'/license/'.$lic->nameId().'/page/{PAGE_NUM}/':'/?p=license&amp;lic='.$lic->id().'&amp;page={PAGE_NUM}'));
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
$xoopsTpl->assign('license', array('id'=>$lic->id(),'name'=>$lic->name(),'link'=>$lic->permalink()));

$xoopsTpl->assign('xoops_pagetitle', sprintf(__('Downloads licensed as %s', 'dtransport'), $lic->name()));

if($dtSettings->inner_dest_download){
    $xoopsTpl->assign('featured_items', $dtfunc->items_by(array($lic->id()), 'licenses', 0, 'featured', 0, $dtSettings->limit_destdown));
}

// Descargas el día
if($dtSettings->inner_daydownload){
    $xoopsTpl->assign('daily_items', $dtfunc->items_by($lic->id(), 'licenses', 0, 'daily', 0, $dtSettings->limit_daydownload));
}

// Language
Dtransport_Functions::getInstance()->addLangString([
    'inLicense' => sprintf(__('<strong>Downloads</strong> licenced as "%s"', 'dtransport'), $lic->name()),
    'featured' => __('<strong>Featured</strong> Downloads','dtransport'),
    'inCategory' => __('In <a href="%s">%s</a>','dtransport'),
    'download' => __('Download','dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport')
]);

include 'footer.php';

