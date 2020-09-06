<?php
// $Id: tags.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-tags.tpl';
$xoopsOption['module_subpage'] = 'tags';

include 'header.php';

$tag = new DTTag($tag);

//Incrementamos los hits
$tag->setHit($tag->hit()+1);
$tag->save();

$common->breadcrumb()->add_crumb($tag->getVar('tag'), $tag->permalink());

// Descargas en esta etiqueta
$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_itemtag')." AS a INNER JOIN ".$db->prefix('mod_dtransport_items')." b ON (a.id_tag=".$tag->id()." AND a.id_soft=b.id_soft) WHERE b.approved='1' AND b.deletion='0'";
list($num) = $db->fetchRow($db->query($sql));

$limit = $dtSettings->xpage;
$limit = $limit<=0 ? 10 : $limit;

$tpages = ceil($num / $limit);

if ($tpages<$page && $tpages>0){
    header('location: '.DT_URL.($dtSettings->permalinks?'/tag/'.$tag->tagId():'/?p=tag&tag='.$tag->id()));
    die();
}

$p = $page>0 ? $page-1 : $page;
$start = $num<=0 ? 0 : $p * $limit;

$nav = new RMPageNav($num, $limit, $page);
$nav->target_url(DT_URL.($dtSettings->permalinks?'/tag/'.$tag->tagId().'/page/{PAGE_NUM}/':'/?p=tag&amp;tag='.$tag->id().'&amp;page={PAGE_NUM}'));
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
$xoopsTpl->assign('tag', array('id'=>$tag->id(),'name'=>$tag->tag(),'link'=>$tag->permalink()));

$xoopsTpl->assign('xoops_pagetitle', sprintf(__('Downloads tagged as "%s"', 'dtransport'), $tag->tag()));

if($dtSettings->inner_dest_download){
    $xoopsTpl->assign('featured_items', $dtfunc->items_by(array($tag->id()), 'tags', 0, 'featured', 0, $dtSettings->limit_destdown));
}

// Descargas el día
if($dtSettings->inner_daydownload){
    $xoopsTpl->assign('daily_items', $dtfunc->items_by($tag->id(), 'tags', 0, 'daily', 0, $dtSettings->limit_daydownload));
}

// Language
Dtransport_Functions::getInstance()->addLangString([
    'featured' => __('<strong>Featured</strong> Downloads','dtransport'),
    'inCategory' => __('In <a href="%s">%s</a>','dtransport'),
    'download' => __('Download','dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport'),
]);

$xoopsTpl->assign('lang_download', __('Download','dtransport'));
$xoopsTpl->assign('lang_in', sprintf(__('Downloads tagged as "%s"', 'dtransport'), '<strong>'.$tag->tag().'</strong>'));

include 'footer.php';
