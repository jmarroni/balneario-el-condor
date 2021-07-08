<?php

class Application_Model_DbTable_Clasificados extends Zend_Db_Table_Abstract
{

	protected $_name = 'clasificados';

	public function getClasificados($param, $paginado=null){
		$select=$this->select();
		$select->from(array('c' => 'clasificados'));

		if(isset($param['id'])){
		   $select->where('c.cla_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('c.cla_id');
		}

		if(isset($param['buscar'])){
         $select->where("c.cla_titulo LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cla_nombre_contacto LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cla_fechahora LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cla_keyword LIKE '%{$param['buscar']}%'")
                ->orWhere("c.cla_descripcion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function updateClasificados($id, $param) {

		try {
			$datos = array();
			$datos ['cla_titulo'] = utf8_decode($param['titulo']);
			$datos ['cla_nombre_contacto'] = utf8_decode($param['nombre']);
			$datos ['cla_descripcion'] = utf8_decode(trim($param['descripcion']));
			$datos ['cla_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
         $datos ['cla_direccion'] = $param['direccion'];
         $datos ['cla_mail_contacto'] = $param['mail'];
         $datos ['cla_keyword'] = $param['keyword'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['cla_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

			$this->_db->update('clasificados', $datos, 'cla_id =' . $id);

			return "ok";
		} catch (Exclaption $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertClasificados($param) {
		try {
		   $datos = array();
			$datos ['cla_titulo'] = utf8_decode($param['titulo']);
			$datos ['cla_nombre_contacto'] = utf8_decode($param['nombre']);
			$datos ['cla_descripcion'] = utf8_decode(trim($param['descripcion']));
			$datos ['cla_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
         $datos ['cla_direccion'] = $param['direccion'];
         $datos ['cla_mail_contacto'] = $param['mail'];
         $datos ['cla_keyword'] = $param['keyword'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['cla_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

		   $this->_db->insert('clasificados', $datos);
		   $insertid = $this->_db->lastInsertId('clasificados');

		   return $insertid;
		} catch (Exclaption $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarClasificados($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios

		       $deleteCore = $this->_db->delete('clasificados', 'cla_id = ' . $id);
		       $deleteCore2 = $this->_db->delete('img_articulos', 'cla_id = ' . $id);

		       $this->_db->commit();
		   } catch (Exclaption $e) {
		       $this->_db->rollBack();
		       echo "Mensaje de Error: " . $e->getMessage();
		   }
		} else {
		   echo "Mensaje de Error: falta id";
		}
	}

}

