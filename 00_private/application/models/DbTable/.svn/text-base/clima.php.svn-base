<?php

class Application_Model_DbTable_clima extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'clima'; 

    public function get($limit = NULL){
        $select = $this->select();
        $select->order('id DESC');
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->where('id = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

