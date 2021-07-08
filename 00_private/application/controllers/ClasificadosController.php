<?php

class ClasificadosController extends Zend_Controller_Action{
	protected $Clasificados;
	protected $clasificadoimagenes;
	protected $clasificadomail;
	public function init() {
		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');*/
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
		   $Cercanos = new Application_Model_DbTable_cercanos();
        $arrCercanos = $Cercanos->get(6);
        $this->view->arrCercanos = $arrCercanos;
        $Gourmet = new Application_Model_DbTable_gourmet();
        $arrGourmet = $Gourmet->get(6);
        $this->view->arrGourmet = $arrGourmet;

        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje1 = $Hospedaje->get(6);
        $this->view->arrHospedaje = $arrHospedaje1;
                
        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje1 = $Hospedaje->get(6,1);
        $this->view->arrHospedaje1 = $arrHospedaje1;

        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje2 = $Hospedaje->get(6,2);
        $this->view->arrHospedaje2 = $arrHospedaje2;
        
        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje3 = $Hospedaje->get(6,3);
        $this->view->arrHospedaje3 = $arrHospedaje3;
        
        $Nocturno = new Application_Model_DbTable_nocturno();
        $arrNocturno = $Nocturno->get(99);
        $this->view->arrNocturno = $arrNocturno;

        $Imagenes = new Application_Model_DbTable_imagenes();
        $arrImagenes = $Imagenes->get(99);
        $this->view->arrImagenes = $arrImagenes;
	}
	
	public function redireccionAction(){
		$param = $this->_getAllParams();
		Header("HTTP/1.1 301 Moved Permanently");
		Header("Location: http://www.balneario-el-condor.com.ar/clasificados/detalle/".$param["seguido"]);
	}

	public function detalleAction()
	{
		$param = $this->_getAllParams();
		// DATOS
		//         $Clasificados = new Application_Model_DbTable_clasificados();
		if (isset($param["keyword"])){
			$arrClasificados = $this->Clasificados->getDetalle($param["keyword"]);
		}else{
			$arrClasificados = $this->Clasificados->getDetalle(NULL,$param["id"]);
		}
		
		$this->view->arrClasificados = $arrClasificados;
		$this->view->title = $arrClasificados[0]["cla_titulo"];
		$this->Clasificados->updateVisitas($arrClasificados[0]["cla_id"], array("cla_visitas" => $arrClasificados[0]["cla_visitas"] + 1));
		$this->view->metaDescription=$arrClasificados[0]["cla_titulo"];
				$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
		$this->view->reCaptcha = $recaptcha->getHTML();
		$this->view->canonica = "alquileres-y-ventas/".$arrClasificados[0]["cla_keyword"];
	}

	function indexAction()
	{
		//         $Clasificados = new Application_Model_DbTable_clasificados();
		$mensaje=$this->_getParam("mensaje");
		$tipo=$this->_getParam("tipo");
		$arrClasificados =$this->Clasificados->get($tipo);
		$this->view->arrClasificados = $arrClasificados;

    	
    	$clave = $this->_getParam("clave");
	    if (isset($clave) && ($clave == "CarmenLuca19812012")){
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *');
            $log = new Application_Model_DbTable_log();
            $log->insert(array("lo_fecha" => date("Y-m-d h:i:s"),"lo_msj" => ALQUILERES));
            $arrClasificadosAlquiloJson = array();
            for ($i = 0;$i < sizeof($arrClasificados); $i ++){
                if ($arrClasificados[$i]["cla_categoria"] == "Alquilo") {
                    foreach ($arrClasificados[$i] as $key => $value){
                        $arrClasificadosAlquiloJson[$i][$key] = utf8_encode($value);
                    }
                }
            }
			echo json_encode($arrClasificadosAlquiloJson);
			exit();
		}

		$this->view->metaDescription="Clasificados Del Balneario el Condor, alquiler, venta, equipos de pesca, casas, cabañas, departamentos, camping, camping, temporada, alta, baja, diciembre, enero, febrero, semana santa";
		$this->view->title = "Clasificados del Balneario El Condor";
		if($mensaje !=''){
			$this->view->mensaje="error";
			$this->view->errorMensaje="error";
		}
		$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
		$this->view->reCaptcha = $recaptcha->getHTML();

        $this->view->tab = array("alquilo" => "","necesito" => "","vendo" => "");
		switch ($tipo){
            case "Alquilo":
                $this->view->tab["alquilo"] = "active";
                break;
            case "Necesito":
                $this->view->tab["necesito"] = "active";
                break;
            case "Vendo":
                $this->view->tab["vendo"] = "active";
                break;
        }
		$this->view->canonica = "alquileres-y-ventas";
	}

    private function id_youtube($url) {

$patron =  '%^ (?:https?://)? (?:www\.)? (?: youtu\.be/ | youtube\.com (?: /embed/ | /v/ | /watch\?v= ) ) ([\w-]{10,12}) $%x';
   	$array = preg_match($patron, $url, $parte);
   	if (false !== $array) {
   		return $parte[1];
    }
        return false;
    }

	public function cargarclasificadoAction(){
		
		//$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
		/*$result = $recaptcha->verify(
		    $_POST['recaptcha_challenge_field'],
		    $_POST['recaptcha_response_field']
		);*/
		/* if (!$result->isValid()) {
			$this->_redirect("/servicios/index/mensaje/error en captcha");
		}*/
		$this->clasificadoimagenes = new Application_Model_DbTable_clasificadoimagenes();
		$param = $this->_getAllParams();
		if(empty($param['titulo']) || empty($param['correo']) || empty($param['categoria'])){
			echo 'Error'; exit();
		} else{ 
			$keyword = str_replace("�", "NI",str_replace("�", "U",str_replace("�", "O",str_replace("�", "I",str_replace("�", "E",str_replace("�", "A",str_replace("�", "ni",str_replace("�", "u",str_replace('�', "o",str_replace("�", "i",str_replace("�", "e",str_replace("�", "a",str_replace(" ", "-",$param["titulo"] )))))))))))));
			$keyword = $keyword."-".rand(100000,900000);
		    $video = $this->id_youtube($param['video']);
		    $return=$this->Clasificados->insertClasificado($param['titulo'], $param['descripcion'], $param['nombre'], $param['correo'], date("Y-m-d h:i:s"), '', $param["direccion"], $keyword,$param['categoria'], $video);
			/*$arrClasImg=$this->uploadFile();*/
			$img = explode('/', $param['archivo']);
			$img = $img[2];
			$img1 = explode('/', $param['archivo1']);
			$img1 = $img1[2];
			$img2 = explode('/', $param['archivo2']);
			$img2 = $img2[2];
			$img3 = explode('/', $param['archivo3']);
			$img3 = $img3[2];
			$img4 = explode('/', $param['archivo4']);
			$img4 = $img4[2];
			
			$return2 = $this->clasificadoimagenes->insertClaImg($return, $img, $img1, $img2, $img3, $img4);
			if($return == 'error'){
				echo $return;exit();
				/*$this->_redirect("/clasificados/index/mensaje/".$return);*/
			}else{
				$titulo = "Nuevo Clasificado";
							$para = "jmarroni@gmail.com";
							$mensaje = "Se ha subido un nuevo clasificado en Balneario El Condor 
							<a href='http://www.balneario-el-condor.com.ar/clasificados/detalle/$keyword' target='_blank'>http://www.balneario-el-condor.com.ar/clasificados/detalle/$keyword</a>";
							// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
							$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
							$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
							// Cabeceras adicionales
							// $cabeceras .= 'To: micaelak@guiaoleo.com' . "\r\n";
							$cabeceras .= 'To: jmarroni@gmail.com' . "\r\n";
							$cabeceras .= 'From: Web ElCondor <balneario-el-condor@web.com>' . "\r\n";
		
							// Mail it
							$comentario = "";
							(mail($para, $titulo, $mensaje, $cabeceras));
				echo $keyword;exit();
				/*$this->_redirect("/clasificados/detalle/id/".$return);*/
			}
		}
		
	}

	public function enviarmailduenoAction(){
		$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
		$result = $recaptcha->verify(
		    $_POST['recaptcha_challenge_field'],
		    $_POST['recaptcha_response_field']
		);
		/*
		if (!$result->isValid()) {
			$this->_redirect("/clasificados/index/mensaje/error en captcha");
		}
		*/
		$param = $this->_getAllParams();
	    $direccionIp = $_SERVER['REMOTE_ADDR'];	
	    $clasificadomail = new Application_Model_DbTable_clasificadomail();
	    $mailDest= $clasificadomail->getMailDest($param["id"]);
	    $nomApe = str_replace("�", "NI",str_replace("�", "U",str_replace("�", "O",str_replace("�", "I",str_replace("�", "E",str_replace("�", "A",str_replace("�", "ni",str_replace("�", "u",str_replace('�', "o",str_replace("�", "i",str_replace("�", "e",str_replace("�", "a",str_replace(" ", "-",$param["nomApeForm"] )))))))))))));
		$clasificadomail->insertClaMail($param["id"], $mailDest[0]["cla_mail_contacto"], $direccionIp, $nomApe, $param["correoForm"], $param["telefonoForm"], date("Y-m-d h:i:s"), $param["comentarioForm"]);
		// if($return == 'error'){
		//	$this->_redirect("/clasificados/index/mensaje/".$param["id"]);
		// }else{ 
	

			print_r($mailDest[0]["cla_mail_contacto"]);
			$this->view->mailDest = $mailDest;
			$this->view->param = $param;
			$this->view->nomApe = $nomApe;
			$mail = new Zend_Mail();
			$html = $this->view;
			$html = $this->view->render("/templatemail/contactoclasificados.phtml");
			$mail->setBodyHtml($html)
			->setFrom("contacto@balneario-el-condor.com.ar","Web Balneario El Condor")
			->addTo($mailDest[0]["cla_mail_contacto"],$mailDest[0]["cla_nombre_contacto"])
			->setSubject("Te contactan por tu clasificado")
			->send();
		$this->_redirect("/clasificados/detalle/id/".$param["id"]);
		// }
		exit();

	}

	public function uploadAction(){
		$uploadImage = new Custom_SubirImagenes();
        $uploadImage->setpath($_SERVER['DOCUMENT_ROOT'].'/imagenes/clasificados/');
        $uploadImage->setconvert_imagick(false); 
        $arrImagen = $uploadImage->guardarImagen();
        $imagenSubida = $uploadImage->getrelative_path().$arrImagen[0];
        print_r($imagenSubida);
        exit();
    }

	private function uploadFile(){
		$upload = new Zend_File_Transfer_Adapter_Http();
		$upload->addValidator('Extension', false, 'gif,GIF,jpg,jpeg,JPG,JPEG,png,PNG,pdf,PDF');
		$upload->addValidator('FilesSize', false, array('min' => '1kB', 'max' => '4MB'));
		$upload->setDestination($_SERVER['DOCUMENT_ROOT'].'/imagenes/clasificados/'); // Cambiar Ruta
		$parametros = array("PDF" => "", "IMAGEN" => "");
		$i = 0; //Variable de posici�n del arreglo de im
	
		foreach ($upload->getFileInfo() as $info) {
		 
			if ($info['name'] != '') {
				$exts = explode(".", $info['name']);
				$n = count($exts) - 1;
				$ext = $exts[$n];
		
				$arrImagenes[$i] = substr(md5(uniqid(rand())), 0, 6) . '_' . $info['name'];
					$baseurl = $_SERVER['DOCUMENT_ROOT'].'/imagenes/clasificados/'; // Cambiar Ruta
		
					$target = $baseurl . $arrImagenes[$i];

					$upload->addFilter('Rename', array('target' => $target));


					//hacer print al receive
					if ($upload->receive($info['name'])){
						$titulo = "Se realizo el upload de una imagen";
						$para = "jmarroni@gmail.com";
						$mensaje = "Enviaron la siguiente imagen a el balneario clasificados ".$target;
						// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
						$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
						$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
						// Cabeceras adicionales
						// $cabeceras .= 'To: micaelak@guiaoleo.com' . "\r\n";
						$cabeceras .= 'To: jmarroni@gmail.com' . "\r\n";
						$cabeceras .= 'From: Web ElCondor <balneario-el-condor@web.com>' . "\r\n";
	
						// Mail it
						$comentario = "";
						(mail($para, $titulo, $mensaje, $cabeceras));
					}else{
						echo "La imagen no subio";
					}
				$i++;
			}//if
				
		}
		return $arrImagenes;
	}

}
