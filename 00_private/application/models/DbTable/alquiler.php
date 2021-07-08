<?php

class Application_Model_DbTable_alquiler extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'log_dispositivos';

	public function get($id = NULL, $limit = NULL){
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('uim' => 'alquiler'));
		$select->order('uim.uim_id DESC');
		if ($limit) $select->limit($limit);
		if ($id){$select->where("uim_id = ? ",$id);}
		
		return $this->_fetch($select);
	}

	public function insert($arreglo){
		try{
			$this->_db->insert('alquiler',$arreglo);
		return $this->_db->lastInsertId('alquiler');
		
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}

}

