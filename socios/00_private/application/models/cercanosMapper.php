<?php

class Application_Model_cercanosMapper extends Zend_Db_Table_Abstract
{

	protected $_name = 'cercanos';

	public function getCercanos($param, $paginado=null){
		$select=$this->select();
		$select->from(array('c' => 'cercanos'));

		if(isset($param['id'])){
		   $select->where('c.ce_id = ?', $param['id']);
		}
		if(isset($param['orden'])){
		   $select->order($param['orden']);
		}else{
		   $select->order('c.ce_id');
		}

		if(isset($param['buscar'])){
		   $select->where("c.ce_titulo LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.ce_direccion  LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.ce_keyword LIKE '%{$param['buscar']}%'")
		          ->orWhere("c.ce_descripcion LIKE '%{$param['buscar']}%'");
		}

		if ($paginado == 1) {
		   return $select;
		} else {
		   return $this->_fetch($select);
		}
	}

	public function get($ce_id = NULL){
        $select = $this->select();
        if (isset($ce_id)){
            {$select->where('ce_id = ?',$ce_id);}
        }
        
        /* echo $select;exit();*/
        $areaArray = $this->_fetch($select);
        if (sizeof($areaArray) > 0) 
            if (isset($ce_id))
                    return new Application_Model_DbTable_cercanos($areaArray[0]);
            else{
                    $arrcercanos = array();
                    foreach ($areaArray as $key => $value) {
                       $arrcercanos[] = new Application_Model_DbTable_cercanos($value);
                    }
                    return $arrcercanos;
                }
        else return array();
    }

	public function updateCercanos($id, $param) {

		try {
			$datos = array();
		   if(isset($param['titulo']))
		   	$datos ['ce_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['ce_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['ce_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['ce_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen']))
				$datos ['ce_imagen'] = $param['src_imagen'];
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['ce_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['ce_visitas'] = $param['visitas'];

			/*
			if (isset($param['publicacion'])) {
			    $arr_fecha = explode('/', $param['publicacion']);
			    $datos ['ce_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
			}
			*/
			$this->_db->update('cercanos', $datos, 'ce_id =' . $id);

			return "ok";
		} catch (Exception $e) {
			return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function insertCercanos($param) {
		try {
		   $datos = array();
		   if(isset($param['titulo']))
		   	$datos ['ce_titulo'] = utf8_decode($param['titulo']);
		   if(isset($param['descripcion']))
				$datos ['ce_descripcion'] = utf8_decode(trim($param['descripcion']));
		   if(isset($param['latitud']))
				$datos ['ce_googlemaps'] = ($param['latitud']!= "") ? "(".$param['latitud'].",".$param['longitud'].")" : "";
		   if(isset($param['direccion']))
				$datos ['ce_direccion'] = $param['direccion'];
		   if(isset($param['src_imagen']))
				$datos ['ce_imagen'] = $param['src_imagen'];
		   if(isset($param['categoria']))
				$datos ['cat_id'] = $param['categoria'];
		   if(isset($param['keyword']))
				$datos ['ce_keyword'] = $param['keyword'];
		   if(isset($param['visitas']))
				$datos ['ce_visitas'] = $param['visitas'];



		   $this->_db->insert('cercanos', $datos);
		   $insertid = $this->_db->lastInsertId('cercanos');

		   return $insertid;
		} catch (Exception $e) {
		   return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
		}
	}

	public function eliminarCercanos($id) {
		if ($id) {
		   try {
		       $this->_db->beginTransaction();
		       // de la tabla de beneficios
		       $deleteCore = $this->_db->delete('cercanos', 'ce_id = ' . $id);
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

