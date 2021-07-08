<?php

class Application_Model_DbTable_Imagenes extends Zend_Db_Table_Abstract
{

	protected $_name = 'imagenes';

	public function getImagenes($param, $paginado=null){
		$select=$this->select();
		$select->from(array('i' => 'imagenes'));

		if(isset($param['id'])){
		   $select->where('i.im_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('i.im_id');
		}

		if(isset($param['buscar'])){
         $select->where("i.im_titulo LIKE '%{$param['buscar']}%'")
                ->orWhere("i.im_keyword LIKE '%{$param['buscar']}%'")
                ->orWhere("i.im_fecha LIKE '%{$param['buscar']}%'")
                ->orWhere("i.im_descripcion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateImagenes($id, $param) {

		try {
			$datos = array();
			if(isset($param['titulo']))
		   	$datos ['im_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['im_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['src_imagen'])){
		   	if(strpos($param['src_imagen'], 'http:') !==false || $param['src_imagen'] == ""){
		   		$datos ['im_imagen'] = $param['src_imagen'];
		   	}elseif(strpos($param['src_imagen'], '/') !==false){
		   		$datos ['im_imagen'] = RUTA.$param['src_imagen'];
		   	}else{
		   		$datos ['im_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   	}
		   }
		   if(isset($param['keyword']))
				$datos ['im_keyword'] = $param['keyword'];


			if (isset($param['fecha'])) {
			    $arr_fecha = explode('/', $param['fecha']);
			    $datos ['im_fecha'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}

			$this->_db->update('imagenes', $datos, 'im_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertImagenes($param) {
		try {
		   $datos = array();
			if(isset($param['titulo']))
		   	$datos ['im_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['im_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['src_imagen']) && $param['src_imagen'] != "")
				$datos ['im_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   if(isset($param['keyword']))
				$datos ['im_keyword'] = $param['keyword'];


			if (isset($param['fecha'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['im_fecha'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}


		   $this->_db->insert('imagenes', $datos);
		   $insertid = $this->_db->lastInsertId('imagenes');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarImagenes($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('imagenes', 'im_id = ' . $id);
		       $this->_db->commit();
		   } catch (Exception $e) {
		       $this->_db->rollBack();
		       echo "Mensaje de Error: " . $e->getMessage();
		   }
		} else {
		   echo "Mensaje de Error: falta id";
		}
	}

}

