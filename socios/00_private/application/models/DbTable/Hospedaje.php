<?php

class Application_Model_DbTable_Hospedaje extends Zend_Db_Table_Abstract
{

    protected $_name = 'hospedaje';


   public function getHospedaje($param, $paginado=null){
		$select=$this->select();
		$select->from(array('h' => 'hospedaje'));

		if(isset($param['id'])){
		   $select->where('h.ho_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('h.ho_id');
		}

		if(isset($param['buscar'])){
		   $select->where("h.ho_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("h.ho_descripcion LIKE '%{$param['buscar']}%'")
		          ->orWhere("h.ho_direccion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateHospedaje($id, $param) {

		try {
			$datos = array();
			if(isset($param['titulo']))
				$datos ['ho_titulo'] = utf8_decode($param['titulo']);
			if(isset($param['descripcion']))
				$datos ['ho_descripcion'] = utf8_decode(trim($param['descripcion']));
			if(isset($param['latitud']))
				$datos ['ho_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
			if(isset($param['ho_direccion']))
				$datos ['ho_direccion'] = $param['direccion'];
			if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
			if(isset($param["src_imagen"])){
		   	if(strpos($param["src_imagen"], 'http:') !==false || $param["src_imagen"] == ""){
		   		$datos ['ho_imagen'] = $param['src_imagen'];
		   	}elseif(strpos($param['src_imagen'], '/') !==false){
		   		$datos ['ho_imagen'] = RUTA.$param['src_imagen'];
		   	}else{
		   		$datos ['ho_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   	}
		   }
			if(isset($param['web']))
				$datos ['ho_web'] = $param['web'];
			if(isset($param['mail']))
				$datos ['ho_mail'] = $param['mail'];
			if(isset($param['telefono']))
				$datos ['ho_telefono'] = $param['telefono'];
		   if(isset($param['keyword']))
				$datos ['ho_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['ho_visitas'] = $param['visitas'];

			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['ho_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('hospedaje', $datos, 'ho_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertHospedaje($param) {
		try {
		   $datos = array();
			if(isset($param['titulo']))
				$datos ['ho_titulo'] = utf8_decode($param['titulo']);
			if(isset($param['descripcion']))
				$datos ['ho_descripcion'] = utf8_decode(trim($param['descripcion']));
			if(isset($param['latitud']))
				$datos ['ho_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
			if(isset($param['ho_direccion']))
				$datos ['ho_direccion'] = $param['direccion'];
			if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
			if(isset($param['src_imagen']) && $param['src_imagen'] != "")
				$datos ['ho_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
			if(isset($param['web']))
				$datos ['ho_web'] = $param['web'];
			if(isset($param['mail']))
				$datos ['ho_mail'] = $param['mail'];
			if(isset($param['telefono']))
				$datos ['ho_telefono'] = $param['telefono'];
		   if(isset($param['keyword']))
				$datos ['ho_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['ho_visitas'] = $param['visitas'];


		   $this->_db->insert('hospedaje', $datos);
		   $insertid = $this->_db->lastInsertId('hospedaje');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarHospedaje($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('hospedaje', 'ho_id = ' . $id);
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

