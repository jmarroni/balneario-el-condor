<?php

class Application_Model_DbTable_Categorias extends Zend_Db_Table_Abstract
{

    protected $_name = 'categoria';

   public function getCategorias($param, $paginado=null){
		$select=$this->select();
		$select->from(array('c' => 'categoria'));

		if(isset($param['id'])){
		   $select->where('c.cat_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('c.cat_id');
		}

		if(isset($param['buscar'])){
		   $select->where("c.cat_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.cat_copete  LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.cat_descripcion LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.cat_fechahora LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateCategorias($id, $param) {

		try {
			$datos = array();
			$datos ['cat_nombre'] = utf8_decode($param['titulo']);

			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['cat_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('categoria', $datos, 'cat_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertCategorias($param) {
		try {
		   $datos = array();
		   $datos ['cat_nombre'] = utf8_decode($param['titulo']);


		   $this->_db->insert('categoria', $datos);
		   $insertid = $this->_db->lastInsertId('categorias');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarCategorias($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('categoria', 'cat_id = ' . $id);
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

