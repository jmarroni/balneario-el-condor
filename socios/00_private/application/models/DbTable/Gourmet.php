<?php

class Application_Model_DbTable_Gourmet extends Zend_Db_Table_Abstract
{

    protected $_name = 'gourmet';

   public function getGourmet($param, $paginado=null){
		$select=$this->select();
		$select->from(array('g' => 'gourmet'));

		if(isset($param['id'])){
		   $select->where('g.go_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('g.go_id');
		}

		if(isset($param['buscar'])){
		   $select->where("g.go_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("g.go_direccion LIKE '%{$param['buscar']}%'")
		          ->orWhere("g.go_keyword LIKE '%{$param['buscar']}%'")
		          ->orWhere("g.go_descripcion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateGourmet($id, $param) {

		try {
			$datos = array();
		   if(isset($param['titulo']))
		   	$datos ['go_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['go_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['go_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['go_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen'])){
		   	if(strpos($param['src_imagen'], 'http:') !==false || $param['src_imagen'] == ""){
		   		$datos ['go_imagen'] = $param['src_imagen'];
		   	}elseif(strpos($param['src_imagen'], '/') !==false){
		   		$datos ['go_imagen'] = RUTA.$param['src_imagen'];
		   	}else{
		   		$datos ['go_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   	}
		   }
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['go_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['go_visitas'] = $param['visitas'];

			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['go_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('gourmet', $datos, 'go_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertGourmet($param) {
		try {
		   $datos = array();
		   if(isset($param['titulo']))
		   	$datos ['go_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['go_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['go_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['go_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen']) && $param['src_imagen'] != "")
				$datos ['go_imagen'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['go_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['go_visitas'] = $param['visitas'];


		   $this->_db->insert('gourmet', $datos);
		   $insertid = $this->_db->lastInsertId('gourmet');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarGourmet($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('gourmet', 'go_id = ' . $id);
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

