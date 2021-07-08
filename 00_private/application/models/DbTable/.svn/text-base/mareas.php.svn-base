<?php

class Application_Model_DbTable_mareas extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'mareas'; 

    public function get($localidad =null, $fecha = null, $limit = NULL){
        $select = $this->select();
        $select->order('ma_fecha');
        if ($limit) $select->limit($limit);
        if ($fecha) $select->where('ma_fecha >= ?',$fecha);
        if ($localidad) $select->where("ma_localidad = ?", $localidad);
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->where('ma_fecha = ?',$identificador);
             if ($localidad) $select->where("ma_localidad = ?", $localidad);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

