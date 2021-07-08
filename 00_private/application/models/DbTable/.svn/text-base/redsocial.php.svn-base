<?php

class Application_Model_DbTable_redsocial extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'feeds'; 

    public function get($limit = NULL,$fe_from = NULL){
        $select = $this->select();
        $select->order('fe_id DESC');
        if ($limit) $select->limit($limit);
        if ($fe_from) $select->where("fe_from = ?",$fe_from);
        return $this->_fetch($select);
    }   
}

