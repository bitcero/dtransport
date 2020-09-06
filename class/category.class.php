<?php
/**
 * D-Transport for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.eduardocortes.mx
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
 * @url          http://www.eduardocortes.mx
 */

class Dtransport_Category extends RMObject
{
		
	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		
		$this->_dbtable = $this->db->prefix("mod_dtransport_categories");
				
		$this->setNew();
		$this->initVarsFromTable();
	
		if ($id==null) return;
		
		if (!$this->loadValues($id)) return;
		$this->unsetNew();
			
	}

	public function id(){
		return $this->getVar('id_cat');
	}	

	/**
	* @desc Nombre de la categoria
	**/	
	public function name(){
		return $this->getVar('name');
	}

	public function setName($name){
		return $this->setVar('name',$name);
	}
	
	/**
	* @desc Descripcion de la categoria
	**/	
	public function desc(){
		return $this->getVar('description');
	}

	public function setDesc($desc){
		return $this->setVar('description',$desc);
	}

	/**
	* @desc Categoría padre 
	**/
	public function parent(){
		return $this->getVar('parent');
	}
	public function setParent($parent){
		return $this->setVar('parent',$parent);
	}

	/**
	* @desc Categoría activa
	**/	
	public function active(){
		return $this->getVar('active');
	}

	public function setActive($active){
		return $this->setVar('active',$active);
	}

	/**
	* @desc Nombre corto
	**/
	public function nameId(){
		return $this->getVar('nameid');
	}

	public function setNameId($nameid){
		return $this->setVar('nameid',$nameid);
	}

    public function permalink(){

        $dtSettings = RMSettings::module_settings('dtransport');

        if(!$dtSettings->permalinks){
            return XOOPS_URL.'/modules/dtransport?s=category&amp;id='.$this->id();
        }

        if($this->parent()<=0){
            return XOOPS_URL.'/'.trim($dtSettings->htbase,'/').'/category/'.$this->nameId().'/';
        }

        $func = new Dtransport_Functions();
        $path[] = $this->nameId();
        $path = array_merge($path, $func->category_path($this->parent()));

        $path = array_reverse($path, true);
        return XOOPS_URL.'/'.trim($dtSettings->htbase,'/').'/category/'.implode("/", $path).'/';

    }
	

	public function save(){
		if ($this->isNew()){
			return $this->saveToTable();
		}
		else{
			return $this->updateTable();
		}		

	}

	public function delete(){
        
        $this->db->queryF("UPDATE ".$this->_dbtable." SET parent=".$this->parent()." WHERE parent=".$this->id());
        
		return $this->deleteFromTable();
	}

}
