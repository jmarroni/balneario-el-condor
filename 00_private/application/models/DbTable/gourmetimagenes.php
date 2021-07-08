<?php

class Application_Model_DbTable_gourmetimagenes extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'gourmet_imagenes'; 

    public function get($go_id = NULL,$limit = NULL){
        $select = $this->select();
        if ($go_id) $select->where("go_id = ?",$go_id);
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    
}

