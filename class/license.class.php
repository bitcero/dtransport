<?php
// $Id: dtlicense.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_License extends RMObject
{

	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_licences");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
		} else {
            
            $this->primary="nameid";
            if ($this->loadValues($id)) $this->unsetNew();
            $this->primary="id_lic";
            
        }

	}

	public function id(){
		return $this->getVar('id_lic');
	}

	public function name(){
		return $this->getVar('name');
	}

	public function setName($name){
		return $this->setVar('name',$name);
	}

    public function nameId(){
        return $this->getVar('nameid');
    }

    public function setNameId($val){
        return $this->setVar('nameid',$val);
    }

	public function link(){
		return $this->getVar('link');
	}

	public function setLink($link){
		return $this->setVar('link',$link);
	}

	public function type(){
		return $this->getVar('type');
	}

	public function setType($type){
		return $this->setVar('type',$type);
	}

    public function permalink(){

        $dtSettings = RMSettings::module_settings('dtransport');

        if($dtSettings->permalinks){
            return XOOPS_URL.'/'.trim($dtSettings->htbase, '/').'/license/'.$this->nameId().'/';
        } else {
            return XOOPS_URL.'/modules/dtransport/index.php?p=license&amp;id='.$this->id();
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

	public function delete(){

		$sql="DELETE FROM ".$this->db->prefix('mod_dtransport_licsoft')." WHERE id_lic=".$this->id();
		$result=$this->db->queryF($sql);

		if (!$result) return false;
			
		return $this->deleteFromTable();
	}


}
