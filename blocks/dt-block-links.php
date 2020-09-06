<?php
// $Id$
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Bloque que muestra los enlaces útiles en el módulo
*/
function dt_block_links(){
    global $xoopsTpl, $xoopsUser, $rmEvents, $rmTpl;

    //include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtfunctions.class.php';
    
    $dtFunc = new Dtransport_Functions();
    $dtSettings = RMsettings::module_settings('dtransport');
    $url = $dtSettings->permalinks ? XOOPS_URL.'/'.trim($dtSettings->htbase, "/") : XOOPS_URL.'/modules/dtransport';

    //$xoopsTpl->assign('dt_header_title', $title);

    $block = array(
        'cansubmit' => $dtFunc->canSubmit(),
        'searchlink' => $dtSettings->permalinks ? $url.'/'.trim($dtSettings->htbase, '/').'/search/' : $url.'/?p=search',
        'url'   => XOOPS_URL.'/modules/dtransport'
    );

    $block['links'][] = array(
        'title' => __('Downloads','dtransport'),
        'link'  => $url,
        'icon' => 'svg-rmcommon-cloud-download'
    );

    $block['links'][] = array(
        'title' => __('My Downloads','dtransport'),
        'link'  => $dtSettings->permalinks ? $url.'/cp/' : $url.'/?p=cpanel',
        'icon'  => 'svg-dtransport-download'
    );

    if ($dtFunc->canSubmit()){
        $block['links'][] = array(
            'title' => __('Submit Download','dtransport'),
            'link'  => $dtSettings->permalinks ? $url.'/submit/' : $url.'/?p=explore&amp;f=submit',
            'icon'  => 'svg-rmcommon-send'
        );
    }

    $block['links'][] = array(
        'title' => __('Recent Downloads','dtransport'),
        'link'  => $dtSettings->permalinks ? $url.'/recent/' : $url.'/?p=explore&amp;f=recent',
        'icon'  => 'svg-dtransport-daily'
    );

    $block['links'][] = array(
        'title' => __('Popular Downloads','dtransport'),
        'link'  => $dtSettings->permalinks ? $url.'/popular/' : $url.'/?p=explore&amp;f=popular',
        'icon'  => 'svg-dtransport-thumb-up'
    );

    $block['links'][] = array(
        'title' => __('Best Rated','dtransport'),
        'link'  => $dtSettings->permalinks ? $url.'/rated/' : $url.'/?p=explore&amp;f=rated',
        'icon'  => 'svg-rmcommon-star'
    );

    // All Dtransport blocks must provide this property
    $block['tplPath'] = XOOPS_ROOT_PATH . '/modules/dtransport/templates/sets/' . $dtSettings->tplset . '/blocks';

    $block = $rmEvents->run_event("dtransport.block.links", $block);

    $rmTpl->add_style('blocks-' . $dtSettings->tplset . '.min.css','dtransport', ['id' => 'dtransport-blocks-css']);

    return $block;
}

