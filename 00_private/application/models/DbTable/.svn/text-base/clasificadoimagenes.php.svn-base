<?php

class Application_Model_DbTable_clasificadoimagenes extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'clasificado_imagenes';

	public function getClasImagenes($identificador){
		if ($identificador){
			$select = $this->select();
			$select->setIntegrityCheck(false);
			$select->where('cla_keyword = ?',$identificador);
			return $this->_fetch($select);
		}else{
			return array();
		}
	}
	
	public function insertClaImagen($identificador, $arrImagenes){
		try{
		if($identificador != null){
		$id = $this->insert(array(
				"cla_id" => $identificador,
				"cla_imagen0" => $arrImagenes[0],
				"cla_imagen1" => $arrImagenes[1],
				"cla_imagen2" => $arrImagenes[2],
				"cla_imagen3" => $arrImagenes[3],
				"cla_imagen4" => $arrImagenes[4]));
		}}
		catch(Zend_Db_Adapter_Exception $e){
			return "error";}
	}

	public function insertClaImg($id, $img0, $img1, $img2, $img3, $img4){
		try{
			if($id != ''){
		$this->_db->insert('clasificado_imagenes',array(
				"cla_id" => $id,
				"cla_imagen0" => $img0,
				"cla_imagen1" => $img1,
				"cla_imagen2" => $img2,
				"cla_imagen3" => $img3,
				"cla_imagen4" => $img4
			));
			return $id;
			}
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}

}