<?php

class Application_Model_DbTable_encuesta extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'encuesta'; 

    public function get($en_id, $limit = NULL){
        $select = $this->select();
        $select->where("en_id_grupo = ? ",$en_id);
        $select->where("en_comentario <> ''");
        $select->order('en_fecha DESC');
        if ($limit) $select->limit($limit);
        return $this->_fetch($select);
    }

    public function getCantidadVotos($en_id){
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array("en" => "encuesta"),array("count(en_id) as idEncuesta","en_opcion"));
        $select->order('en_fecha DESC');
        $select->where("en_id_grupo = ? ",$en_id);
        $select->group("en_opcion");
        return $this->_fetch($select);
    }

    
}

