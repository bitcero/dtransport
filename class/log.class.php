<?php
// $Id: dtlog.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_Log extends RMObject
{

	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_logs");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
		}	

	}

	public function id(){
		return $this->getVar('id_log');
	}

	/**
	* @desc Id del elemento al que pertenece el log
	**/
	public function software(){
		return $this->getVar('id_soft');
	}

	public function setSoftware($software){
		return $this->setVar('id_soft',$software);
	}

	/**
	* @desc Titulo de log
	**/
	public function title(){
		return $this->getVar('title');
	}

	public function setTitle($title){
		return $this->setVar('title',$title);
	}

	/**
	* @desc Contenido del log
	**/
	public function log($type='s'){
		return $this->getVar('log', $type);
	}

	public function setLog($log){
		return $this->setVar('log',$log);
	}
	
	/**
	* @desc Fecha de creacion del log
	**/
	public function date(){
		return $this->getVar('date');
	}
		
	public function setDate($date){
		return $this->setVar('date',$date);
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
