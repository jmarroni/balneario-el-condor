<?php

class Application_Model_DbTable_novedades extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'novedades'; 

    public function get($limit = NULL, $group = NULL,$categoria = null,$excluir = null){
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array("nov" => "novedades"));
        $select->joinLeft(array("img" => "img_articulos"),"img.nov_id = nov.nov_id");
        $select->order('nov.nov_fechahora DESC');
        if ($limit) $select->limit($limit);
        if ($group)	 $select->group($group); else $select->group("nov.nov_id");
        if (isset($categoria)) $select->where('cn_id = ?',$categoria);
        if (isset($excluir)) $select->where('nov.nov_id not in (?)',$excluir);
        
        return $this->_fetch($select);
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array("nov" => "novedades"));
            $select->joinLeft(array("img" => "img_articulos"),"img.nov_id = nov.nov_id");
            if (intval($identificador)){
                $select->where('nov.nov_id = ?',$identificador);
            }else{
                $select->where('nov_keyword = ?',$identificador);
            }
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

