<?php
// $Id: rate.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Módulo para la administración de descargas
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

define('DT_LOCATION','rate');
include '../../mainfile.php';

$id = isset($_POST['id']) ? $_POST['id'] : 0;
$rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
$ret = isset($_POST['ret']) ? $_POST['ret'] : '';

$dtSettings =& $xoopsModuleConfig;


//Verificamos si el software existe
$item = new Dtransport_Software($id);
if ($item->isNew()){
	redirect_header(XOOPS_URL."modules/dtransport/",2,_MS_DT_ERRIDEXIST);
	die();
}

$retlink = ($ret!='' ? XOOPS_URL.'/modules/dtransport/'.($dtSettings->urlmode ? 'item/'.$item->nameid().'/' : 'item.php?id='.$item->id()) : $ret);

if ($rate<=0 || $rate>10){
	redirect_header($retlink, 2, _MS_DT_NORATE);
	die();
}

$db->queryF("DELETE FROM ".$db->prefix("dtrans_votedata")." WHERE date<'".(time()-86400)."'");

$ip = $_SERVER['REMOTE_ADDR'];

$sql = "SELECT COUNT(*) FROM ".$db->prefix("dtrans_votedata")." WHERE ";
if ($xoopsUser){
	$sql .= "uid='".$xoopsUser->uid()."' AND date>'".(time()-86400)."' AND id_soft='".$item->id()."'";
} else {
	$sql .= "ip='$ip' AND date>'".(time()-86400)."' AND id_soft='".$item->id()."'";
}

list($num) = $db->fetchRow($db->query($sql));

if ($num>0){
	redirect_header($retlink, 2, _MS_DT_NODAY);
	die();
}

if ($item->addVote($rate)){
	$db->queryF("INSERT INTO ".$db->prefix("dtrans_votedata")." (`uid`,`ip`,`date`,`id_soft`) VALUES
			('".($xoopsUser ? $xoopsUser->uid() : 0)."','$ip','".time()."','".$item->id()."')");
	redirect_header($retlink, 1, _MS_DT_VOTEOK);
	die();
} else {
	
	redirect_header($retlink, 1, _AS_DT_VOTEFAIL);
	die();
	
}
?>
