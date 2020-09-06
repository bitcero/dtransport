<?php
// $Id: explore.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-explore.tpl';
$xoopsOption['module_subpage'] = 'explore-'.$explore;

$accepted = $common->events()->trigger('dtransport.exploring.accepted', array('mine','recent','popular','rated','publisher','updated'));


if(!isset($explore) || !in_array($explore, $accepted)){
    if($dtSettings->permalinks)
        $dtfunc->error_404();
    else
        redirect_header(DT_URL, 1, __('Invalid parameters','dtransport'));
}

// Comprobamos el usuario
if(!$xoopsUser && $explore=='mine')
    redirect_header(DT_URL, 1, __('You are not authorized to view this section!','dtransport'));

$titles = array(
    'mine' => __('My Downloads','dtransport'),
    'recent' => __('Recent Downloads','dtransport'),
    'updated' => __('Updated Downloads','dtransport'),
    'popular' => __('Most Downloaded','dtransport'),
    'rated' => __('Best Rated Downloads','dtransport'),
    'publisher' => __('Downloads of %s','dtransport')
);
$titles = $common->events()->trigger('dtransport.exploring.titles', $titles);

include 'header.php';

$common->breadcrumb()->add_crumb($titles[$explore], DT_URL.($dtSettings->permalinks?'/'.$explore:'/?p=explore&f='.$explore));

// Preparamos la consulta SQL
$sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_dtransport_items")." WHERE approved=1 AND `deletion`=0";
switch($explore){
    case 'mine':
        $sql .= " AND uid=".$xoopsUser->uid()." ORDER BY `created` DESC";
        break;
    case 'updated':
        $sql .= " AND uid=".$xoopsUser->uid()." ORDER BY `modified` DESC";
        break;
    case 'recent':
        $sql .= " ORDER BY `created` DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY `hits` DESC";
        break;
    case 'rated':
        $sql .= " ORDER BY (rating/votes) DESC";
        break;
    case 'publisher':
        $sql = " AND uid=".$publisher->uid()." ORDER BY `created`,`modified` DESC";
        break;
}

$sql = $common->events()->trigger("dtransport.explore.count", $sql);

list($num) = $db->fetchRow($db->query($sql));

$limit = $dtSettings->xpage;
$limit = $limit<=0 ? 10 : $limit;
$tpages = ceil($num / $limit);

if ($tpages<$page && $tpages>0){
    header('location: '.DT_URL.($dtSettings->permalinks?'/'.$explore:'/?p=explore&f='.$explore));
    die();
}

$p = $page>0 ? $page-1 : $page;
$start = $num<=0 ? 0 : $p * $limit;

$nav = new RMPageNav($num, $limit, $page);
$nav->target_url(DT_URL.($dtSettings->permalinks?'/'.$explore.'/page/{PAGE_NUM}/':'/?p=explore&amp;f='.$explore.'&amp;page={PAGE_NUM}'));
$xoopsTpl->assign('pagenav', $nav->render(false));

$sql = str_replace("COUNT(*)",'*',$sql);
$sql .= " LIMIT $start,$limit";
$result = $db->query($sql);

while($row = $db->fetchArray($result)){
    $item = new Dtransport_Software();
    $item->assignVars($row);
    $xoopsTpl->append('items', array_merge($dtfunc->createItemData($item), [
        'usersRate' => number_format($item->rating / $item->votes, 1),
        'percent' => number_format($item->rating / $item->votes, 0) * 10,
        'langVotes' => sprintf(__('%s votes', 'dtransport'), $common->format()->quantity($item->votes)),
        'langDownloads' => sprintf(__('%s downloads', 'dtransport'), $common->format()->quantity($item->hits))
    ]));
}

if($dtSettings->inner_dest_download)
    $xoopsTpl->assign('featured_items', $dtfunc->get_items(0, 'featured', $dtSettings->limit_destdown));

// Descargas el día
if($dtSettings->inner_daydownload){
    Dtransport_Functions::getInstance()->addLangString('dayDownload', __('<strong>Day</strong> Downloads','dtransport'));
    $xoopsTpl->assign('daily_items', $dtfunc->get_items(0, 'daily', $dtSettings->limit_daydownload));
    $xoopsTpl->assign('daily_width', floor(100/($dtSettings->limit_daydownload)));
}

// Idioma
Dtransport_Functions::getInstance()->addLangString('featured', __('<strong>Featured</strong> Downloads','dtransport'));
Dtransport_Functions::getInstance()->addLangString('download', __('Download','dtransport'));
//Dtransport_Functions::getInstance()->addLangString('in', __('<strong>Available</strong> Downloads','dtransport'));
$xoopsTpl->assign('listTitle', $titles[$explore]);

$xoopsTpl->assign('xoops_pagetitle', $titles[$explore]);

Dtransport_Functions::getInstance()->assignLang();

//$common->template()->add_style('main.min.css','dtransport', ['id' => 'dtrans-css']);

include 'footer.php';
