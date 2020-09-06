<?php
// $Id: dtlistsoft.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// http://www.eduardocortes.mx
// http://www.exmsystem.com
// --------------------------------------------
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License as
// published by the Free Software Foundation; either version 2 of
// the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public
// License along with this program; if not, write to the Free
// Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
// MA 02111-1307 USA
// --------------------------------------------------------------
// @copyright: 2007 - 2008 Red México

define('DT_LOCATION','listsoft');
include ("../../../mainfile.php");
include ("../language/".$xoopsConfig['language']."/admin.php");
$tpl = new XoopsTpl();


$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
$parent = isset($_REQUEST['parent']) ? $_REQUEST['parent'] : '';

//Barra de navegación
$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_items');
$search ? $sql.=" WHERE name LIKE '%$search%'" : '';
	list($num)=$db->fetchRow($db->queryF($sql));
	
	$page = isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '';
    	$limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 30;
	$limit = $limit<=0 ? 30 : $limit;
	if ($page > 0){ $page -= 1; }
    	$start = $page * $limit;
    	$tpages = (int)($num / $limit);
    	if($num % $limit > 0) $tpages++;
    	$pactual = $page + 1;
    	if ($pactual>$tpages){
    	    $rest = $pactual - $tpages;
    	    $pactual = $pactual - $rest + 1;
    	    $start = ($pactual - 1) * $limit;
    	}
	
    
    	if ($tpages > 1) {
    	    $nav = new XoopsPageNav($num, $limit, $start, 'pag', 'limit='.$limit, 0);
    	    $tpl->assign('itemsNavPage', $nav->renderNav(4, 1));
    	}

	$showmax = $start + $limit;
	$showmax = $showmax > $num ? $num : $showmax;
	$tpl->assign('lang_showing', sprintf(_AS_DT_SHOWING, $start + 1, $showmax, $num));
	$tpl->assign('limit',$limit);
	$tpl->assign('pag',$pactual);

//Fin Barra de navegación

//Lista de software
$sql = "SELECT * FROM ".$db->prefix('mod_dtransport_items');
$search ? $sql.=" WHERE name LIKE '%$search%'" : '';
$sql.=" LIMIT $start,$limit";
$result = $db->queryF($sql);
while ($row=$db->fetchArray($result)){

	$tpl->append('items',array('id'=>$row['id_soft'],'name'=>$row['name']));
			
}

$tpl->assign('lang_search',_AS_DT_SEARCH);
$tpl->assign('lang_listsoft',_AS_DT_LISTSOFT);
$tpl->assign('parent',$parent);
$tpl->assign('search', $search);
echo $tpl->fetch('db:admin/dtrans_listsoft.html');
?>
