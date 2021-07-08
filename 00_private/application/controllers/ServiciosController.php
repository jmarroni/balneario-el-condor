<?php

class ServiciosController extends Zend_Controller_Action{
	protected $Servicios;
	
	public function init()
	{
		$this->Servicios = new Application_Model_DbTable_servicios();
		$Mareas = new Application_Model_DbTable_mareas();
		$arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
		$this->view->arrMareas = $arrMareas;

		$Clima = new Application_Model_DbTable_clima();
		$arrClima = $Clima->get(1);
		$this->view->arrClima = $arrClima;

		$this->view->param = array(
				"home"          => 'class="radiusPrimero"',
				"Ubicacion"     => '',
				"Servicio"      => 'class="activo"',
				"Imagenes"      => '',
				"Novedades"     => '',
				"Clasificados"  => '',
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
		public function detalleAction()
		{
			$param = $this->_getAllParams();
			// DATOS
			$arrServicios = $this->Servicios->getDetalle($param["id"]);
			$this->view->arrServicios = $arrServicios;
			$this->view->title = $arrServicios[0]["ser_titulo"];
			$this->view->metaDescription=substr(strip_tags($arrServicios[0]["ser_keyword"]),0,140);
			$this->view->canonica = "servicios/".$arrServicios[0]["ser_keyword"];

		}

		public function indexAction()
		{
			$mensaje=$this->_getParam("mensaje");
			$arrServicios = $this->Servicios->get();
			$this->view->arrServicios = $arrServicios;
			$this->view->metaDescription = "Servicios que brinda nuestra Villa Maritima al turista, y a todo aquel que vivia allí, pesca, deportes, travesias";
			$this->view->metaKeywords="Servicios Del Balneario el Condor, profesionales, enseñanza, equipos de pesca, pesca, travesias, paseos, guiados, temporada, alta, baja, diciembre, enero, febrero, semana santa";
			$this->view->title = "Servicios del Balneario El Condor";
			if($mensaje !=''){
				$this->view->mensaje="error"; 
				$this->view->errorMensaje="error";
			}
			$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
			$this->view->reCaptcha = $recaptcha->getHTML();
			$this->view->canonica = "servicios";
		}
		
		
		public function cargarservicioAction(){
			/*$recaptcha = new Zend_Service_ReCaptcha("6LdA1uISAAAAAGwv8Sho0NynciwJ5dbYZuausU_e", "6LdA1uISAAAAAGJt12gMlBP2z-MGUkUItUYh-dVf");
			$result = $recaptcha->verify(
			    $_POST['recaptcha_challenge_field'],
			    $_POST['recaptcha_response_field']
			);
			if (!$result->isValid()) {
				$this->_redirect("/servicios/index/mensaje/error en captcha");

			}*/
			
			$param = $this->_getAllParams();
			$img = explode('imagenes/servicios/', $param["archivo"]);
			$imagenes=$this->uploadFile();
			$keyword = str_replace("Ñ", "NI",str_replace("Ú", "U",str_replace("Ó", "O",str_replace("Í", "I",str_replace("É", "E",str_replace("Á", "A",str_replace("ñ", "ni",str_replace("ú", "u",str_replace('ó', "o",str_replace("í", "i",str_replace("é", "e",str_replace("á", "a",str_replace(" ", "-",$param["titulo"] )))))))))))));
			$return=$this->Servicios->insertServicio($param["titulo"], $param["descripcion"], $img[1], $param["nombre"], $param["correo"], date("Y-m-d h:i:s"), '', $param["direccion"], $keyword);
			if($return == 'error'){
				$this->_redirect("/servicios/index/mensaje/".$return);
			}else{
				echo $keyword;exit();
				//$this->_redirect("/servicios/detalle/id/".$keyword);
			}
		}
		
		public function uploadAction(){
		$uploadImage = new Custom_SubirImagenes();
        $uploadImage->setpath($_SERVER['DOCUMENT_ROOT'].'/imagenes/servicios/');
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
			$upload->setDestination($_SERVER['DOCUMENT_ROOT'].'/imagenes/servicios/');
			$parametros = array("PDF" => "", "IMAGEN" => "");
		
			foreach ($upload->getFileInfo() as $info) {
		
				if ($info['name'] != '') {
					$exts = explode(".", $info['name']);
					$n = count($exts) - 1;
					$ext = $exts[$n];
		
					$nombre = substr(md5(uniqid(rand())), 0, 6) . '_' . $info['name'];
					$baseurl = $_SERVER['DOCUMENT_ROOT'].'/imagenes/servicios/';
		
					$target = $baseurl . $nombre;
					$upload->addFilter('Rename', array('target' => $target));
					if ($upload->receive($info['name'])){
						$titulo = "Se realizo el upload de una imagen";
						$para = "jmarroni@gmail.com";
						$mensaje = "Enviaron la siguiente imagen a el balneario servicios ".$target;
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
					}
				}
				//}
			}
			return $nombre;
		}
	}