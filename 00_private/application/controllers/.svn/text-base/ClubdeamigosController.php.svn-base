<?php

class ClubdeamigosController extends Zend_Controller_Action
{

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
                "home"          => 'class=" activo radiusPrimero"',
                "Ubicacion"     => '',
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

        $Imagenes = new Application_Model_DbTable_imagenes();
        $arrImagenes = $Imagenes->get(99);
        $this->view->arrImagenes = $arrImagenes;
    }

    public function detalleAction()
    {
        $param = $this->_getAllParams();
        // DATOS
        $clubDeAmigos = new Application_Model_DbTable_clubdeamigos();
        $detalleAmigos = $clubDeAmigos->getDetalle($param["cl_id"]);
        $this->view->detalleAmigos = $detalleAmigos;
        $this->view->metaKeywords="Club,Amigos,El Condor,Balneario,Rio Negro,Playa,Verano,La Boca,Balenario Massini,Villa Maritima,".str_replace(" ", ",", $detalleAmigos[0]["cl_titulo"]);
        $this->view->metaDescription=substr($detalleAmigos[0]["cl_descripcion"],0,140);
        $this->view->title = $detalleAmigos[0]["cl_titulo"];
        $this->view->canonica = "club-de-amigos/".$detalleAmigos[0]["cl_keyword"];
    }

    public function indexAction()
    {
        // DATOS
        $clubDeAmigos = new Application_Model_DbTable_clubdeamigos();
        $listadoAmigos = $clubDeAmigos->get();
        $this->view->listadoAmigos = $listadoAmigos;
        $this->view->metaKeywords="Club,Amigos,El Condor,Balneario,Rio Negro,Playa,Verano,La Boca,Balenario Massini,Villa Maritima,El Faro,Picoto";
        $this->view->metaDescription="El Club Social y Deportivo Amigos del Balneario El Condor";
        $this->view->title = "Club de Amigos, noticias y destacados.";
        $this->view->canonica = "club-de-amigos";
    }
}

