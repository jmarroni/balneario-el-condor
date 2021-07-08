<?php

class NocturnosController extends Zend_Controller_Action
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
                "Home"          => '',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Informacion"   => '',
                "Historia"      => '',
        		"Lugares"		=> '',
        		"Nocturno"		=> 'active'
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

    public function detalleAction()
    {
        $param = $this->_getAllParams();
        // DATOS
        $objNocturno = new Application_Model_DbTable_nocturno();
        $Nocturno = $objNocturno->getDetalle($param["id"]);
        $this->view->Nocturno = $Nocturno;
        $this->view->metaKeywords="Playas,PLaya,Vistas,Acantilados,Peces,Pesca,Pescados,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($Nocturno[0]["no_descripcion"]),0,140);
        $objNocturno->update(array("no_visitas" => ($Nocturno[0]["no_visitas"] + 1)),"no_id = ".$Nocturno[0]["no_id"]);
    }

    public function indexAction()
    {
        // DATOS
        $objNocturno = new Application_Model_DbTable_nocturno();
        $Nocturno = $objNocturno->get();
        $this->view->Nocturno = $Nocturno;
        $this->view->metaKeywords="Playas,PLaya,Vistas,Acantilados,Peces,Pesca,Pescados,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Lugares donde quedarse despierto toda la noche, amanecer en la playa ubicando los pub's y boliches de la zona Balnearia";
        $this->view->title = "Confiterias, pubs, boliches en el Condor";
        $this->view->canonica = "boliches-pub";
        $clave = $this->_getParam("clave");
        if (isset($clave) && ($clave == "CarmenLuca19812012")){
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($Nocturno);
            exit();
        }
    }
}

