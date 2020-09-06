<?php
// $Id: dtfeature.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_Feature extends RMObject
{

	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_features");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
		}else{
			$this->primary="nameid";
			if ($this->loadValues($id)) $this->unsetNew();
			$this->primary="id_feat";
		}	

	}

	/**
	* @desc Id del elemento a que pertenece la característica
	**/
	public function software(){
		return $this->getVar('id_soft');	
	}

	public function setSoftware($software){
		return $this->setVar('id_soft',$software);
	}

	public function title(){
		return $this->getVar('title');
	}

	public function setTitle($title){
		return $this->setVar('title',$title);
	}

	public function content($type='s'){
		return $this->getVar('content',$type);
	}

	public function setContent($content){
		return $this->setVar('content',$content);
	}

	/**
	* @desc Fecha de Creación de la característica
	**/
	public function created(){
		return $this->getVar('created');
	}

	public function setCreated($value){
		return $this->setVar('created',$value);
	}

	/**
	* @desc Fecha de modificación/creación de la característica
	**/
	public function modified(){
		return $this->getVar('modified');
	}

	public function setModified($modified){
		return $this->setVar('modified',$modified);
	}

	/**
	* @desc Nombre corto de la caracteristica
	**/
	public function nameId(){
		return $this->getVar('nameid');
	}

	public function setNameId($nameid){
		return $this->setVar('nameid',$nameid);
	}
    
    /**
    * Obtiene el enlace directo a la característica
    */
    public function permalink(){

        $dtSettings = RMSettings::module_settings('dtransport');

        if($dtSettings->permalinks)
            return DT_URL.'/feature/'.$this->id().'/';
        else
            return DT_URL.'/?s=feature&amp;id='.$this->id();
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
		return $this->deleteFromTable();
	}

}

