<?php

class ImagenController extends Zend_Controller_Action
{

    public function init()
    {
        $Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="radiusPrimero"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => 'class="activo"',
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


    public function indexAction()
    {
        $autor = $this->_getParam("autor");
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
        $this->view->title = "Imagenes de playas, mares y la gente del Balneario";
        $this->view->description = "Todas las imagenes del Balneario El Condor, Nuestra Villa Maritima y sus hermosos paisajes en la galeria imagenes mas grande del Balneario";
        $this->view->canonica = "imagen";
    }

    public function visitaAction(){
        $id = $this->_getParam("id");
        $Imagen = new Application_Model_DbTable_imagenes();
        $Imagenes = $Imagen->get("","im_id = ".intval($id));
        $visita = intval($Imagenes[0]["im_visitas"]) + 1;
        $Imagen->update(array("im_visitas" => ($visita)),"im_id = ".intval($id));
        exit();
    }

    public function concursokayakAction(){
        $autor = "ConcursoPesca";
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
        $this->view->title = "Imagenes de playas, mares y la gente del Balneario";
        $this->view->description = "Todas las imagenes del Balneario El Condor, Nuestra Villa Maritima y sus hermosos paisajes en la galeria imagenes mas grande del Balneario";
        $this->view->canonica = "imagen";
    }
    
    
    public function resizeAction(){
    	$param = $this->_getAllParams();
    	// Abrimos la carpeta que nos pasan como parámetro
	    $dir = opendir($_SERVER['DOCUMENT_ROOT']."/resize/");
	    // Leo todos los ficheros de la carpeta
	    if ($param["clave"] == "130702uade"){
            $imagen = new Custom_SubirImagenes();

		    while ($elemento = readdir($dir)){
		        // Tratamos los elementos . y .. que tienen todas las carpetas
		        if( $elemento != "." && $elemento != ".."){
		            // Si es una carpeta
		            if( is_dir($path.$elemento) ){
		            } else {

                        $Imagen = new Application_Model_DbTable_imagenes();
		                $Imagen->insert(array("im_id" => NULL,
		                						"im_titulo" => $param["autor"],
		                						"im_descripcion" => "",
		                						"im_imagen" => "http://static.balneario-el-condor.com.ar/css/images/repositorio/".$elemento,
		                						"im_thumb" => "http://static.balneario-el-condor.com.ar/css/images/repositorio/".$elemento,
		                						"img_orig" => "http://static.balneario-el-condor.com.ar/css/images/repositorio/".$elemento,
		                						"im_visitas" => "0",
		                						"im_fecha" => date("Y-m-d"),
		                						"im_keyword" => "http://static.balneario-el-condor.com.ar/css/images/repositorio/".$elemento));
		            }
		        }
		    }
	    }
    	exit();
    }

}

