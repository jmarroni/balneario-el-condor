<?php

class Application_Model_DbTable_hospedajeimagenes extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'hospedaje_imagenes'; 

    public function get($ho_id = NULL,$limit = NULL){
        $select = $this->select();
        if ($ho_id) $select->where("ho_id = ?",$ho_id);
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    
}

