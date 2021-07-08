<?php

class Application_Model_DbTable_hospedaje extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'hospedaje'; 

    public function get($limit = NULL,$tipo = NULL){
        $select = $this->select();
        $select->order('ho_visitas DESC');
        if ($limit) $select->limit($limit);
        if ($tipo) $select->where("ho_tipo = ?",$tipo);
        $hospedajes =  $this->_fetch($select);
        $hospedajeImagenes = new Application_Model_DbTable_hospedajeimagenes();
        
        for ($i = 0; $i < sizeof($hospedajes); $i++) {
        	$hospedajes[$i]["imagen"] = $hospedajeImagenes->get($hospedajes[$i]["ho_id"],1);
        }
        return $hospedajes;
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->where('ho_keyword = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

