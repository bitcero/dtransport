<?php
// $Id: dtfilegroup.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


class Dtransport_FileGroup extends RMObject
{
	
	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_groups");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
		}	

	}
	

	public function id(){
		return $this->getVar('id_group');
	}

	/**
	* @desc Nombre del grupo
	**/
	public function name(){
		return $this->getVar('name');
	}

	public function setName($name){
		return $this->setVar('name',$name);
	}
	
	/**
	* @desc Id del elemento al que pertenece el grupo
	**/
	public function software(){
		return $this->getVar('id_soft');
	}

	public function setSoftware($software){
		return $this->setVar('id_soft',$software);
	}

	
	public function save(){
		if ($this->isNew()){
			return $this->saveToTable();
		}
		else{
			return $this->updateTable();
		}		

	}
	
	/**
	* @desc Obtiene los archivos del grupo
	* @param bool True devuelve objetos {@link Dtransport_File}
	* @return array
	*/
	public function files($obj = false){
		$sql = "SELECT * FROM ".$this->db->prefix("mod_dtransport_files")." WHERE `group`='".$this->id()."'";
		$result = $this->db->query($sql);
		$files = array();
		while ($row = $this->db->fetchArray($result)){
			if ($obj){
				$file = new Dtransport_File();
				$file->assignVars($row);
				$files[] = $file;
			} else {
				$files[] = $row;
			}
		}
		
		return $files;
	}

	public function delete(){
		
		$sql="UPDATE ".$this->db->prefix('mod_dtransport_files')." SET `group`=0 WHERE `group`=".$this->id();
		$result=$this->db->queryF($sql);

		if (!$result) return false;		

		return $this->deleteFromTable();
	}


}

