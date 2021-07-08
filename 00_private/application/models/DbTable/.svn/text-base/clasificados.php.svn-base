<?php

class Application_Model_DbTable_clasificados extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'log_dispositivos';

	
	public function updateVisitas($id, $param) {


			$this->_db->update('clasificados', $param, 'cla_id =' . $id);

			return "ok";
		
	}
	public function get($tipo = NULL, $limit = NULL){
		$select = $this->select();
		$select->setIntegrityCheck(false);
		$select->from(array('cla' => 'clasificados'));
		$select->joinLeft (array('cla_img' => 'clasificado_imagenes'), 'cla.cla_id=cla_img.cla_id');
		$select->order('cla.cla_id DESC');
		if ($limit) $select->limit($limit);
		if ($tipo){
			$select->where("cla_categoria = ? ",$tipo);
			$select->where("cla_titulo <> 'Alquiler de Castillo Inflable'");
			
		} 
		$select->order("cla_visitas DESC");
		return $this->_fetch($select);
	}

	public function getDetalle($keyword=NULL,$id=NULL){
		if (isset($keyword) || isset($id)){
			$select = $this->select();
			$select->setIntegrityCheck(false);
			$select->from(array('cla' => 'clasificados'));
			$select->joinLeft (array('cla_img' => 'clasificado_imagenes'), 'cla.cla_id=cla_img.cla_id');
			if (isset($id)) $select->where('cla.cla_id = ?', $id);
			if (isset($keyword)) $select->where('cla.cla_keyword = ?', $keyword);

			//$select->where('cla_keyword = ?',$identificador);
			return $this->_fetch($select);
		}else{
			return array();
		}
	}

	public function insertClasificado($titulo,$descripcion,$nombre_contacto,$mail,$fechahora,$googlemaps,$direccion,$keyword,$categoria, $video){
		try{
			/*print_r(array("cla_titulo" => $titulo,
				"cla_descripcion" => $descripcion,
				"cla_nombre_contacto" => $nombre_contacto,
				"cla_mail_contacto" => $mail,
				"cla_fechahora" => $fechahora,
				"cla_googlemaps" => $googlemaps,
				"cla_direccion" => $direccion,
				"cla_keyword" => $keyword,
				'cla_categoria'=>$categoria,
				'cla_video'=>$video));exit(); */
		$this->_db->insert('clasificados',array("cla_titulo" => $titulo,
				"cla_descripcion" => $descripcion,
				"cla_nombre_contacto" => $nombre_contacto,
				"cla_mail_contacto" => $mail,
				"cla_fechahora" => $fechahora,
				"cla_googlemaps" => $googlemaps,
				"cla_direccion" => $direccion,
				"cla_keyword" => $keyword,
				'cla_categoria'=>$categoria,
				'cla_video'=>$video));
		return $this->_db->lastInsertId('clasificados');
		
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}

}

