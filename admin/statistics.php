<?php
// $Id: dt_items.php 1005 2012-07-12 05:40:46Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCSUBLOCATION', 'statistics');
include 'header.php';
$common->location = 'items';

$rmTpl->add_style('admin.css', 'dtransport');

$item = rmc_server_var($_GET, 'item', 0);

if($item<=0)
    redirectMsg(DT_URL, __('You must specify a valid download item!','dtransport'), RMMSG_ERROR);

$item = new Dtransport_Software($item);
if($item->isNew())
    redirectMsg(DT_URL, __('Specified item is not valid!','dtransport'), RMMSG_WARN);

$functions->itemsToolbar($item);

// Get usage data
$days = 30;
$period = 86400*$days;
$sql = "SELECT `date` FROM ".$xoopsDB->prefix("mod_dtransport_downs")." WHERE `date` > ".(time()-$period)." AND id_soft=".$item->id()." ORDER BY `date`";
$result = $xoopsDB->query($sql);
$usageData = array();

while($row = $xoopsDB->fetchArray($result)){
    if(!isset($usageData[date("m-d", $row['date'])]))
        $usageData[date("m-d", $row['date'])] = 0;
        
    $usageData[date("m-d", $row['date'])]++;
}

$ago = mktime(0,0,1,date("m",time()-$period),date('d', time()-$period), date('Y', time()-$period));
$data = array();
$tf = new RMTimeFormatter(0, '%T% %d%');
$total30 = 0;

for($i=0;$i<$days;$i++){

    $total = isset($usageData[date('m-d', $ago)]) ? $usageData[date('m-d', $ago)] : 0;
    //$total = rand(0, 300);
    $data[] = '['.($ago*1000).','.$total.']';
    $total30 += $total;

    $ago += 86400;
}

// Files activity
$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("mod_dtransport_files")." WHERE id_soft=".$item->id()." ORDER BY hits DESC LIMIT 0,5");
$filesIds = array();
$filesObjects = array();

if($xoopsDB->getRowsNum($result)>0){

    while($row = $xoopsDB->fetchArray($result)){
        $file = new Dtransport_File();
        $file->assignVars($row);
        $filesIds[] = $row['id_file'];
        $filesObjects[$row['id_file']] = $file;
    }

    // Get Data
    $sql = "SELECT `date`, id_file FROM ".$xoopsDB->prefix("mod_dtransport_downs")." WHERE `date` > ".(time()-$period)." AND id_file IN (".implode(",",$filesIds).") ORDER BY `date`";

    $result = $xoopsDB->query($sql);
    $usageData = array();

    while($row = $xoopsDB->fetchArray($result)){
        $usageData[$row['id_file']][date("m-d", $row['date'])]++;
    }

    $ago = mktime(0,0,1,date("m",time()-$period),date('d', time()-$period), date('Y', time()-$period));
    $dataFiles = array();
    $tf = new RMTimeFormatter(0, '%T% %d%');
    $total30 = 0;

    for($i=0;$i<$days;$i++){

        foreach($filesIds as $id){

            $total = isset($usageData[$id][date('m-d', $ago)]) ? $usageData[$id][date('m-d', $ago)] : 0;
            //$total = rand(50, 300);
            $dataFiles[$id][] = '['.($ago*1000).','.$total.']';
            $total30 += $total;

        }

        $ago += 86400;

    }

    //print_r($dataFiles); die();

}

// All files
$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("mod_dtransport_files")." WHERE id_soft=".$item->id()." ORDER BY hits DESC");
$allFiles = array();
$filesCount = 0;
while($row = $xoopsDB->fetchArray($result)){
    $file = new Dtransport_File();
    $file->assignVars($row);
    $filesCount++;
    if($file->getVar('hits')>0)
        $allFiles[] = $file;
}

$common->breadcrumb()->add_crumb(__('Downloads', 'dtransport'), 'items.php');
$common->breadcrumb()->add_crumb($item->name, 'items.php?action=edit&');

// Template
$rmTpl->add_style('statistics.css', 'dtransport');

$rmTpl->add_script("flot/jquery.flot.js", 'dtransport', ['id' => 'flot-js', 'footer' => 1]);
$rmTpl->add_script("flot/jquery.flot.resize.js", 'dtransport', ['id' => 'flot-resize-js', 'footer' => 1]);
$rmTpl->add_script("flot/jquery.flot.selection.js", 'dtransport', ['id' => 'flot-selection-js', 'footer' => 1]);

xoops_cp_header();

include RMTemplate::getInstance()->path('admin/dtrans-statistics.php', 'module', 'dtransport');

xoops_cp_footer();