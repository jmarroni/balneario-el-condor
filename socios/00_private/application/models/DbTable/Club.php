<?php

class Application_Model_DbTable_Club extends Zend_Db_Table_Abstract
{

	protected $_name = 'clubdeamigos';

	public function getClub($param, $paginado=null){
		$select=$this->select();
		$select->from(array('c' => 'clubdeamigos'));

		if(isset($param['id'])){
		   $select->where('c.cl_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('c.cl_id');
		}

		if(isset($param['buscar'])){
         $select->where("c.cl_titulo LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cl_fechahora LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cl_keyword LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cl_descripcion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateClub($id, $param) {

		try {
			$datos = array();
		   if(isset($param['titulo']))
		   	$datos ['cl_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['cl_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['src_imagen'])){
		   	if(strpos($param['src_imagen'], 'http:') !==false || $param['src_imagen'] == ""){
		   		$datos ['cl_imagenes'] = $param['src_imagen'];
		   	}elseif(strpos($param['src_imagen'], '/') !==false){
		   		$datos ['cl_imagenes'] = RUTA.$param['src_imagen'];
		   	}else{
		   		$datos ['cl_imagenes'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   	}
		   }
		   if(isset($param['keyword']))
				$datos ['cl_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['cl_visitas'] = $param['visitas'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['cl_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

			$this->_db->update('clubdeamigos', $datos, 'cl_id =' . $id);

			return "ok";
		} catch (Exclption $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertClub($param) {
		try {
		   $datos = array();
		   if(isset($param['titulo']))
		   	$datos ['cl_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['cl_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['src_imagen']) && $param['src_imagen'] != "")
				$datos ['cl_imagenes'] = RUTA_IMG."thumb_".$param['src_imagen'];
		   if(isset($param['keyword']))
				$datos ['cl_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['cl_visitas'] = $param['visitas'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['cl_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

		   $this->_db->insert('clubdeamigos', $datos);
		   $insertid = $this->_db->lastInsertId('clubdeamigos');

		   return $insertid;
		} catch (Exclption $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarClub($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('clubdeamigos', 'cl_id = ' . $id);
		       $this->_db->commit();
		   } catch (Exclption $e) {
		       $this->_db->rollBack();
		       echo "Mensaje de Error: " . $e->getMessage();
		   }
		} else {
		   echo "Mensaje de Error: falta id";
		}
	}

}

