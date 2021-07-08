<?php

class InformacionController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->metaKeywords="El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Balneario El Condor, se encuentra ubicado a 30 km de la ciudad de Viedma, Rio Negro, posee playas de mas 150km de extension.";
        $Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        
        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="activo radiusPrimero"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Informacion"         =>  'class="activo"',
                "Historia"      => 'class="radiusUltimo"'
                );
    }

    public function indexAction()
    {
         $db_Informacion = new Application_Model_DbTable_informacionutil();
         $informacionutil = $db_Informacion->getList();
         $this->view->informacionutil = $informacionutil;
    }

}

