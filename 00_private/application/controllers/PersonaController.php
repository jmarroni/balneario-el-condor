<?php

class PersonaController extends Zend_Controller_Action
{
    public function init()
    {
        
    }

    public function formAction(){
    	$datos = $this->_getParam("datos");
    	
    	$this->view->mail = "";
    	$this->view->nombre = "";
    	$this->view->apellido = "";
    	$this->view->ciudad = "";
    	$this->view->provincia = "";
    	$this->view->pais = "";
    	$this->view->sexo = "";
    	$this->view->id = "";
    	if ($datos ==  "fbcid"){
    		$cookieFbc = $_COOKIE["datos_facebook"];
    		//echo $_COOKIE["datos_facebook"];
    		//setcookie("datos_facebook","",time(),"/"); // Elimino la cookie una vez obtenido los datos
    		$arrFacebookData = explode(",", $cookieFbc);
    		$this->view->mail = $arrFacebookData[0];
	    	$this->view->nombre = $arrFacebookData[1];
	    	$this->view->apellido = $arrFacebookData[2];
	    	$this->view->ciudad = $arrFacebookData[3];
	    	$this->view->provincia = $arrFacebookData[4];
	    	$this->view->pais = "Argentina";
	    	$this->view->sexo = ($arrFacebookData[5] == "male")?"Masculino":"Femenino";
    		$this->view->id = $arrFacebookData[6];
    	//	print_r($arrFacebookData);
    		//exit();
    	}
    
    }
	public function logAction(){ }
	public function newuserAction(){
		$datos = $this->_getParam("datos");
		$this->view->datos = $datos;
	}
	public function altaAction(){
		echo "alta de una persona";
	
	}
	
	public function georeferenciaAction(){
		
	}
	
	public function perfilAction(){
		$objUsuario = new Application_Model_usuariosMapper();
		$arrUsuario = $objUsuario->get($_COOKIE["usuario_id"]);
		$this->view->arrUsuario = $arrUsuario;
		
		$objDireccion = new Application_Model_ubicacionMapper();
		$arrDireccion = $objDireccion->get(NULL,array("us_id" => $_COOKIE["usuario_id"]));
		$this->view->arrDireccion = $arrDireccion;
		

		$this->_helper->layout->disableLayout();
	}
	public function cerrarsesionAction(){
		foreach ($_COOKIE as $key => $value) {
			setcookie($key,"",time(),"/");
		}
		$this->_redirect("/");
	}
}
