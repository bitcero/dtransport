<?php
// $Id: dtstatistics.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Módulo para la administración de descargas
// http://www.eduardocortes.mx
// http://www.exmsystem.net
// --------------------------------------------
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License as
// published by the Free Software Foundation; either version 2 of
// the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public
// License along with this program; if not, write to the Free
// Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
// MA 02111-1307 USA
// --------------------------------------------------------------
// @copyright: 2007 - 2008 Red México

/**
* @desc Clase para el manejo de estadísticas
*/
class DTStatistics extends RMObject
{
	function __construct($id = null){
		$this->db =& XoopsDatabaseFactory::getDatabaseConnection();
		$this->_dbtable = $this->db->prefix("mod_dtransport_downs");
		$this->setNew();
		$this->initVarsFromTable();
		
		if ($id==null) return;
		
		if ($this->loadValues($id)) $this->unsetNew();
		
	}
	
	public function uid(){
		return $this->getVar('uid');
	}
	public function setUid($value){
		return $this->setVar('uid', $value);
	}
	
	public function software(){
		return $this->getVar('id_soft');
	}
	public function setSoftware($value){
		return $this->setVar('id_soft', $value);
	}
	
	public function hits(){
		return $this->getVar('downs');
	}
	public function setHits($value){
		return $this->setVar('downs', $value);
	}
	
	public function ip(){
		return $this->getVar('ip');
	}
	public function setIp($value){
		return $this->setVar('ip', $value);
	}
	
	public function date(){
		return $this->getVar('date');
	}
	public function setDate($value){
		return $this->setVar('date', $value);
	}
	
	public function file(){
		return $this->getVar('id_file');
	}
	public function setFile($value){
		return $this->setVar('id_file', $value);
	}
	
}

?>