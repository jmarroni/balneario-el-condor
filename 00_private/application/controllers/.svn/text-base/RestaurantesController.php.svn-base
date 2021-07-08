<?php

class RestaurantesController extends Zend_Controller_Action
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
        $Cercanos = new Application_Model_DbTable_gourmet();
        $cercanos = $Cercanos->getDetalle($param["id"]);
        $this->view->cercanos = $cercanos;
        $this->view->metaKeywords="Restaurante,Restaurant,Lugar,Comer,Cenar,Desayunas,Almuerzo,Merienda,Picada,Asado,Parrilla,Gourmet,Pizza,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($cercanos[0]["go_descripcion"]),0,140);
        $this->view->title = $cercanos[0]["go_titulo"];
        $Cercanos->update(array("go_visitas" => ($cercanos[0]["go_visitas"] + 1)),"go_id = ".$cercanos[0]["go_id"]);
        $this->view->canonica = "/restaurantes-y-confiterias/".$cercanos[0]["go_keyword"];
    }

    public function indexAction()
    {
        // DATOS
        $Cercanos = new Application_Model_DbTable_gourmet();
        $cercanos = $Cercanos->get();
        $this->view->cercanos = $cercanos;
        $this->view->metaKeywords="Restaurante,Restaurant,Lugar,Comer,Cenar,Desayunos,Almuerzo,Merienda,Picada,Asado,Parrilla,Gourmet,Pizza,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Todos los lugares del balneario para ir a comer, cenar, comer una Pizza con amigos o tomar algo temprano, lugar y descripcion del mismo";
        $this->view->title = "Restaurantes, Lugares para comer en el Balneario";
        $this->view->canonica = "restaurantes-y-confiterias";
    }
}

