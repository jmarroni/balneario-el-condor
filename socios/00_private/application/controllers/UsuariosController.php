<?php

class UsuariosController extends Zend_Controller_Action
{
    public function init()
    {
    	/* Initialize action controller here */
	  	$this->_helper->layout()->setLayout('layout-admin');
		if (!Zend_Auth::getInstance()->hasIdentity()) {
         $this->_redirect('/');
         exit();}
        $ad_nombre_apellido = Zend_Auth::getInstance()->getIdentity()->ad_nombre_apellido;
		$ad_imagen = Zend_Auth::getInstance()->getIdentity()->ad_imagen;
        
		$this->view->ad_nombre_apellido = $ad_nombre_apellido;
		$this->view->ad_imagen = $ad_imagen;
        // $this->view->menu ='usuarios';

    }

    public function indexAction()
    {
    	$this->_redirect('/');
         exit();
    }

    public function newsletterAction(){
        $ne_model = new Application_Model_newsletterMapper();
        $arrSuscriptos = $ne_model->get();
        $this->view->arrSuscriptos = $arrSuscriptos;
    }

    public function encuestasAction(){
        $this->view->menu ='reportes';
        $encuestas = new Application_Model_encuestaMapper();
        $arrEncuestas = $encuestas->get();
        $this->view->arrEncuestas = $arrEncuestas;
        $objEventoMapper = new Application_Model_eventoMapper();
        $arrEventos = $objEventoMapper->get();
        $this->view->arrEventos = $arrEventos;
        $params = $this->_getAllParams();
        
        if ($params["ev"]!= '') {
            $option["evento_ev_id"] = $params["ev"];
            $us_model = new Application_Model_usuariosMapper();
            $arrUsuarios  = $us_model->get(NULL, $option);
            $this->view->arrUsuarios = $arrUsuarios;
        }

    }

    public function suscriptosAction(){
        $us_model = new Application_Model_usuariosMapper();
        $arrUsuarios  = $us_model->get();
        $this->view->arrUsuarios = $arrUsuarios;
    }
    
    public function enviarinvitacionesAction(){
        $ids = $this->_getParam('ids');
        $arrUsuarios = explode(",", $ids);
        $us_model = new Application_Model_usuariosMapper();
        $ev = $this->_getParam('ev');
        $envento = $this->_getParam('envento');
        


        $mensaje = "";
        $objWeb = new Application_Model_webMapper();
        $arrDbTableWeb = $objWeb->get(8);
        /*echo $arrDbTableWeb->getEvento_ev_id(); exit();*/
        $this->view->arrDbTableWeb =  $arrDbTableWeb;
        $objMapperEvento = new Application_Model_eventoMapper();
        $arrDbTableEvento = $objMapperEvento->get($ev);
        /*print_r($arrDbTableEvento);exit();*/
        /*echo $arrDbTableEvento[0]->getEv_contiene_citas();exit();*/
        $this->view->arrDbTableEvento = $arrDbTableEvento;
       	if ($envento == 2){
       		$usuariosGet = $us_model->get(NULL,array("evento_ev_id" => $ev));
       		$arrUsuarios = array();
       		foreach ($usuariosGet as $valueUs) {
       			$arrUsuarios[] = $valueUs->getUs_id();
       		}
       }
        foreach ($arrUsuarios as $key => $value) {
        	$clave = "";
            $us = $us_model->get($value);
            if ($arrDbTableEvento->getEv_contiene_citas() == 1){

        		$mensaje =	$arrDbTableEvento->getEv_mensaje_de_confirmacion();
            	$clave = $this->generaPass();
            	$sha_clave = sha1($clave.SEMILLA);
            	$us_model->save(array("us_clave" => $sha_clave),$us->getUs_id());
                $textFrom = "Invitación a Invertur";

             }else{
             	$mensaje =	$arrDbTableEvento->getEv_mensaje_de_invitacion();
                $textFrom = "Invitación a Turtech";
             }
	        
	         $mail = new Zend_Mail();
            $html = $this->view;
            $this->view->nombre = $us->getUs_nombre();
            $this->view->apellido = $us->getUs_apellido();
            $this->view->usuario = $us->getUs_email();
            $this->view->pass = $clave;
            $this->view->cita = $arrDbTableEvento->getEv_contiene_citas();

            $html = $this->view->render("/usuarios/mail_plataforma2.phtml");
            $html = str_replace("?", "\"",str_replace("”", "\"",str_replace("“", "\"", $html)));
           // echo $html;exit();
            $mail->setBodyHtml($html)
                ->setFrom("registracion@invertur.com.ar",utf8_decode($textFrom))
                ->addTo($us->getUs_email())
                ->setSubject("Invertur")
                ->send();
               /*echo $mensaje;*/
                $log_table = new Application_Model_logMapper();
                $log_table->save(array("lo_fecha" => date("Y-m-d h:i:s"),"Usuario_us_email" => $us->getUs_email()));
        }
        exit();

    }
    
    public function loginAction(){
        $this->view->menu ='reportes';
        $params = $this->_getAllParams();

        $option = array("order" => "login");
        if (isset($params["ev"])) {
            $option["evento_ev_id"] = $params["ev"];
        }
        if (isset($params["us"])) {
            $option["us_id"] = $params["us"];   
        }

    	$us_model = new Application_Model_usuariosMapper();
    	$this->view->usuarios = $us_model->get(NULL,$option);

        $objEventoMapper = new Application_Model_eventoMapper();
        $arrEventos = $objEventoMapper->get();
        $this->view->arrEventos = $arrEventos;
        $arrUsuarios = $us_model->get();
        $this->view->arrUsuarios = $arrUsuarios;
    }
    
    public function citasAction(){
        $this->view->menu ='reportes';

        $params = $this->_getAllParams();

        $option = array("type" => "citas");
        if (isset($params["ev"])) {
            $option["evento_ev_id"] = $params["ev"];
        }
        if (isset($params["usg"])) {
            $option["Usuarios_us_id_generador"] = $params["usg"];   
        }
        if (isset($params["usi"])) {
            $option["Usuarios_us_id_citado"] = $params["usi"];   
        }

    	$citas = new Application_Model_citasMapper();
    	$us_model = new Application_Model_usuariosMapper();
    	$arrCitas = $citas->get(NULL,$option);
        
    	$this->view->arrCitas = $arrCitas;

        $objEventoMapper = new Application_Model_eventoMapper();
        $arrEventos = $objEventoMapper->get();
        $this->view->arrEventos = $arrEventos;
        $arrUsuarios = $us_model->get();
        // print_r($arrUsuarios);
        $this->view->arrUsuarios = $arrUsuarios;
    }
    
    public function masivoAction(){
    	
    	$this->view->menu ='usuarios';
    }
    
    public function procesarmasivoAction(){
    	$archivo = $this->_getParam("archivo");
    	$file = fopen(RUTA_FIJA."/htdocs/admin/".$archivo, "r") or exit("Unable to open file!");
    	//Output a line of the file until the end is reached
    	$errores = "";
    	$i = 0;
    	$us_model = new Application_Model_usuariosMapper();
    	
    	//Verifico el archivo antes de insertar
    	while(!feof($file))
    	{
    		$linea = fgets($file);
    		if (ltrim(rtrim($linea)) != ""){
	    		$datosUsuarios = explode(",", $linea);
	    		if (sizeof($datosUsuarios) > 11 || sizeof($datosUsuarios) < 11){
	    			$errores .= "<br />Linea ".($i+1)." Error, la cantidad de campos no es correcta, deben ser 11 en total el total de lineas es ".sizeof($datosUsuarios);
	    		}else{
	    			$usuariosDatos = $us_model->get(NULL,array('us_mail' => $datosUsuarios[4]));
	    			if (sizeof($usuariosDatos) > 0){
	    				$errores .= "- Linea ".$i." Error, El usuario ya existe en la base de datos<br />";
	    			}
	    		}
	    		$i ++;
    		}
    	}
    	
    	if ($errores != ""){
    		echo $errores;
    	}else{
    		fclose($file);
    		$file = fopen(RUTA_FIJA."/htdocs/admin/".$archivo, "r") or exit("Unable to open file!");
    		$i = 0;
    		while(!feof($file))
    		{
    			$linea = fgets($file);
    			if (ltrim(rtrim($linea)) != "" && $i > 0){
	    			$datosUsuarios = explode(",", $linea);
	    			$datos = array(
	    					'us_nombre' 				=> $datosUsuarios["0"],
	    					'us_apellido' 				=> $datosUsuarios["1"],
	    					'us_actividad_principal' 	=> $datosUsuarios["2"],
	    					'us_cargo' 					=> $datosUsuarios["3"],
	    					'us_email' 					=> $datosUsuarios["4"],
	    					'us_telefono' 				=> $datosUsuarios["5"],
	    					'us_ciudad' 				=> $datosUsuarios["6"],
	    					'us_status' 				=> 1,
	    					'Provincia_pr_id' 			=> $datosUsuarios["7"],
	    					'Pais_pa_id' 				=> $datosUsuarios["8"],
	    					'evento_ev_id' 				=> $datosUsuarios["9"],
	    					'us_tags' 					=> $datosUsuarios["10"]
	    			);
	    			$us_model->save($datos);
    			}
    			$i ++;
    			
    		}
            echo '{"proceso" : "ok"}';
    	}
    	fclose($file);

    	exit();
    	
    }
    
    public function uploadarchivoAction(){
    
    	$uploadMaterial  = new Custom_SubirImagenes();
    	//$uploadMaterial->setpath("C:/xampp/htdocs/neoworkshop/htdocs/imagenes/repositorio");
    	$uploadMaterial->setpath(RUTA_FIJA."/htdocs/admin/archivos/altas/");
    	$uploadMaterial->setconvert_imagick(false);
    	$arrMaterial = $uploadMaterial->guardarImagen();
    	$materialSubido = $uploadMaterial->getrelative_path().$arrMaterial[0];
    	print_r($materialSubido);
    	exit();
    }
    
    public function accionAction()
    {
        $this->view->menu ='usuarios';
        $id = $this->_getParam('id');
        
        if (isset($id)){
            $id = intval($id);
            $us_model = new Application_Model_usuariosMapper();
            $us = $us_model->get($id);
            $pr_model = new Application_Model_provinciaMapper();
            $prov=$pr_model->get(NULL,NULL, $us->getPais_pa_id());
            $listProv='';
            $listProv = '<option value=""></option>';
            foreach ($prov as $p) {
                if($p->getPr_id() == $us->getProvincia_pr_id()){
                    $listProv.='<option value="'.$p->getPr_id().'" selected>'.utf8_encode($p->getPr_nombre()).'</option>';    
                } else {
                    $listProv.='<option value="'.$p->getPr_id().'">'.utf8_encode($p->getPr_nombre()).'</option>';
                }
            }
            $this->view->listProv = $listProv;
        }else{
            $us = new Application_Model_DbTable_usuarios(array());
        }
        $this->view->usuarios = $us;
        
        $pais_model = new Application_Model_paisMapper();
        $paises = $pais_model->get();
        $this->view->paises = $paises; 
        $provincia_model = new Application_Model_provinciaMapper();
        $provincias = $provincia_model->get();
        $this->view->provincias = $provincias;

        $objEventoMapper = new Application_Model_eventoMapper();
        $arrEventos = $objEventoMapper->get();
        //TODO hay que agregar la cantidad de inscriptos cuando hag a las inscripciones o altas masivas
        // echo "<pre>";print_r($arrEventos);exit();
        $this->view->arrEventos = $arrEventos;

    }
    
    public function listadoAction()
    {
        $this->view->menu ='usuarios';
    	$jerarquia = $this->_getParam('j');
        $actividad = $this->_getParam('a');
        $evento = $this->_getParam('ev');
        $us_model = new Application_Model_usuariosMapper();
        /*echo $jerarquia;
        echo $actividad;*/
        if (isset($jerarquia) && isset($actividad) && isset($evento)) {
                /*j + a + ev*/
                $arrUs = $us_model->get(NULL,array("evento_ev_id" => $evento), $jerarquia,$actividad);
                $this->view->arrUsuarios = $arrUs;
                
                $rondas = new Application_Model_rondaMapper();
                $objRondas = $rondas->get();
                $objEvento = new Application_Model_eventoMapper();
                for ($i = 0; $i < sizeof($objRondas); $i++) {
                    $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
                }
        } elseif (isset($jerarquia) && isset($actividad)) {
                /*j + a*/
                $arrUs = $us_model->get(NULL,NULL, $jerarquia,$actividad);
                $this->view->arrUsuarios = $arrUs;
                
                $rondas = new Application_Model_rondaMapper();
                $objRondas = $rondas->get();
                $objEvento = new Application_Model_eventoMapper();
                for ($i = 0; $i < sizeof($objRondas); $i++) {
                    $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
                }
        } elseif (isset($jerarquia) && isset($evento)) {
                /*j + ev*/
                $arrUs = $us_model->get(NULL,array("evento_ev_id" => $evento), $jerarquia);
                $this->view->arrUsuarios = $arrUs;
                
                $rondas = new Application_Model_rondaMapper();
                $objRondas = $rondas->get();
                $objEvento = new Application_Model_eventoMapper();
                for ($i = 0; $i < sizeof($objRondas); $i++) {
                    $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
                }
        } elseif (isset($evento) && isset($actividad)) {
                /*ev + a*/
                $arrUs = $us_model->get(NULL,array("evento_ev_id" => $evento),NULL,$actividad);
                $this->view->arrUsuarios = $arrUs;
                
                $rondas = new Application_Model_rondaMapper();
                $objRondas = $rondas->get();
                $objEvento = new Application_Model_eventoMapper();
                for ($i = 0; $i < sizeof($objRondas); $i++) {
                    $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
                }
        } elseif (isset($jerarquia)) {
            /*j*/
            $arrUs = $us_model->get(NULL,NULL, $jerarquia);
            $this->view->arrUsuarios = $arrUs;
            
            $rondas = new Application_Model_rondaMapper();
            $objRondas = $rondas->get();
            $objEvento = new Application_Model_eventoMapper();
            for ($i = 0; $i < sizeof($objRondas); $i++) {
                $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
            }
        } elseif (isset($evento)) {
            /*ev*/
            $arrUs = $us_model->get(NULL,array("evento_ev_id" => $evento));
            $this->view->arrUsuarios = $arrUs;
            
            $rondas = new Application_Model_rondaMapper();
            $objRondas = $rondas->get();
            $objEvento = new Application_Model_eventoMapper();
            for ($i = 0; $i < sizeof($objRondas); $i++) {
                $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
            }
        } elseif (isset($actividad)) {
            /*a*/
            $arrUs = $us_model->get(NULL,NULL,NULL, $actividad);
            $this->view->arrUsuarios = $arrUs;
            
            $rondas = new Application_Model_rondaMapper();
            $objRondas = $rondas->get();
            $objEvento = new Application_Model_eventoMapper();
            for ($i = 0; $i < sizeof($objRondas); $i++) {
                $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
            }
        } else {
            /*sin filtro*/
            $arrUs = $us_model->get();
            $this->view->arrUsuarios = $arrUs;
            
            $rondas = new Application_Model_rondaMapper();
            $objRondas = $rondas->get();
            $objEvento = new Application_Model_eventoMapper();
            for ($i = 0; $i < sizeof($objRondas); $i++) {
                $objRondas[$i]->evento = $objEvento->get($objRondas[$i]->getEvento_ev_id());
            }

        }
        
        $this->view->rondas = $objRondas;

        $objEventoMapper = new Application_Model_eventoMapper();
        $arrEventos = $objEventoMapper->get();
        //TODO hay que agregar la cantidad de inscriptos cuando hag a las inscripciones o altas masivas
        // echo "<pre>";print_r($arrEventos);exit();
        $this->view->arrEventos = $arrEventos;
        
    }

    public function uploadAction(){
            
        $uploadMaterial  = new Custom_SubirImagenes();
        
        //$uploadMaterial->setpath("C:/xampp/htdocs/neoworkshop/htdocs/imagenes/repositorio"); 
        $uploadMaterial->setpath("/var/www/virtual/fidegroup.com.ar/neoworkshop/htdocs/imagenes/repositorio/");
        $uploadMaterial->setconvert_imagick(false);
        $arrMaterial = $uploadMaterial->guardarImagen();
        $materialSubido = $uploadMaterial->getrelative_path().$arrMaterial[0];
        print_r($materialSubido);
        exit();
    }

    private function generaPass(){
    //Se define una cadena de caractares. Te recomiendo que uses esta.
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    //Obtenemos la longitud de la cadena de caracteres
    $longitudCadena=strlen($cadena);
     
    //Se define la variable que va a contener la contraseña
    $pass = "";
    //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
    $longitudPass=8;
     
    //Creamos la contraseña
    for($i=1 ; $i<=$longitudPass ; $i++){
        //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
        $pos=rand(0,$longitudCadena-1);
     
        //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
        $pass .= substr($cadena,$pos,1);
    }
    return $pass;
}
    
    public function procesarAction(){
        
    	$params = $this->_getAllParams();
        $clave='';
        if($this->_getParam('status')!= $this->_getParam('status_older') && $this->_getParam('status') == 1){
            $clave = $this->generaPass();
            $sha_clave = sha1($clave.SEMILLA);
            $datos = array(
            'us_nombre' => $this->_getParam('nombre'),
            'us_apellido' => $this->_getParam('apellido'),
            'us_actividad_principal' => $this->_getParam('actividad'),
            'us_cargo' => $this->_getParam('cargo'),
            'us_jerarquiza_cargo' => $this->_getParam('jerarquiza'),
            'us_email' => $this->_getParam('email'),
            'us_telefono' => $this->_getParam('telefono'),
            'us_como_conociste' => $this->_getParam('conociste'),
            'us_temas_de_interes' => $this->_getParam('temas'),
            'us_ciudad' => $this->_getParam('ciudad'),
            'us_clave' => $sha_clave,
            'us_status' => $this->_getParam('status'),
            'Provincia_pr_id' => $this->_getParam('provincia'),
            'Pais_pa_id' => $this->_getParam('pais'),
            'evento_ev_id'  => $this->_getParam('evento'),
            'us_tags'  => $this->_getParam('tags')
        ); 

        } else {
        	$datos = array(
                'us_nombre' => $this->_getParam('nombre'),
                'us_apellido' => $this->_getParam('apellido'),
        		'us_actividad_principal' => $this->_getParam('actividad'),
                'us_cargo' => $this->_getParam('cargo'),
                'us_jerarquiza_cargo' => $this->_getParam('jerarquiza'),
                'us_email' => $this->_getParam('email'),
                'us_telefono' => $this->_getParam('telefono'),
                'us_como_conociste' => $this->_getParam('conociste'),
                'us_temas_de_interes' => $this->_getParam('temas'),
                'us_ciudad' => $this->_getParam('ciudad'),
                'us_status' => $this->_getParam('status'),
                'Provincia_pr_id' => $this->_getParam('provincia'),
                'Pais_pa_id' => $this->_getParam('pais'),
                'evento_ev_id'  => $this->_getParam('evento'),
                'us_tags'  => $this->_getParam('tags')
        	); 
    	
        }
        /*print_r($datos);*/
        $us_model = new Application_Model_usuariosMapper();
        if ($this->_getParam("id") != ''){
            $us = $us_model->save($datos,$this->_getParam("id"));
        }else{
            $us = $us_model->save($datos);
        }
        //$web = $us_model->save($datos);
        if($clave != ''){

        $objWeb = new Application_Model_webMapper();
        $arrDbTableWeb = $objWeb->get(8);
        $this->view->arrDbTableWeb =  $arrDbTableWeb;
        $objMapperEvento = new Application_Model_eventoMapper();
        $arrDbTableEvento = $objMapperEvento->get($arrDbTableWeb->getEvento_ev_id());
        $this->view->arrDbTableEvento = $arrDbTableEvento;



            $mail = new Zend_Mail();
            $html = $this->view;
            $this->view->nombre = $this->_getParam('nombre');
            $this->view->apellido = $this->_getParam('apellido');
            $this->view->usuario = $this->_getParam('email');
            $this->view->pass = $clave;
            $html = $this->view->render("/usuarios/mail_registracion.phtml");
            echo $html;exit();
            $mail->setBodyHtml($html)
                ->setFrom("sitio@invertur.com.ar","Panel de Invertur")
                ->addTo($this->_getParam('email'))
                ->setSubject("Registracion")
                ->send();
        }
        
    	echo '{"proceso" : "ok"}';
    	exit();
    	
    }

    public function mail_registracionAction(){

    }

    public function buscarprovinciaAction(){
        //$this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        $pr_model = new Application_Model_provinciaMapper();
        $listpr=$pr_model->get(NULL, NULL, $id);
        $provincias='';
        $provincias = '<option value=""></option>';
        foreach ($listpr as $pr) {
            $provincias.='<option value="'.$pr->getPr_id().'">'.utf8_encode($pr->getPr_nombre()).'</option>';

        }
        echo $provincias;
        exit();
    }
    
    public function eliminarAction(){
    	$id = $this->_getParam("id");
    	$us_model = new Application_Model_usuariosMapper();
    	$us_model->delete('us_id = '.intval($id));
    	echo '{"proceso" : "ok"}';
    	exit();
    }

    public function buscarAction(){
        $params = $this->_getAllParams();
        $id = $params["id"];
        
        $us_model = new Application_Model_usuariosMapper();
        
        $usuario = $us_model->get($id);
        print_r($usuario);
        exit();
        
    }
}
