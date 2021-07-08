<?php

class Application_Model_DbTable_Nocturnos extends Zend_Db_Table_Abstract
{

    protected $_name = 'nocturnos';

   public function getNocturnos($param, $paginado=null){
		$select=$this->select();
		$select->from(array('n' => 'nocturnos'));

		if(isset($param['id'])){
		   $select->where('n.no_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('n.no_id');
		}

		if(isset($param['buscar'])){
		   $select->where("n.no_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("n.no_descripcion LIKE '%{$param['buscar']}%'")
		          ->orWhere("n.no_keyword LIKE '%{$param['buscar']}%'")
		          ->orWhere("n.no_direccion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateNocturnos($id, $param) {

		try {
			$datos = array();
			if(isset($param['titulo']))
		   	$datos ['no_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['no_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['no_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['no_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen'])){
		   	if(strpos($param['src_imagen'], 'http:') !==false || $param['src_imagen'] == ""){
		   		$datos ['no_imagen'] = $param['src_imagen'];
		   	}elseif(strpos($param['src_imagen'], '/') !==false){
		   		$datos ['no_imagen'] = RUTA.$param['src_imagen'];
		   	}else{
		   		$datos ['no_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   	}
		   }
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['no_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['no_visitas'] = $param['visitas'];


			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['no_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('nocturnos', $datos, 'no_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertNocturnos($param) {
		try {
		   $datos = array();
			if(isset($param['titulo']))
		   	$datos ['no_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['no_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['no_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['no_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen']) && $param['src_imagen'] != "")
				$datos ['no_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['no_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['no_visitas'] = $param['visitas'];


		   $this->_db->insert('nocturnos', $datos);
		   $insertid = $this->_db->lastInsertId('nocturnos');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarNocturnos($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('nocturnos', 'no_id = ' . $id);
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

