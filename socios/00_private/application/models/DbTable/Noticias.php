<?php

class Application_Model_DbTable_Noticias extends Zend_Db_Table_Abstract
{


   protected $_name = 'noticias';

   public function getNoticias($param, $paginado=null){
     $select=$this->select();
     $select->from(array('n' => 'noticias'));

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
                ->orWhere("n.no_fechahora LIKE '%{$param['buscar']}%'")
                /*->orWhere("n.no_keyword LIKE '%{$param['buscar']}%'")*/
                ->orWhere("n.no_descripcion LIKE '%{$param['buscar']}%'");
     }

     if ($paginado == 1) {
         return $select;
     } else {
         return $this->_fetch($select);
     }
   }

   public function updateNoticias($id, $param) {

     try {
         $datos = array();
         $datos ['no_titulo'] = utf8_decode(str_replace("–", "-", $param['titulo']));
         $datos ['no_descripcion'] = utf8_decode(str_replace("–", "-", $param['descripcion']));
         $datos ['no_imagenes'] = $param['src_imagen'];
         //$datos ['no_keyword'] = $param['keyword'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['no_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

         $this->_db->update('noticias', $datos, 'no_id =' . $id);

         return "ok";
     } catch (Exception $e) {
         return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
     }
   }


   public function insertNoticias($param) {
     try {
         $datos = array();
         $datos ['no_titulo'] = utf8_decode(str_replace("–", "-", $param['titulo']));
         $datos ['no_descripcion'] = utf8_decode(str_replace("–", "-", $param['descripcion']));
         $datos ['no_imagenes'] = $param['src_imagen'];
         //$datos ['no_keyword'] = $param['keyword'];

         if (isset($param['fecha'])) {
             $arr_fecha = explode('/', $param['fecha']);
             $datos ['no_fechahora'] = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
         }

         $this->_db->insert('noticias', $datos);
         $insertid = $this->_db->lastInsertId('noticias');

         return $insertid;
     } catch (Exception $e) {
         return "Ocurrio un error mientras ejecutabamos la consulta:" . $e->getMessage();
     }
   }


   public function eliminarNoticias($id) {
     if ($id) {
         try {
             $this->_db->beginTransaction();
             // de la tabla de beneficios
             $deleteCore = $this->_db->delete('noticias', 'no_id = ' . $id);
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

