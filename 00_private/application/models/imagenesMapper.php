<?php

class Application_Model_imagenesMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'imagenes';
    
    public function get($lo_id = NULL, $param = array()){
    	$select = $this->select();
    	
        if (isset($lo_id)) $select->where("lo_id = ?",$lo_id);
        $arrImagenes = $this->_fetch($select);

        if (sizeof($arrImagenes) > 0) 
            if (sizeof($arrImagenes) == 1)
                    return new Application_Model_DbTable_imagenes($arrImagenes[0]);
            else{
                    $arrImg = array();
                    foreach ($arrImagenes as $key => $value) {
                       $arrImg[] = new Application_Model_DbTable_imagenes($value);
                    }
                    return $arrImg;
                }
        else return array();
    }
}