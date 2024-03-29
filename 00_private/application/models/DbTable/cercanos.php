<?php

class Application_Model_DbTable_cercanos extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'cercanos'; 

    public function get($limit = NULL){
        $select = $this->select();
        $select->order('ce_visitas DESC');
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            if (intval($identificador)){
                $select->where('ce_id = ?',$identificador);
            }else $select->where('ce_keyword = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

