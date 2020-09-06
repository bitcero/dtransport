<?php
// $Id: dttag.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class DTTag extends RMObject
{

	function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_tags");
		$this->setNew();
		$this->initVarsFromTable();

		if ($id==null) return;
		
		if (is_numeric($id)){
			
			if (!$this->loadValues($id)) return;
			$this->unsetNew();
            
		}else{
			$this->primary="tagid";
			if ($this->loadValues($id)) $this->unsetNew();
			$this->primary="id_tag";
		}
	}


	public function id(){
		return $this->getVar('id_tag');
	}	

	public function tag(){
		return $this->getVar('tag');
	}

	public function setTag($tag){
		return $this->setVar('tag',$tag);
	}

    public function tagId(){
        return $this->getVar('tagid');
    }

    public function setTagId($val){
        return $this->setVar('tagid',$val);
    }

	public function hit(){
		return $this->getVar('hits');
	}

	public function setHit($hit){
		return $this->setVar('hits',$hit);
	}

    public function permalink(){

        $dtSettings = RMSettings::module_settings('dtransport');

        if($dtSettings->permalinks){
            return XOOPS_URL.'/'.trim($dtSettings->htbase, '/').'/tag/'.$this->tagId().'/';
        } else {
            return XOOPS_URL.'/modules/dtransport/index.php?p=tag&amp;id='.$this->id();
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
		return $this->deleteFromTable();
	}

}
?>
