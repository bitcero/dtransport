<?php
// $Id: dtfile.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_File extends RMObject
{

	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_files");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
		}

	
	}

	public function id(){
		return $this->getVar('id_file');
	}	

	/**
	* @desc Id del elemento al que pertenece el archivo
	**/
	public function software(){
		return $this->getVar('id_soft');
	}

	public function setSoftware($software){
		return $this->setVar('id_soft',$software);
	}
	
	
	/**
	* @desc Archivo para descargar. Puede ser una URL
	**/
	public function file(){
		return $this->getVar('file');
	}

	public function setFile($file){
		return $this->setVar('file',$file);
	}
	
	/**
	* @desc Indica si se trata de un archivo remoto o un archivo local
	**/
	public function remote(){
		return $this->getVar('remote');
	}

	public function setRemote($remote){

		return $this->setVar('remote',$remote);
	}

	/**
	* @desc Número de descargas del archivo.
	* Debe ser igual al campo de hits en los elementos
	**/
	public function hits(){
		return $this->getVar('hits');
	}
	
	public function setHits($hits){
		return $this->setVar('hits',$hits);
	}
	public function addHit(){
		$sql = "UPDATE ".$this->db->prefix("mod_dtransport_files")." SET hits=hits+1 WHERE id_file='".$this->id()."'";
		return $this->db->queryF($sql);
	}

	/**
	* @desc Id del grupo de archivos al que pertenece este archivo
	**/
	public function group(){
		return $this->getVar('group');	
	}

	public function setGroup($group){
		return $this->setVar('group',$group);
	}

	/**
	* @desc Archivo por defecto a descargar
	**/
	public function isDefault(){
		return $this->getVar('default');
	}

	public function setDefault($default){
		return $this->setVar('default',$default);
	}

	/**
	* @desc Tamaño del archivo
	**/
	public function size(){
		return $this->getVar('size');
	}

	public function setSize($size){
		return $this->setVar('size',$size);
	}
	
	public function date(){
		return $this->getVar('date');
	}
	public function setDate($value){
		return $this->setVar('date', $value);
	}
	
	public function mime(){
		return $this->getVar('mime');
	}
	public function setMime($value){
		return $this->setVar('mime', $value);
	}
	
	public function title(){
		$title = $this->getVar('title');
		return $title!='' ? $title : $this->getVar('file');
	}
	
	public function setTitle($title){
		return $this->setVar('title', $title);
	}

    public function permalink(){

        $dtSettings = RMSettings::module_settings('dtransport');

        if($dtSettings->permalinks){
            return XOOPS_URL.'/'.trim($dtSettings->htbase,'/').'/download/'.$this->id().'/';
        } else {
            return XOOPS_URL.'/modules/dtransport/?s=download&id='.$this->id();
        }
    }
    
	public function save(){
		if ($this->isNew()){
			return $this->saveToTable();
		}
		else{
			return $this->updateTable();
		}		

	}

	public function delete(Dtransport_Software $sw = null){
		global $xoopsModuleConfig;
        
        if($this->remote())
            return $this->deleteFromTable();
        
        if(!is_a($sw, 'Dtransport_Software'))
            $sw = new Dtransport_Software($this->software());
        
		$dtSettings = RMSettings::module_settings('dtransport');
        
        if($sw->getVar('secure'))
            unlink(rtrim($dtSettings->directory_secure,'/').'/'.$this->file());
        else
            unlink(rtrim($dtSettings->directory_insecure,'/').'/'.$this->file());
        
		return $this->deleteFromTable();
	}


}
