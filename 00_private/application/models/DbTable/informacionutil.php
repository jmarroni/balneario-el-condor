<?php

 class Application_Model_DbTable_informacionutil extends Zend_Db_Table_Abstract{

	protected $_name = 'informacionutil';

	public function getList($filtro = ""){
	   
	         	$select = $this->select();
	         	$select->order("orden");
	            return $this->_fetch($select);
	}
		
	public function GetInformacion($paramClients){
		$select = $this->select();
		$select->where('id_menu = ?',$paramClients);
		return $this->_fetch($select);
	}
	
	public function insert(array $param){
		$this->_db->insert('informacionutil',$param);
		return $this->_db->lastInsertId('menu');
	}
	
	public function delete($param){
		$this->_db->delete('menu',"idinformacionutil = ".$param["idinformacionutil"]);
	}
	public function update(array $param,$where ){
		$this->_db->update('informacionutil',$param,"id_menu = ".$param["idinformacionutil"]);
	}

	
}
