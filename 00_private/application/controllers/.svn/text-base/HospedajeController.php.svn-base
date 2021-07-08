<?php

class HospedajeController extends Zend_Controller_Action
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
                "home"          => '',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Informacion"   => '',
                "Historia"      => '',
        		"Hospedaje"		=> 'active'
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
        $objHospedaje = new Application_Model_DbTable_hospedaje();
        $Hospedaje = $objHospedaje->getDetalle($param["id"]);
        $this->view->Hospedaje = $Hospedaje;

        $this->view->metaKeywords="Camping,Casas,Departamentos,Alquiler,Venta,Alojamiento,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($Hospedaje[0]["ho_descripcion"]),0,50);
        $this->view->title = $Hospedaje[0]["ho_titulo"];
        $objHospedaje->update(array("ho_visitas" => ($Hospedaje[0]["ho_visitas"] + 1)),"ho_id = ".$Hospedaje[0]["ho_id"]);

    }

    public function indexAction()
    {
        // DATOS
        $objHospedaje = new Application_Model_DbTable_hospedaje();
        $Hospedaje = $objHospedaje->get();
        $this->view->Hospedaje = $Hospedaje;
        $this->view->metaKeywords="Camping,Casas,Departamentos,Alquiler,Venta,Alojamiento,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Encuentra en esta seccion todo lo necesario para hospedarte en la Villa Maritima, desde Camipng donde armar la carpa si venis de mochilero, hasta casas con vista al mar para aprovechar en familia";
        $this->view->title = "Hoteles, Camping y cabañas para alojarse";
    }
}

