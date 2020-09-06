<?php
// $Id: category.php 190 2013-01-08 05:24:45Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['template_main'] = 'dt-category.tpl';
$xoopsOption['module_subpage'] = 'category';
include 'header.php';


if($dtSettings->permalinks){

    if(!is_numeric($page))
        $dtfunc->error_404();

    if($path=='')
        redirect_header(DT_URL, 2, __('Category not specified!','dtransport'));

    $idp = 0; # ID de la categoria padre
    $path = explode("/", $path);
    foreach ($path as $k){

        if ($k=='') continue;

        $category = new Dtransport_Category();
        $sql = "SELECT * FROM ".$db->prefix("mod_dtransport_categories")." WHERE nameid='$k' AND parent='$idp'";
        $result = $db->query($sql);

        if ($db->getRowsNum($result)>0){
            $row = $db->fetchArray($result);
            $idp = $row['id_cat'];
            $category->assignVars($row);
            
            $common->breadcrumb()->add_crumb($category->getVar('name'), $category->permalink());
            
        } else {
            $dtfunc->error_404();
        }

    }

} else {

    if($id<=0)
        redirect_header(DT_URL, 1, __("Specified category does not exists!",'dtransport'));

    if(!is_numeric($page))
        $page = 1;

    $category = new Dtransport_Category($id);

}


// Descargas en esta categoría
$tbls = $db->prefix("mod_dtransport_items");
$tblc = $db->prefix("mod_dtransport_catitem");
$sql = "SELECT COUNT(*) FROM $tbls as s, $tblc as c WHERE c.cat='".$category->id()."' AND s.id_soft=c.soft AND s.approved=1 AND s.deletion=0";
list($num) = $db->fetchRow($db->query($sql));

$limit = $dtSettings->xpage;
$limit = $limit<=0 ? 10 : $limit;

$tpages = ceil($num / $limit);

if ($tpages<$page && $tpages>0){
    header('location: '.$category->permalink());
    die();
}

$p = $page>0 ? $page-1 : $page;
$start = $num<=0 ? 0 : $p * $limit;

$nav = new RMPageNav($num, $limit, $page);
$nav->target_url($category->permalink().($dtSettings->permalinks ? 'page/{PAGE_NUM}/' : '&amp;page={PAGE_NUM}'));
$xoopsTpl->assign('pagenav', $nav->render(true));

// Seleccionamos los registros
$sql = str_replace('COUNT(*)', 's.*', $sql);
$sql .= " ORDER BY s.modified DESC";

$sql .= " LIMIT $start, $limit";

$result = $db->queryF($sql);

while ($row = $db->fetchArray($result)){
	$item = new Dtransport_Software();
	$item->assignVars($row);
	$xoopsTpl->append('download_items', array_merge($dtfunc->createItemData($item), [
        'category'=>$category->name()
    ]));
}

if($dtSettings->inner_dest_download){
    $xoopsTpl->assign('featured_items', $dtfunc->get_items($category->id(), 'featured', $dtSettings->limit_destdown));
}

// Descargas el día
if($dtSettings->inner_daydownload){
    $xoopsTpl->assign('daily_items', $dtfunc->get_items($category->id(), 'daily', $dtSettings->limit_daydownload));
    $xoopsTpl->assign('daily_width', floor(100/($dtSettings->limit_daydownload)));
    $xoopsTpl->assign('lang_daydown', __('<strong>Day</strong> Downloads','dtransport'));
}

// Language
Dtransport_Functions::getInstance()->addLangString([
    'featured' => __('<strong>Featured</strong> Downloads','dtransport'),
    'inCategory' => __('In <a href="%s">%s</a>','dtransport'),
    'download' => __('Download','dtransport'),
    'dayDownload' => __('<strong>Day</strong> Downloads','dtransport'),
]);

$xoopsTpl->assign('xoops_pagetitle', $category->name() . " &raquo; " . $xoopsModule->name());
$xoopsTpl->assign('lang_in', sprintf(__('<strong>Downloads in</strong> %s','dtransport'), $category->name()));

$xoopsTpl->assign('lang_featured',sprintf(__('<strong>%s</strong> Featured Downloads','dtransport'), $category->name()));
$xoopsTpl->assign('lang_download', __('Download','dtransport'));

// Datos de la categoría
$xoopsTpl->assign('category', array('id'=>$category->id(),'name'=>$category->name(),'link'=>$category->permalink()));
$xoopsTpl->assign('xoops_pagetitle', sprintf(__('Downloads in "%s"','dtransport'), $category->name()));

include 'footer.php';

