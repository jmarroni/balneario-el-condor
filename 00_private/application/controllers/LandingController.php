<?php

class LandingController extends Zend_Controller_Action{
	

	public function anotarseAction(){
		$param = $this->_getAllParams();
		$tejoModel = new Application_Model_DbTable_tejo();
		if ($param["nombre"] 		!= "" &&
		$param["apellido"] 		!= "" &&
		$param["mail"] 			!= "" &&
		$param["entradas"] 		!= "" &&
		$param["telefono"]	 	!= ""){
			$insert["te_nombre"] = $param["nombre"];
			$insert["te_apellido"] = $param["apellido"];
			$insert["te_telefono"] = $param["telefono"];
			$insert["te_mail"] = $param["mail"];
			$insert["te_newsletter"] = (isset($param["quiero"]))?"1":"0";
			$insert["te_club_asociacion"] = $param["nombreclub"];
			$insert["te_provincia"] = $param["provincia"];
			$insert["te_localidad"] = $param["localidad"];
			$insert["te_alojamiento"] = $param["alojamiento"];
			$insert["te_asistencia"] = $param["asistencia"];
			$insert["te_concursantes"] = $param["concursantes"];
			$insert["te_entradas"] = $param["entradas"];
			$insert["te_excursiones"] = (isset($param["excursiones"]))?"1":"0";
			$insert["te_cena"] = (isset($param["cena"]))?"1":"0";
			$insert["te_comentarios"] = $param["comentario"];
			$tejoModel->insert($insert);
			echo "Sus datos fueron almacenados de forma correcta, en breve tendra noticias nuestras.";
			$html = "Se recibi&oacute; una suscripci&oacute;n de la web del balneario, los datos:<br><br>";
			$html .= "<strong>Nombre</strong>:&nbsp;".$insert["te_nombre"]."<br>";
			$html .= "<strong>Apellido</strong>:&nbsp;".$insert["te_apellido"]."<br>";
			$html .= "<strong>Mail</strong>:&nbsp;".$insert["te_mail"]."<br>";
			$html .= "<strong>Telefono</strong>:&nbsp;".$insert["te_telefono"]."<br>";
			$html .= "<strong>Club / Asociaci&oacute;n</strong>:&nbsp;".$insert["te_club_asociacion"]."<br>";
			$html .= "<strong>Provincia</strong>:&nbsp;".$insert["te_provincia"]."<br>";
			$html .= "<strong>Localidad</strong>:&nbsp;".$insert["te_localidad"]."<br>";
			$html .= "<strong>Cantidad de Alojamientos</strong>:&nbsp;".$insert["te_alojamiento"]."<br>";
			$html .= "<strong>Personas que asisten(ademas del concursante)</strong>:&nbsp;".nl2br($insert["te_asistencia"])."<br>";
			$html .= "<strong>Concursantes</strong>:&nbsp;".$insert["te_concursantes"]."<br>";
			$html .= "<strong>Asiste/n a la cena</strong>:&nbsp;".((isset($param["cena"]))?"Si":"No")."<br>";
			$html .= "<strong>Asiste/n a las excursiones</strong>:&nbsp;".((isset($param["excursiones"]))?"Si":"No")."<br>";
			$html .= "<strong>Comentarios</strong>:&nbsp;".nl2br($insert["te_comentarios"])."<br>";
			$mensaje = $html;
			$para  = 'tejoelcondor@gmail.com' . ', '; // atención a la coma
			$para = 'tejo@balneario-el-condor.com.ar';
			
			// subject
			$titulo = 'Suscripción Web';
				
			// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Cabeceras adicionales
			$cabeceras .= 'To: tejoelcondor@gmail.com,tejo@balneario-el-condor.com.ar' . "\r\n";
			$cabeceras .= 'From: Web del Balneario <tejo@balneario-el-condor.com.ar>' . "\r\n";
			
			// Mail it
			mail($para, $titulo, $mensaje, $cabeceras);
		}else{
			echo "Error en los datos enviados, por favor verifique los datos y si son correctos reintente mas tarde.";
		}
		exit();
	}
	
	public function boletininformativoAction(){
		$Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="radiusPrimero activo"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
	}
	
	public function tejoAction(){
		$Mareas = new Application_Model_DbTable_mareas();
		$arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
		$this->view->arrMareas = $arrMareas;
		
		$Clima = new Application_Model_DbTable_clima();
		$arrClima = $Clima->get(1);
		$this->view->arrClima = $arrClima;
		
		$this->view->param = array(
				"home"          => 'class="radiusPrimero activo"',
				"Ubicacion"     => '',
				"Servicio"      => '',
				"Imagenes"      => '',
				"Novedades"     => '',
				"Clasificados"  => '',
				"Fauna"         => '',
				"Historia"      => 'class="radiusUltimo"'
		);
	}

	public function coloniaAction(){
		$Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="radiusPrimero activo"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
	}

	public function primaveraAction(){
		$Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="radiusPrimero activo"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
	}

	public function participarAction(){
		$mail = $this->_getParam("email");
		$recibir = $this->_getParam("recibir");
		if (isset($mail)){
			$newsletter = new Application_Model_DbTable_landing();
			if ($newsletter->participar($mail,$recibir) == "error"){
				echo "ERROR";
			}else{
				echo "OK";
			}
		}else{
			echo "ERROR";
		}
		exit();
	}
	public function reservarAction(){
        $param = $this->_getAllParams();

        if ($param["nombre"] 		!= "" &&
        	$param["apellido"] 		!= "" &&
        	$param["mail"] 			!= "" &&
        	$param["entradas"] 		!= "" &&
        	$param["telefono"]	 	!= ""){

	        $newsletter = new Application_Model_DbTable_landing();

	        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
	           $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
	        {
	            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
	        else
	            $ip = $_SERVER['REMOTE_ADDR'];

	        $newsletter->insertPrimavera(
	        							array("pri_nombre" 	=> $param["nombre"],
	        								"pri_apellido" 	=> $param["apellido"],
	        								"pri_mail" 		=> $param["mail"],
	        								"pri_telefono" 	=> $param["telefono"],
	        								"pri_comentario"=> $param["comentario"],
	        								"pri_entradas" 	=> $param["entradas"],
	        								"pri_quiero" 	=> $param["quiero"],
	        								"pri_ip" 		=> $ip));

	        if (mail('contacto@balneario-el-condor.com.ar','[Sitio Web] - Reserva de entradas','Se realizo la siguiente reserva'.implode("|", $param)))
	            echo '{"Mensaje":"Se ha ingresado correctamente la reserva, pronto tendra noticias nuestras!!"}';
	        else
	            echo '{"Mensaje":"ocurri&oacute; un error intentelo nuevamente mas tarde"}';
	     }
	     exit();
	}
	
	public function fiestatejoAction(){
		$autor = "fiestatejo";
		$this->view->autor = $autor;
		// DATOS
		$Imagen = new Application_Model_DbTable_imagenes();
		if ($autor  == ""){
			$limit = "20";
			$Imagenes = $Imagen->get($limit);
		}else{
			$Imagenes = $Imagen->get("","im_titulo = '$autor'");
		}
		$this->view->autores = $Imagen->getAutores();
		$this->view->Imagenes = $Imagenes;
		$this->view->title = "5ta Fiesta Nacional del tejo";
		$this->view->description = "Todas las imagenes de la 5ta Fiesta nacional del Tejo del Balneario El Condor, Viedma, Rio Negro";
		$this->view->canonica = "imagen";
	}
	
	public function fiestadianinoAction(){
		$autor = "fiestanino";
		$this->view->autor = $autor;
		// DATOS
		$Imagen = new Application_Model_DbTable_imagenes();
		if ($autor  == ""){
			$limit = "20";
			$Imagenes = $Imagen->get($limit);
		}else{
			$Imagenes = $Imagen->get("","im_titulo = '$autor'");
		}
		$this->view->autores = $Imagen->getAutores();
		$this->view->Imagenes = $Imagenes;
		$this->view->title = "D&iacute;a del ni&ntilde;o en la Villa Maritima";
		$this->view->description = "Todas las imagenes dell día del niño en el Balneario El Condor, Viedma, Rio Negro";
		$this->view->canonica = "imagen";
	}
	
	public function arenasdelsurAction(){
		$autor = "arenasdelsur";
		$this->view->autor = $autor;
		// DATOS
		$Imagen = new Application_Model_DbTable_imagenes();
		if ($autor  == ""){
			$limit = "20";
			$Imagenes = $Imagen->get($limit);
		}else{
			$Imagenes = $Imagen->get("","im_titulo = '$autor'");
		}
		$this->view->autores = $Imagen->getAutores();
		$this->view->Imagenes = $Imagenes;
		$this->view->title = "Arenas del Sur Cena Show";
		$this->view->description = "Todas las imagenes de la 5ta Fiesta nacional del Tejo del Balneario El Condor, Viedma, Rio Negro";
		$this->view->canonica = "imagen";
	}
	
	public function feriagastronomicaAction(){
		$this->Clasificados = new Application_Model_DbTable_clasificados();
		$this->view->title = "Feria Gastronomica";
		$this->view->description = "Todo acerca de la feria Gastronomica que se llevar� acabo en el Balneario el Condor entre los d�as 2,3,4 y 5 de abril";
		$this->view->canonica = "Feria Gestronomica";
		
				//         $Clasificados = new Application_Model_DbTable_clasificados();
		$mensaje=$this->_getParam("mensaje");
		$arrClasificados =$this->Clasificados->get();
		$this->view->arrClasificados = $arrClasificados;
		$this->view->metaDescription="Clasificados Del Balneario el Condor, alquiler, venta, equipos de pesca, casas, cabañas, departamentos, camping, camping, temporada, alta, baja, diciembre, enero, febrero, semana santa";
		$this->view->title = "Clasificados del Balneario El Condor";
		if($mensaje !=''){
			$this->view->mensaje="error";
			$this->view->errorMensaje="error";
		}
		$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
		$this->view->reCaptcha = $recaptcha->getHTML();
		$this->view->canonica = "alquileres-y-ventas";
	}
	
	public function clasificadosAction(){
				$this->Clasificados = new Application_Model_DbTable_clasificados();
		$Mareas = new Application_Model_DbTable_mareas();
		$clasificadoimagenes = new Application_Model_DbTable_clasificadoimagenes();
		$clasificadomail = new Application_Model_DbTable_clasificadomail();
		$arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
		$this->view->arrMareas = $arrMareas;

		$Clima = new Application_Model_DbTable_clima();
		$arrClima = $Clima->get(1);
		$this->view->arrClima = $arrClima;

		$this->view->param = array(
				"home"          => 'class="radiusPrimero"',
				"Ubicacion"     => '',
				"Servicio"      => '',
				"Imagenes"      => '',
				"Novedades"     => '',
				"Clasificados"  => 'class="activo"',
				"Fauna"         => '',
                "Informacion"         => '',
				"Historia"      => 'class="radiusUltimo"'
		);
		$this->view->title = "Clasificados - Alquileres";
		$this->view->description = "Alquileres Temporarios, Casas, Deptos, Hotel, Camping, todo en el Balneario";
		$this->view->canonica = "Clasificados - Alquileres";
		
		$arrClasificados =$this->Clasificados->get('Alquilo');
		$this->view->arrClasificados = $arrClasificados;
	}
}
