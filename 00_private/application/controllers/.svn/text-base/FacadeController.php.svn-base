<?php
class FacadeController extends Zend_Controller_Action
{
	private $latitud;
	private $longitud;
	private $limit;
	private $offset;
	private $request;
	private $usuario;
	
    public function init(){
    	$this->latitud = $this->_getParam("latitud"); 
    	$this->longitud = $this->_getParam("longitud"); 
    	$this->limit = $this->_getParam("limit"); 
    	$this->offset = $this->_getParam("offset"); 
    	$this->usuario = $this->_getParam("usuario");
    	$this->usuario = (isset($this->usuario))?$this->usuario:$_COOKIE["usuario_id"];
    	header('Content-Type: application/json');
    }

    
    public function saveperfilAction(){
    	$params = $this->_getAllParams(); 
    	$objUsuarios = new Application_Model_usuariosMapper();
    	$clave = rand(111111,999999);
    	$varUsuarios = $objUsuarios->save(
    				array(	"us_id" => $this->usuario,
    						"us_nombre" => $params["nombre"],
    						"us_apellido" => $params["apellido"],
    						"us_apodo" => $params["mail"],
    						"us_sexo" => $params["sexo"],
    						"us_telefono" =>$params["telefono"] ,
    						"us_clave" => sha1($clave)
    				),$this->usuario);
    	
    	$objUbicacion = new Application_Model_ubicacionMapper();
    	if ($params["direccion_trabajo"] != ""){
    		$objUbicacion->save(array(
    							"ub_id" => $params["ub_id_trabajo"],
    							"ub_calle" => $params["direccion_trabajo"],
					    		"ub_altura" => "",
					    		"ub_piso" => "",
					    		"ub_depto" => "",
					    		"ub_ciudad" => $params["localidad_trabajo"],
					    		"tu_id" => 1,
    							"us_id" => $this->usuario,
					    		"ub_longitud" => $params["longitud_trabajo"],
					    		"ub_latitud" => $params["latitud_trabajo"],
					    		"ub_localidad" => $params["localidad_trabajo"],
    							"ub_provincia" => $params["provincia_trabajo"]
    							),($params["ub_id_trabajo"] != "")?$params["ub_id_trabajo"]:NULL);
    		
    		setcookie('ubicacion_trabajo',$params["latitud_trabajo"] .",". $params["longitud_trabajo"], time() + (3600 * 24),"/");
		 		    						
    	}
        if ($params["direccion_casa"] != ""){
    		$objUbicacion->save(array(
    							"ub_id" => $params["ub_id_casa"],
    							"ub_calle" => $params["direccion_casa"],
					    		"ub_altura" => "",
					    		"ub_piso" => "",
					    		"ub_depto" => "",
					    		"ub_ciudad" => $params["localidad_casa"],
					    		"tu_id" => 2,
    							"us_id" => $this->usuario,
					    		"ub_longitud" => $params["longitud_casa"],
					    		"ub_latitud" => $params["latitud_casa"],
					    		"ub_localidad" => $params["localidad_casa"],
    							"ub_provincia" => $params["provincia_casa"]
    							),($params["ub_id_casa"] != "")?$params["ub_id_casa"]:NULL);
    		setcookie('ubicacion_casa',$params["latitud_casa"] .",". $params["longitud_casa"], time() + (3600 * 24),"/");
    	}
    	//TODO parametrizar el mail
    	// mail($params["mail"], "Bienvenido a Ubbimap, te acercamos tu usuario y clave de acceso",'usuario: '.$params["mail"]." clave:".$clave);
    	$this->request = array("status" => "OK");
    	$this->output();
    	
    	
    	exit();
    }
    
    
    public function favoritoAction(){
    	$lo_id = $this->_getParam("id"); 
    	$objFavorito = new Application_Model_favoritosMapper();
    	$objFavorito->save(array("lo_id" => $lo_id,"us_id" => (isset($this->usuario)?$this->usuario:0)));
    	$this->request = array("process" => "OK");
		$this->output();
    	exit();
    }
    
	public function getfavoritoAction(){
    	$objFavorito = new Application_Model_favoritosMapper();
    	$arrFavoritos = $objFavorito->get(array("us_id" => $this->usuario));
    	for ($i = 0; $i < sizeof($arrFavoritos); $i++) {
    		$fecha = $arrFavoritos[$i]["fa_date"];
    		$arrFecha = explode("-", substr($fecha, 0 , 10));
    		if (sizeof($arrFecha) > 1){
    			$arrFavoritos[$i]["fa_date"] = $arrFecha[2]."-".$arrFecha[1]."-".$arrFecha[0];
    		}else{
    			$arrFavoritos[$i]["fa_date"] = "---";
    		}
    		
    	}

    	$this->request = array("data" => $arrFavoritos);
		$this->output();
    	exit();
    	
    }
    
    
    public function historialAction(){
    	$search = $this->_getParam("search"); 
    	$lo_id = $this->_getParam("id"); 
    	
    	$objHistorial = new Application_Model_historialMapper();
    	$historial = array("th_id" => "1",
    						"lo_id" => $lo_id,
    						"hi_date" => date("Y-m-d h:i:s"),
    						"hi_palabra" => $search,
    						"us_id" => (isset($this->usuario)?$this->usuario:0),
    						"hi_resultados" => "-1",
    						"tu_id" => $_COOKIE["posicion"]);
    	
    	$objHistorial->save($historial);
    	$this->request = array("status-historial" => "OK");
		$this->output();
    	exit();
    }
    
    public function gethistorialAction(){
    	$objHistorial = new Application_Model_historialMapper();
    	$tipo = $this->_getParam("tipo");
    	$arrHistorial = $objHistorial->get(array("us_id" => $this->usuario,"tipo" => $tipo));
    	for ($i = 0; $i < sizeof($arrHistorial); $i++) {
    		$fecha = $arrHistorial[$i]["hi_date"];
    		$arrFecha = explode("-", substr($fecha, 0 , 10));
    		$arrHistorial[$i]["hi_date"] = $arrFecha[2]."/".$arrFecha[1]."/".$arrFecha[0];
    		$arrHistorial[$i]["lo_calle"] = utf8_encode(str_replace("´", " ", $arrHistorial[$i]["lo_calle"]));
    	}
    	$fechaMinima = (isset($arrHistorial[0]["hi_date"]))?substr($arrHistorial[0]["hi_date"], 0,10):date("Y-m-d");
    	$fechaMaxima = (isset($arrHistorial[sizeof($arrHistorial) -1]["hi_date"]))?substr($arrHistorial[sizeof($arrHistorial) -1]["hi_date"], 0,10):date("Y-m-d");
    	$this->request = array("data" => $arrHistorial, "fecha_minima" => $fechaMinima, "fecha_maxima" => $fechaMaxima);
		$this->output();
    	exit();
    	
    }
    public function indexAction(){exit();}

    public function busquedaAction(){
    	
    	$param = $this->_getParam("search"); 
    	$longitud = strlen($param);
            if (substr($param, $longitud - 2, 2) == "es"){ // Solo busco singulares elimiono los plurales
    		$param = substr($param, 0, $longitud - 2);
    	}
    	if (substr($param, $longitud - 1, 1) == "s"){ // Solo busco singulares elimiono los plurales
    		$param = substr($param, 0, $longitud - 1);
    	}
    	$order = $this->_getParam("order");   	
    	$objLocalesMapper = new Application_Model_localesMapper();
    	$objLocales = $objLocalesMapper->get(NULL,array("palabra" 	=> $param,
    													"latitud" 	=> $this->latitud,
    													"longitud" 	=> $this->longitud,
    													"limit" 	=> $this->limit,
    													"offset" 	=> $this->offset,
    													"order" 	=>	(isset($order)?$order:"distancia")
    													));
    	
    	
    	$data = array();
    	$objImagenes = new Application_Model_imagenesMapper();
    	$distanciaMaxima = 10;
		for ($i = 0; $i < sizeof($objLocales); $i++) {
				$value = $objLocales[$i];
				$data[$i]["id"] =  $value->getlo_id();
				$data[$i]["nombre"] =  $value->getlo_nombre();
				$data[$i]["ditancia"] =  $value->getlo_distancia();
				$data[$i]["unidad"] =  $value->getunidad_distancia();
				$data[$i]["calle"] =  utf8_encode(str_replace("´", " ", $value->getlo_calle()));
				$data[$i]["altura"] =  $value->getlo_altura();
				$data[$i]["piso"] =  $value->getlo_piso();
				$data[$i]["oficina"] =  $value->getlo_oficina();
				$data[$i]["telefono"] =  $value->getlo_telefono1();
				$data[$i]["telefono2"] =  $value->getlo_telefono2();
				$data[$i]["telefono3"] =  $value->getlo_telefono3();
				$data[$i]["mail"] =  $value->getlo_mail();
				$data[$i]["web"] =  $value->getlo_web();
				$data[$i]["longitud"] =  $value->getlo_longitud();
				$data[$i]["latitud"] =  $value->getlo_latitud();
				$objGalleryImage = $objImagenes->get($value->getlo_id());
				if (sizeof($objGalleryImage) > 0){
					for ($j = 0; $j < sizeof($objGalleryImage); $j++) {
						$data[$i]["imagen"][$j] = $objGalleryImage->getim_url();
					}

				}else{
					$data[$i]["imagen"][0] = "/img/no-image.jpg";
				}
				if ($data[$i]["unidad"] == "mts."){
					if ($distanciaMaxima < $data[$i]["ditancia"])
						$distanciaMaxima = $data[$i]["ditancia"];
				}else{
					if ($distanciaMaxima < $data[$i]["ditancia"]*1000)
						$distanciaMaxima = $data[$i]["ditancia"]*1000;
				}
		}
		$this->request = array("data" => $data, "alcance" => $distanciaMaxima);
		$this->output();
    	exit();
    }

    private function output(){
    	$request_test = json_encode($this->request);
	    switch(json_last_error()) {
	        case JSON_ERROR_NONE:
	            $this->request["status"] = ' - Sin errores';
	        break;
	        case JSON_ERROR_DEPTH:
	            $this->request["status"] = ' - Excedido tamaño máximo de la pila';
	        break;
	        case JSON_ERROR_STATE_MISMATCH:
	            $this->request["status"] = ' - Desbordamiento de buffer o los modos no coinciden';
	        break;
	        case JSON_ERROR_CTRL_CHAR:
	            $this->request["status"] = ' - Encontrado carácter de control no esperado';
	        break;
	        case JSON_ERROR_SYNTAX:
	            $this->request["status"] = ' - Error de sintaxis, JSON mal formado';
	        break;
	        case JSON_ERROR_UTF8:
	            $this->request["status"] = ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
	        break;
	        default:
	            $this->request["status"] = ' - Error desconocido';
	        break;
	    }
	    echo 'jsonpCallback('.json_encode($this->request).')';
    	exit();
    }
    
    public function altapersonaAction(){
    	$params = $this->_getAllParams(); 
    	$objUsuarios = new Application_Model_usuariosMapper();
    	$clave = rand(111111,999999);
    	$varUsuarios = $objUsuarios->save(
    				array(	"us_id" => NULL,
    						"us_nombre" => $params["nombre"],
    						"us_apellido" => $params["apellido"],
    						"us_apodo" => $params["mail"],
    						"us_fbc_id" => $params["fbcid"],
    						"us_twitter" => "",
    						"us_sexo" => $params["sexo"],
    						"us_telefono" => $params["codigopais"].' '.$params["numero"] ,
    						"us_clave" => sha1($clave)
    				));
    	
    	$objUbicacion = new Application_Model_ubicacionMapper();
    	/* if (isset($params["direccion_trabajo"] != ""){*/
    	$georeferencia = $_COOKIE["ubicacion_georeferencia"];
    	$arrGeoreferencia = explode($georeferencia, $georeferencia);
    		$objUbicacion->save(array(
    							"ub_id" => NULL,
    							"ub_calle" => "",
					    		"ub_altura" => "",
					    		"ub_piso" => "",
					    		"ub_depto" => "",
					    		"ub_ciudad" => "",
					    		"tu_id" => 1,
					    		"us_id" => $varUsuarios,
					    		"ub_pais" => 0,
					    		"ub_longitud" => $arrGeoreferencia[1],
					    		"ub_latitud" => $arrGeoreferencia[0],
					    		"ub_localidad" => "",
    							"ub_provincia" => ""
    							));
			$objUbicacion->save(array(
    							"ub_id" => NULL,
    							"ub_calle" => "",
					    		"ub_altura" => "",
					    		"ub_piso" => "",
					    		"ub_depto" => "",
					    		"ub_ciudad" => "",
					    		"tu_id" => 2,
					    		"us_id" => $varUsuarios,
					    		"ub_pais" => 0,
					    		"ub_longitud" => $arrGeoreferencia[1],
					    		"ub_latitud" => $arrGeoreferencia[0],
					    		"ub_localidad" => "",
    							"ub_provincia" => ""
    							));
    	//TODO parametrizar el mail
		    //Envio el email
			$mail = new Zend_Mail('UTF-8');
			$this->view->usuario = $params["mail"];
			$this->view->clave = $clave;
			$html = $this->view->render("/mail/suscripcion.phtml");
			$mail->setBodyHtml($html)
			->setFrom('no-reply@ubbimap.com', 'Ubbimap.com')
			->addTo($params["mail"])
			->setSubject("Bienvenido a Ubbimap, te acercamos tu usuario y clave de acceso")
			->send();
    	$this->request = array("status" => "OK");
    	$this->output();
    }
    
    public function iniciosesionAction(){
    		$params = $this->_getAllParams(); 
    		$objUsuario = new Application_Model_usuariosMapper();
    		$Usuario = $objUsuario->get(NULL,$params);
    		if (sizeof($Usuario) > 0){
	    		$objUbicacion = new Application_Model_ubicacionMapper();
	    		$ubicaciones = $objUbicacion->get(NULL,array("us_id" => $Usuario->getus_id()));
	    		$respuesta = array("datos_usuario" => array("us_id" => $Usuario->getus_id(),
	    													"us_nombre" => $Usuario->getus_nombre(),
	    													"us_apellido" => $Usuario->getus_apellido()));
	    		for ($i = 0; $i < sizeof($ubicaciones); $i++) {
	    			$respuesta["datos_ubicacion"][$i]["latitud"] = $ubicaciones[$i]["ub_latitud"];
	    			$respuesta["datos_ubicacion"][$i]["longitud"] = $ubicaciones[$i]["ub_longitud"];
	    			$respuesta["datos_ubicacion"][$i]["tipo"] = $ubicaciones[$i]["tu_id"];
	    		}
	    		$this->request = array("status" => "OK","datos" => $respuesta );
    		}else{
    			$this->request = array("status" => "ERR","datos" => array("mensaje" => "Error usuario y clave") );
    		}
    		$this->output();
    		exit();
    }
    
    
    public function validadfbcAction(){
    		$params = $this->_getAllParams(); 
    		$objUsuario = new Application_Model_usuariosMapper();
    		$Usuario = $objUsuario->get(NULL,array("fbid" => $params["fbcid"]));
    		if (sizeof($Usuario) > 0){
	    		$objUbicacion = new Application_Model_ubicacionMapper();
	    		$ubicaciones = $objUbicacion->get(NULL,array("us_id" => $Usuario[0]->getus_id()));
	    		$respuesta = array("datos_usuario" => array("us_id" => $Usuario[0]->getus_id(),
	    													"us_nombre" => $Usuario[0]->getus_nombre(),
	    													"us_apellido" => $Usuario[0]->getus_apellido()));
	    		for ($i = 0; $i < sizeof($ubicaciones); $i++) {
	    			$respuesta["datos_ubicacion"][$i]["latitud"] = $ubicaciones[$i]["ub_latitud"];
	    			$respuesta["datos_ubicacion"][$i]["longitud"] = $ubicaciones[$i]["ub_longitud"];
	    			$respuesta["datos_ubicacion"][$i]["tipo"] = $ubicaciones[$i]["tu_id"];
	    		}
	    		$respuesta["status"] = "OK";
	    		$this->request = array("datos" => $respuesta );
    		}else{
    			$this->request = array("datos" => array("status" => "ERR","mensaje" => "Error FBID Inexistente") );
    		}
    		$this->output();
    		exit();
    }
    
	public function perfilAction(){ 
		//$params = $this->_getAllParams();
		$objUsuario = new Application_Model_usuariosMapper();
		$Usuario = $objUsuario->get($this->usuario);
		
		if (sizeof($Usuario) > 0){
			$objDireccion = new Application_Model_ubicacionMapper();
			$Direccion = $objDireccion->get(NULL,array("us_id" => $this->usuario ));
			//print_r($Direccion);exit();
			$respuesta = array("us_id" => $Usuario->getus_id(),
	    					   "us_nombre" => $Usuario->getus_nombre(),
	    					   "us_apellido" => $Usuario->getus_apellido(),
							   "us_apodo" => $Usuario->getus_apodo(),
							   "us_twitter" => $Usuario->getus_twitter(),
							   "us_sexo" => $Usuario->getus_sexo(),
							   "us_telefono" => $Usuario->getus_telefono());
			
			 for ($i = 0; $i < sizeof($Direccion); $i++) {

			 		if($Direccion[$i]["tu_id"] == 1){
			 			$respuesta["direccion_trabajo"] = $Direccion[$i];
			 		}
			 		
			 		if($Direccion[$i]["tu_id"] == 2){
			 			$respuesta["direccion_casa"] = $Direccion[$i];
			 		}
				
	    	}
			
	    	$this->request = array("status" => "OK","datos" => $respuesta );
			
    	}else{
    		$this->request = array("datos" => array("status" => "ERR","mensaje" => "Error FBID Inexistente") );
    	}
    	$this->output();
    	exit();
	}
	
	
	public function pruebamailAction(){
		echo $this->view->render('/mail/suscripcion.phtml');
		exit();
	}
	


}

