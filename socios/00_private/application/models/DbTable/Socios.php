<?php

class Application_Model_DbTable_Socios extends Zend_Db_Table_Abstract
{


   protected $_name = 'socios';

   public function get($param = null, $paginado=null){
     $select=$this->select();
     
     if (isset($param["id"])){
        $select->where("IdSocio = ?",$param["id"]);
     }

     if ($paginado == 1) {
         return $select;
     } else {
         return $this->_fetch($select);
     }
   }




   public function insertNovedades($param) {
     try {
         $datos = array();
         $datos ['nov_titulo'] = utf8_decode(str_replace("–", "-", $param['titulo']));
         $datos ['nov_video'] = $param['nov_video'];
         $datos ['nov_descripcion'] = utf8_decode(str_replace("–", "-", $param['descripcion']));
         ///$datos ['nov_imagenes'] = $param['src_imagen'];
         $datos ['nov_keyword'] = $param['keyword'];

         /*
         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['nov_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }
        */
         $datos ['nov_fechahora'] = date("Y-m-d h:i:s");
         $datos ['nov_visitas'] = 0;

         $this->_db->insert('novedades', $datos);
         $insertid = $this->_db->lastInsertId('novedades');

         return $insertid;
     } catch (Exception $e) {
         echo "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
         exit();
     }
   }


   public function eliminarNovedades($id) {
     if ($id) {
         try {
             $this->_db->beginTransaction();
             // de la tabla de beneficios
             $deleteCore = $this->_db->delete('novedades', 'nov_id = ' . $id);
               $deleteCore2 = $this->_db->delete('img_articulos', 'nov_id = ' . $id);
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

