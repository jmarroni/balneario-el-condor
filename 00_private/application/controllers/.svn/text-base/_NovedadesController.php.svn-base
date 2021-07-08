<?php

class NovedadesController extends Zend_Controller_Action
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
                "Imagenes"      => '',
                "Novedades"     => 'class="activo"',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Informacion"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
    }

    public function detalleAction()
    {
        $param = $this->_getAllParams();
        // DATOS
        $Novedades = new Application_Model_DbTable_novedades();
        $arrNovedades = $Novedades->getDetalle($param["id"]);

        $this->view->arrNovedades = $arrNovedades;
        $layout = $this->_helper->layout();
        $view->view->metaKeywords="Noticias,Actividades,Novedades,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($arrNovedades[0]["nov_descripcion"]),0,140);
        $this->view->title = $arrNovedades[0]["nov_titulo"];
        $this->view->canonica = "noticias-y-actividades/".$arrNovedades[0]["nov_keyword"];
        
    }

    public function indexAction()
    {
        $Novedades = new Application_Model_DbTable_novedades();
        $arrNovedades = $Novedades->get(20);
        $this->view->arrNovedades = $arrNovedades;
        $this->view->metaKeywords="Noticias,Actividades,Novedades,Interes,Publico,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Todas las noticias relevantes de la Zona Balnearia, desde El Condor, hasta Las Grutas, todo sobre el camino, las rutas y demas cuestioens que nos interesan";
        $this->view->title = "Novedades, Noticias del Balneario el Condor";
        $this->view->canonica = "noticias-y-actividades";
    }
}

