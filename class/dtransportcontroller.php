<?php
/**
 * D-Transport for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

class DtransportController implements iCommentsController
{
    public function increment_comments_number($comment){
        
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $params = urldecode($comment->getVar('params'));
        parse_str($params);
        
        if(!isset($item) || $item<=0) return;
        
        $sql = "UPDATE ".$db->prefix("mod_dtransport_items")." SET comments=comments+1 WHERE id_soft=$item";
        return $db->queryF($sql);
        
    }
    
    public function reduce_comments_number($comment){
        
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $params = urldecode($comment->getVar('params'));
        parse_str($params);
        
        if(!isset($item) || $item<=0) return;
        
        $sql = "UPDATE ".$db->prefix("mod_dtransport_items")." SET comments=comments-1 WHERE id_soft=$item AND comments>0";
        $db->queryF($sql);
        
    }
    
    public function get_item($params, $com){
        static $items;
        
        $params = urldecode($params);
        parse_str($params);
        if(!isset($item) || $item<=0) return __('Not found','dtransport');;
        
        if(isset($items[$item])){
            return $items[$item]->getVar('name').' '.$items[$item]->getVar('version');
        }
        
        include_once (XOOPS_ROOT_PATH.'/modules/dtransport/class/software.class.php');
        
        $item = new Dtransport_Software($item);
        if($item->isNew()){
            return __('Not found','dtransport');
        }
        
        $items[$item->id()] = $item;
        return $item->getVar('name').' '.$item->getVar('version');
        
    }
    
    public function get_item_url($params, $com){
        static $items;
        
        $params = urldecode($params);
        parse_str($params);
        if(!isset($item) || $item<=0) return '';
        
        if(isset($items[$item])){
            $ret = $items[$item]->permalink();
            return $ret;
        }
        
        include_once (XOOPS_ROOT_PATH.'/modules/dtransport/class/software.class.php');
        
        $item = new Dtransport_Software($item);
        if($item->isNew()){
            return '';
        }
        
        $items[$item->id()] = $item;
        
        return $item->permalink();
        
    }
    
    public function get_main_link(){
        
        $dtSettings = RMSettings::module_settings('dtransport');
        
        if ($dtSettings->permalinks){
            return XOOPS_URL.$dtSettings->htbase;
        } else {
            return XOOPS_URL.'/modules/dtransport';
        }
        
    }

    /**
     * Get the license data
     * @return mixed
     */
    public function licenseData()
    {
        global $common;
        $data = $common->settings()::module_settings('dtransport', 'licenseData');
        return $data;
    }

    public static function getInstance()
    {
        static $instance;
    
        if (!isset($instance)) {
            $instance = new DtransportController();
        }
    
        return $instance;
    }

}

