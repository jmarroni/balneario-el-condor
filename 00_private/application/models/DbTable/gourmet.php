<?php

class Application_Model_DbTable_gourmet extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'gourmet'; 

    public function get($limit = NULL){
        $select = $this->select();
        $select->order('go_visitas DESC');
        if ($limit) $select->limit($limit);
                $gourmet =  $this->_fetch($select);
        $gourmetImagenes = new Application_Model_DbTable_gourmetimagenes();
        for ($i = 0; $i < sizeof($gourmet); $i++) {
        	$gourmet[$i]["imagen"] = $gourmetImagenes->get($gourmet[$i]["go_id"],1);
        }
        return $gourmet;
    }

    public function getDetalle($identificador){
        if ($identificador){
            $select = $this->select();
            $select->where('go_keyword = ?',$identificador);
            return $this->_fetch($select);
        }else{
            return array();
        }
    }

    
}

