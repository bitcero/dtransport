<?php
// $Id: files-ajax.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';
load_mod_locale('dtransport');
$xoopsModule = RMModules::load('dtransport');
$xoopsModuleConfig = RMSettings::module_settings('dtransport');

$common->ajax()->prepare();

define('DT_PATH', XOOPS_ROOT_PATH.'/modules/dtransport');
define('DT_URL',$xoopsModuleConfig->permalinks ? XOOPS_URL.'/'.trim($xoopsModuleConfig->htbase, "/") : XOOPS_URL.'/modules/dtransport');

include_once DT_PATH.'/class/functions.class.php';
include_once DT_PATH.'/class/software.class.php';

$dtFunc = new Dtransport_Functions();

/**
* Send a response in json format
* 
* @param mixed Message to send
* @param mixed INdicate that this message is caused by an error
* @param mixed Return a XOOPS Token
* @param mixed If provide, the client will be redirected to given URL
*/
function response($msg, $error = 1, $tk = 0, $url = ''){
    global $xoopsSecurity;
    
    $ret = array(
        'message'   => $msg,
        'error'     => $error,
        'token'     => $tk ? $xoopsSecurity->createToken() : '',
        'url'       => $url
    );
    
    echo json_encode($ret);
    die();
    
}

if(!$xoopsSecurity->check())
    response(__('Session token not valid!','dtransport'), 1, 0, DT_URL);

$item = rmc_server_var($_POST, 'item', 0);
$rate = rmc_server_var($_POST, 'rate', 0);
$uid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$ip = $dtFunc->ip();

// Pueden votar usuarios anónimos?
if($uid<=0 && $xoopsModuleConfig->vote_anonymous)
    response(__('We\'re sorry. You must be logged to vote!','dtransport'), 1, 1);
    
if($rate<0)
    response(__('Given rate is not valid!', 'dtransport'),1, 0, DT_URL);

if($item<=0)
    response(__('Specified item is not valid!','dtransport'), 1, 0, DT_URL);

$item = new Dtransport_Software($item);

if($item->isNew())
    response(__('Specified item does not exists!','dtransport'), 1, 0, DT_URL);

$voted = $item->getUserVote();

if($voted===false){
    
    $sql = "INSERT INTO ".$xoopsDB->prefix("mod_dtransport_votedata")." (`uid`,`ip`,`date`,`id_soft`,`rate`) VALUES
            ($uid, '$ip',".time().",".$item->id().",$rate)";
            
} else {

    if($xoopsUser){
        $sql = "UPDATE ".$xoopsDB->prefix("mod_dtransport_votedata")." SET `date`=".time().", `rate`=$rate WHERE `uid`=$uid";
    } else {
        $sql = "UPDATE ".$xoopsDB->prefix("mod_dtransport_votedata")." SET `date`=".time().", `rate`=$rate WHERE `uid`=0 AND `ip`='$ip'";
    }
    
}

if(!$xoopsDB->queryF($sql))
    response(__('Vote could not be stored! Please try again.', 'dtransport'), 1, 1);

//$result = $xoopsDB->query("SELECT SUM(rate) as suma FROM ".$xoopsDB->prefix("mod_dtransport_votedata")." WHERE id_soft=".$item->id());
//$row = $xoopsDB->fetchArray($result);
//$rating = $row['suma'];
//list($votes) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mod_dtransport_votedata")." WHERE id_soft=".$item->id()));

// Calculate new vote value
if(false === $voted){
    $qRate = " + $rate";
} else {
    $qRate = $rate >= $voted ? " + " . ($rate - $voted) : " - " . ($voted - $rate);
}

$sql = "UPDATE ".$xoopsDB->prefix("mod_dtransport_items")." SET " . ($voted ? '' : "votes=votes+1,") . " rating=rating$qRate WHERE id_soft=".$item->id();

if ($xoopsDB->queryF($sql)){
    response($voted ? __('Thanks for update your vote!', 'dtransport') : __('Thank you for vote!','dtransport'), 0, 1);
} else{
    response(__('oops, an error ocurred! Please try again.','dtransport'), 1, 1);
}
