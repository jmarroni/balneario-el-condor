<?php

class Application_Model_DbTable_imagenes extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'imagenes'; 

    public function get($limit = NULL,$where = NULL){
        $select = $this->select();
        $select->order('im_fecha DESC');
        $select->order('im_visitas DESC');
        if ($where != NULL) $select->where($where);

        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->where('im_keyword = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    public function getAutores(){
         $select = $this->select();
         $select->group('im_titulo');
         return $this->_fetch($select);
    }

}

