<?php

class FaunaController extends Zend_Controller_Action
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
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => 'class="activo"',
                 "Informacion"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
    }

    public function indexAction()
    {
        $this->view->metaKeywords="Fauna,Animales,Vegetacion,Turismo,Flora,Alternativo,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="La Fauna del Balneario El Condor";
        $this->view->title = "Fauna y Flora que podemos encontrar en el Balneario";
        $this->view->canonica = "fauna";
    }
}

