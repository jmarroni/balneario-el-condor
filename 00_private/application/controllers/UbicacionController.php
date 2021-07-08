<?php

class UbicacionController extends Zend_Controller_Action
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
                        "Ubicacion"     => 'class="activo"',
                        "Servicio"      => '',
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

    }

    public function indexAction()
    {
        $this->view->metaKeywords="GPS,Ubicacion,referencia,como,llegar,lugar,latitud,longitud,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="A 30 kilometros de Viedma, donde se unen el rio Negro y el mar, se encuentra el balneario El Condor, Portal de Ingreso a la Patagonia Atlantica. Caracterizado por la extension de sus playas y la presencia de la Colonia de loros mas grande del mundo.";
        $this->view->title = "Ubicacion geografica del Balneario el Condor";
        
    }

}

