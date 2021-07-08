<?php

class Application_Model_DbTable_recetas extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'recetas'; 

    public function get($limit = NULL){
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array("rec" => "recetas"));
        $select->order('rec.re_id DESC');
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array("rec" => "recetas"));
            $select->where('re_keyword = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

