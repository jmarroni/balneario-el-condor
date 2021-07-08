<?php

class RecetasController extends Zend_Controller_Action
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
        $Recetas = new Application_Model_DbTable_recetas();
        $arrRecetas = $Recetas->getDetalle($param["keyword"]);
        $this->view->arrRecetas = $arrRecetas;
        $this->view->metaKeywords="Noticias,Actividades,Novedades,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($arrRecetas[0]["rec_descripcion"]),0,140);
        $this->view->title = $arrRecetas[0]["rec_titulo"];
        $this->view->canonica = "recetas/".$arrRecetas[0]["rec_keyword"];
    }

    public function indexAction()
    {
        $Recetas = new Application_Model_DbTable_recetas();
        $arrRecetas = $Recetas->get(20);
        $this->view->arrRecetas = $arrRecetas;
        $this->view->metaKeywords="Noticias,Actividades,Novedades,Interes,Publico,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Todas las noticias relevantes de la Zona Balnearia, desde El Condor, hasta Las Grutas, todo sobre el camino, las rutas y demas cuestioens que nos interesan";
        $this->view->title = "Novedades, Noticias del Balneario el Condor";
        $this->view->canonica = "recetas";
    }
}

