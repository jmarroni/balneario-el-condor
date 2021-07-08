<?php

class Application_Model_DbTable_servicios extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'servicios';

	public function get($limit = NULL){
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->order('ser_id DESC');
		if ($limit) $select->limit($limit);
		return $this->_fetch($select);
	}

	public function getDetalle($identificador){
		if ($identificador){
			$select = $this->select();
			$select->setIntegrityCheck(false);
			$select->where('ser_keyword = ?',$identificador);
			return $this->_fetch($select);
		}else{
			return array();
		}
	} 
	
	public function insertServicio($titulo,$descripcion,$imagenes,$nombre_contacto,$mail,$fechahora,$googlemaps,$direccion,$keyword){
		try{
			$this->insert(array("ser_titulo" => $titulo,
					"ser_descripcion" => $descripcion,
					"ser_imagenes" => $imagenes,
					"ser_nombre_contacto" => $nombre_contacto,
					"ser_mail_contacto" => $mail,
					"ser_fechahora" => $fechahora,
					"ser_googlemaps" => $googlemaps,
					"ser_direccion" => $direccion,
					"ser_keyword" => $keyword));
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}
}