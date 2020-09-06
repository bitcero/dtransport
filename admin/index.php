<?php
/**
 * D-Transport: Downloads Manager
 *
 * Copyright © 2015 Red Mexico http://www.eduardocortes.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Red Mexico http://www.eduardocortes.mx
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

define('RMCLOCATION', 'dashboard');
include 'header.php';

// Get usage data
$days = 30;
$period = 86400*($days - 1);
$sql = "SELECT `date` FROM ".$xoopsDB->prefix("mod_dtransport_downs")." WHERE `date` > ".(time()-$period)." ORDER BY `date`";
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
    $data[] = '['.($ago*1000).','.$total.']';
    $total30 += $total;

    $ago += 86400;
}

// Top Downloads
$sql = "SELECT * FROM ".$xoopsDB->prefix("mod_dtransport_items")." ORDER BY hits DESC LIMIT 0,10";
$result = $xoopsDB->query($sql);
$tops = array();

while($row = $xoopsDB->fetchArray($result)){
    $item = new Dtransport_Software();
    $item->assignVars($row);
    $tops[] = array(
        'name' => $row['name'], 
        'hits' => $row['hits'], 
        'link' => 'statistics.php?item='.$item->id()
    );
}

// Best Rated
$sql = "SELECT * FROM ".$xoopsDB->prefix("mod_dtransport_items")." ORDER BY rating/votes DESC LIMIT 0,5";
$result = $xoopsDB->query($sql);
$bestRated = array();

while($row = $xoopsDB->fetchArray($result)){
    $item = new Dtransport_Software();
    $item->assignVars($row);
    $bestRated[] = array(
        'name' => $row['name'], 
        'rating' => number_format($item->getVar('rating')/$item->getVar('votes'), 1),
        'link' => 'statistics.php?item='.$item->id()
    );
}

// D-TRANSPORT IN NUMBERS
// ----------------------

// Total downloads
list($totalItems) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mod_dtransport_items")));
list($totalDowns) = $xoopsDB->fetchRow($xoopsDB->query("SELECT SUM(hits) FROM ".$xoopsDB->prefix("mod_dtransport_items")));
list($itemsWaiting) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mod_dtransport_items")." WHERE approved=0"));
list($totalCats) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mod_dtransport_categories")));
list($catsInactive) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mod_dtransport_categories")." WHERE active=0"));

// Check htaccess if any
if($xoopsModuleConfig['permalinks']){


    $rule = "RewriteRule ^".trim($xoopsModuleConfig['htbase'],'/')."/?(.*)$ modules/dtransport/loader.php?q=$1 [L]";
    $ht = new RMHtaccess('dtransport');
    $htResult = $ht->write($rule);
    if($htResult!==true){
        showMessage(__('An error ocurred while trying to write .htaccess file!','dtransport'), RMMSG_ERROR);
    }

}

$common->template()->add_script("flot/jquery.flot.js", 'dtransport', ['footer' => 0, 'id' => 'flot-js']);
$common->template()->add_script("flot/jquery.flot.resize.js", 'dtransport', ['footer' => 0, 'id' => 'flot-resize-js']);
$common->template()->add_script("admin.min.js", 'dtransport', ['footer' => 0, 'id' => 'flot-resize-js']);
include_once DT_PATH . '/include/js-strings.php';

RMTemplate::getInstance()->add_body_class('dashboard');
$common->breadcrumb()->add_crumb(__('Dashboard', 'dtransport'));

//$rmTpl->add_style('dashboard.css', 'dtransport');

xoops_cp_header();

include RMTemplate::path('admin/dtrans-index.php', 'module', 'dtransport');

xoops_cp_footer();
