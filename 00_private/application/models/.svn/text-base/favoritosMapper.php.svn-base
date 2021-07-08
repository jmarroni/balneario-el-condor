<?php

class Application_Model_favoritosMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'favoritos';
    
    public function get($us_id = NULL, $param = array()){
    	$select = $this->select();
        if (isset($param["us_id"])){
        	$select->where("us_id = ?",$param["us_id"]);
        }
        
        $select->setIntegrityCheck(false)
    			->from(array('lo' => 'locales'))
    			->joinInner(array("fv" => "favoritos"),"lo.lo_id = fv.lo_id");
        $select->order("fa_date DESC");
        $select->group("lo.lo_id");
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
    
public function save($data,$idfavorito = NULL){
        // print_r($clasifications); echo "<br> cla_id: ".$idCla; exit();
        if (isset($idfavorito)){
			
            $this->_db->update('favoritos', $data, "fa_id = ".$idfavorito );            

        }else{
$data["fa_date"] = date("Y-m-d h:i:s");
            $this->_db->insert('favoritos', $data);
            return $this->_db->lastInsertId('favoritos');

        }
    }
}