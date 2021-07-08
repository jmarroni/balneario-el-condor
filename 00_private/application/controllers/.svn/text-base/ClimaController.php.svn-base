<?php

class ClimaController extends Zend_Controller_Action
{
    
    public function init()
    {
        
    }

    
    
    public function jsonactualAction(){
    	header('Content-Type: application/json');
    	$clave = $this->_getParam("clave");
	    if ($clave == "130702uade"){
	    	$clima = new Application_Model_DbTable_clima();
	    	$arrClima = $clima->get(1);
	    	$respuesta = array("temperatura" => $arrClima[0]["temp_c"],
	    						"viento" => $arrClima[0]["viento"],
	    						"condicion" => $arrClima[0]["condicion"],
	    						"icono" => str_replace("/css/images", "", $arrClima[0]["icono"]));
	    	echo 'jsonpCallback('.json_encode($respuesta).')';
	    	exit();
	    }
    }
    
    
}
?>

		
