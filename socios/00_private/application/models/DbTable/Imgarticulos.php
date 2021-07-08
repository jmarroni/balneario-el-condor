<?php

class Application_Model_DbTable_Imgarticulos extends Zend_Db_Table_Abstract
{

    protected $_name = 'img_articulos';

   public function getImg($param, $paginado=null){
		$select=$this->select();
		$select->from(array('i' => 'img_articulos'));

		if(isset($param['id'])){
		   $select->where('i.img_id = ?', $param['id']);
		}
		if(isset($param['clasificados'])){
		   $select->where('i.cla_id = ?', $param['clasificados']);
		}
		if(isset($param['novedades'])){
		   $select->where('i.nov_id = ?', $param['novedades']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('i.img_id');
		}

		if(isset($param['buscar'])){
		   $select->where("i.img_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("i.img_copete  LIKE '%{$param['buscar']}%'")
		          ->orWhere("i.img_descripcion LIKE '%{$param['buscar']}%'")
		          ->orWhere("i.img_fechahora LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateImg($id, $param) {

		try {
			$datos = array();
			if(isset($param['nombre'])){
		   	if(strpos($param['nombre'], 'http:') !==false || $param['nombre'] == ""){
		   		$datos ['img_nombre'] = $param['nombre'];
		   	}elseif(strpos($param['nombre'], '/') !==false){
		   		$datos ['img_nombre'] = RUTA.$param['nombre'];
		   	}else{
		   		$datos ['img_nombre'] = RUTA_IMG."thumb_".$param['nombre'];
		   	}
		   }
			if(isset($param['cla_id']))
				$datos ['cla_id'] = $param['cla_id'];
			if(isset($param['nov_id']))
				$datos ['nov_id'] = $param['nov_id'];

			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['img_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('img_articulos', $datos, 'img_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertImg($param) {
		try {
		   $datos = array();
			if(isset($param['nombre']) && $param['nombre'] != "")
				$datos ['img_nombre'] = RUTA_IMG.$param['nombre'];
			if(isset($param['cla_id']))
				$datos ['cla_id'] = $param['cla_id'];
			if(isset($param['nov_id']))
				$datos ['nov_id'] = $param['nov_id'];


		   $this->_db->insert('img_articulos', $datos);
		   $insertid = $this->_db->lastInsertId('img_articulos');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarImg($id =NULL,$nov_id=NULL) {
		if (isset($id)) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('img_articulos', 'img_id = ' . $id);
		       $this->_db->commit();
		   } catch (Exception $e) {
		       $this->_db->rollBack();
		       echo "Mensaje de Error: " . $e->getMessage();
		   }
		} 

		if (isset($nov_id)){
			try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('img_articulos', 'nov_id = ' . $nov_id);
		       $this->_db->commit();
		   } catch (Exception $e) {
		       $this->_db->rollBack();
		       echo "Mensaje de Error: " . $e->getMessage();
		   }
		}
	}
}

