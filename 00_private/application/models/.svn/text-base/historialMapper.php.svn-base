<?php

class Application_Model_historialMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'historiales';
    
    public function get($us_id = NULL, $param = array()){
    	$select = $this->select();
        if (isset($param["us_id"])){
        	$select->where("us_id = ?",$param["us_id"]);
        }
        if (isset($param["tipo"])){
        	$select->where("tu_id = ?",$param["tipo"]);
        }
        $select->setIntegrityCheck(false)
    			->from(array('lo' => 'locales'))
    			->joinInner(array("hi" => "historiales"),"lo.lo_id = hi.lo_id");
        $select->order("hi.hi_date DESC");
    	$select->limit(20);
        $arrHistorial = $this->_fetch($select);
		
        return $arrHistorial;
        
        /*
        if (sizeof($arrHistorial) > 0) 
            if (isset($us_id)||(isset($param["usuario"])&& isset($param["clave"])))
                    return new Application_Model_DbTable_historial($arrHistorial[0]);
            else{
                    $arrBudget = array();
                    foreach ($arrHistorial as $key => $value) {
                       $arrBudget[] = new Application_Model_DbTable_historial($value);
                    }
                    return $arrBudget;
                }
        
        else return array();
        */
    }
    
public function save($data,$idhistorial = NULL){
        // print_r($clasifications); echo "<br> cla_id: ".$idCla; exit();
        if (isset($idhistorial)){

            $this->_db->update('historiales', $data, "hi_id = ".historiales );            

        }else{

            $this->_db->insert('historiales', $data);
            return $this->_db->lastInsertId('historiales');

        }
    }
}