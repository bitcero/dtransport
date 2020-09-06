<?php
// $Id: rss.php 259 2013-11-13 21:35:24Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

load_mod_locale("dtransport");
$show = rmc_server_var($_GET,'show','all');

$xoopsModule = RMFunctions::load_module('dtransport');
$config = RMSettings::module_settings('dtransport');
include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtfunctions.class.php';
include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtsoftware.class.php';
include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtcategory.class.php';
include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtlicense.class.php';
include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtransport-platform.class.php';

$rss_channel = array();
$dtFunc = new Dtransport_Functions();

switch($show){
    case 'cat':

        $id = rmc_server_var($_GET,'cat',0);
        if ($id<=0){
            redirect_header('backend.php', 1, __('Sorry, specified category was not foud!','dtransport'));
            die();
        }
        
        $cat = new Dtransport_Category($id);
        if ($cat->isNew()){
            redirect_header('backend.php', 1, __('Sorry, specified category was not foud!','dtransport'));
            die();
        }
        
        $rss_channel['title'] = sprintf(__('%s :: Downloads in %s','dtransport'), $xoopsModule->name(), $cat->getVar('name'));
        $rss_channel['link'] = $cat->permalink();
        $rss_channel['description'] = htmlspecialchars($cat->getVar('description'), ENT_QUOTES);
        $rss_channel['lastbuild'] = formatTimestamp(time(), 'rss');
        $rss_channel['webmaster'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['editor'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['category'] = $cat->getVar('name');
        $rss_channel['generator'] = 'Common Utilities';
        $rss_channel['language'] = RMCLANG;
        
        // Get posts
        $downs = $dtFunc->get_items($id);
        $rss_items = array();
        foreach($downs as $down){
            $item = array();
            $item['title'] = $down['name'];
            $item['link'] = $down['link'];
            if($item['image']!=''){
                $image = '<img src="'.$item['image'].'" alt="'.$item['name'].'" /><br />';
            } else {
                $image = '';
            }
            $item['description'] = XoopsLocal::convert_encoding(htmlspecialchars($image.$down['description'], ENT_QUOTES));
            $item['pubdate'] = formatTimestamp($down['creation'], 'rss');
            $item['guid'] = $down['link'];
            $rss_items[] = $item;
        }
        
        break;
    
    case 'all':
    default:

        $rss_channel['title'] = $xoopsModule->name();
        $rss_channel['link'] = XOOPS_URL.($config->permalinks ? $config->basepath : '/modules/dtransport');
        $rss_channel['description'] = __('All recent published items','dtransport');
        $rss_channel['lastbuild'] = formatTimestamp(time(), 'rss');
        $rss_channel['webmaster'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['editor'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['category'] = 'Downloads';
        $rss_channel['generator'] = 'Common Utilities';
        $rss_channel['language'] = RMCLANG;

        // Get posts
        $downs = $dtFunc->get_items();
        $rss_items = array();
        foreach($downs as $down){
            $item = array();
            $item['title'] = $down['name'];
            $item['link'] = $down['link'];
            if($item['image']!=''){
                $image = '<img src="'.$item['image'].'" alt="'.$item['name'].'" /><br />';
            } else {
                $image = '';
            }
            $item['description'] = XoopsLocal::convert_encoding(htmlspecialchars($image.$down['description'], ENT_QUOTES));
            $item['pubdate'] = formatTimestamp($down['creation'], 'rss');
            $item['guid'] = $down['link'];
            $rss_items[] = $item;
        }
        
        break;
}



