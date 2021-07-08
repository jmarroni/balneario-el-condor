<?php

class Application_Model_usuariosMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'usuarios';
    
    public function get($us_id = NULL, $param = array()){
    	$select = $this->select();
        if (isset($param["usuario"])&& isset($param["clave"])){
        	$select->where("us_apodo = ?",$param["usuario"]);
        	$select->where("us_clave = ?",sha1($param["clave"]));
        }
        if (isset($param["fbid"])){
        	$select->where("us_fbc_id = ?",$param["fbid"]);
        }
    	if (isset($us_id)){
        	$select->where("us_id = ?",$us_id);
        }
        $arrUsuarios = $this->_fetch($select);
		

        
        if (sizeof($arrUsuarios) > 0) 
            if (isset($us_id)||(isset($param["usuario"])&& isset($param["clave"])))
                    return new Application_Model_DbTable_usuarios($arrUsuarios[0]);
            else{
                    $arrBudget = array();
                    foreach ($arrUsuarios as $key => $value) {
                       $arrBudget[] = new Application_Model_DbTable_usuarios($value);
                    }
                    return $arrBudget;
                }
        else return array();
    }
    
public function save($data,$idUsuarios = NULL){
        // print_r($clasifications); echo "<br> cla_id: ".$idCla; exit();
        if (isset($idUsuarios)){

            $this->_db->update('usuarios', $data, "us_id = ".$idUsuarios );            

        }else{

            $this->_db->insert('usuarios', $data);
            return $this->_db->lastInsertId('usuarios');

        }
    }
}