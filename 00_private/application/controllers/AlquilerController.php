<?php

class AlquilerController extends Zend_Controller_Action{
	protected $Fotos;
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
	}
	
	public function redireccionAction(){
		$param = $this->_getAllParams();
		Header("HTTP/1.1 301 Moved Permanently");
		Header("Location: http://www.balneario-el-condor.com.ar/clasificados/detalle/".$param["seguido"]);
	}

	public function detalleAction()
	{
		$param = $this->_getAllParams();
		$alquiler= new Application_Model_DbTable_alquiler();
		$arrAlquiler = $alquiler->get($param["id"],null);
		
		$this->view->title = $arrAlquiler[0]["uim_titulo"];
		$this->view->alquiler = $arrAlquiler;
		
	}

	function indexAction()
	{
		$mensaje=$this->_getParam("mensaje");
		$alquiler = new Application_Model_DbTable_alquiler();
		$this->view->arrAlquiler= $alquiler->get();
		$this->view->metaDescription="Clasificados Del Balneario el Condor, fotos, imagenes";
		$this->view->title = "Fotos e Imagenes";
		if($mensaje !=''){
			$this->view->mensaje="error";
			$this->view->errorMensaje="error";
		}
		$this->view->canonica = "fotos";
	}

    private function id_youtube($url) {
		$patron =  '%^ (?:https?://)? (?:www\.)? (?: youtu\.be/ | youtube\.com (?: /embed/ | /v/ | /watch\?v= ) ) ([\w-]{10,12}) $%x';
	   	$array = preg_match($patron, $url, $parte);
	   	if (false !== $array) {
	   		return $parte[1];
	    }
        return false;
    }

	public function cargarAction(){
		$fotos = new Application_Model_DbTable_alquiler();
		$param = $this->_getAllParams();
		$arreglo=array(
				"uim_titulo" => $param['titulo'],
				"uim_telefono" => $param['telefono'],
				"uim_mail" => $param['mail'],
				"uim_direccion" => $param['direccion'],
				"uim_contacto" => $param['contacto'],
				"uim_descripcion" => $param['descripcion'],
				"uim_imagen0" => str_replace(['[', '"', ']'], '', $param['archivo0']),
				"uim_imagen1" => str_replace(['[', '"', ']'], '', $param['archivo1']),
				"uim_imagen2" => str_replace(['[', '"', ']'], '', $param['archivo2']),
				"uim_imagen3" => str_replace(['[', '"', ']'], '', $param['archivo3']),
				"uim_imagen4" => str_replace(['[', '"', ']'], '', $param['archivo4'])
		);
	    $return=$fotos->insert($arreglo);
	    echo $return;
	    exit();
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
    	$output_dir = $_SERVER['DOCUMENT_ROOT']."/imagenes/alquiler/";
		if(isset($_FILES["myfile"]))
		{
			$ret = array();
			
			$unique=substr(md5(uniqid(rand())), 0, 6);
			$error =$_FILES["myfile"]["error"];
			//You need to handle  both cases
			//If Any browser does not support serializing of multiple files using FormData() 
			if(!is_array($_FILES["myfile"]["name"])) //single file
			{
		 	 	$fileName = $unique."_".$_FILES["myfile"]["name"];
		 		move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
		 		array_push($ret,$fileName);
			}
			else  //Multiple files, file[]
			{
			  $fileCount = count($_FILES["myfile"]["name"]);
			  for($i=0; $i < $fileCount; $i++)
			  {
			  	$fileName = $unique."_".$_FILES["myfile"]["name"][$i];
				move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
			  	array_push($ret,$fileName);
			  }
			
			}
		    echo json_encode($ret);
		 }
		 exit();
	}

}
