<?php

class Application_Model_ubicacionMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'ubicaciones';
    
    public function get($ub_id = NULL, $param = array()){
    	$select = $this->select();
    	
    	if (isset($param["us_id"])){ $select->where("us_id = ?",$param["us_id"]);}
        $arrUbicacion = $this->_fetch($select);

        if (sizeof($arrUbicacion) > 0) 
            if (isset($ub_id))
                   // return new Application_Model_DbTable_ubicacion($arrUbicacion[0]);
                   return $arrUbicacion;
            else{
                    $arrBudget = array();
                    /*
                    foreach ($arrUbicacion as $key => $value) {
                    	print_r($value);
                       $arrBudget[] = new Application_Model_DbTable_ubicacion($value);
                    }
                    print_r($arrBudget);
                    return $arrBudget;*/
                    return $arrUbicacion;
                }
        else return array();
    }
    
public function save($data,$idUbicacion = NULL){
        // print_r($clasifications); echo "<br> cla_id: ".$idCla; exit();
        if (isset($idUbicacion)){

            $this->_db->update('ubicaciones', $data, "ub_id = ".$idUbicacion );            

        }else{

            $this->_db->insert('ubicaciones', $data);

        }
    }
}