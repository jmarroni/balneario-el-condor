<?php

class Application_Model_DbTable_agenda extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'agenda';

	
	public function updateVisitas($id, $param) {


			$this->_db->update('agenda', $param, 'ag_id =' . $id);

			return "ok";
		
	}
	public function get($destacado=NULL, $fecha_minima = NULL, $limit = NULL){
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('ag' => 'agenda'));
		if (isset($destacado)) $select->where('ag.ag_destacado = ?', $destacado);
		if(isset($fecha_minima)) $select->where('ag.ag_fecha >= ?', $fecha_minima);
		$select->order('ag.ag_fecha ASC');
		if (isset($limit)) $select->limit($limit);
		return $this->_fetch($select);
	}

	public function getDetalle($id=NULL){
		if (isset($id)){
			$select = $this->select();
			$select->setIntegrityCheck(false);
			$select->from(array('ag' => 'agenda'));
			if (isset($id)) $select->where('ag.ag_url_amigable = ?', $id);
			//$select->where('ag_keyword = ?',$identificador);
			return $this->_fetch($select);
		}else{
			return array();
		}
	}

	

}

