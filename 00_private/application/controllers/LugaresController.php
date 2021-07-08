<?php

class LugaresController extends Zend_Controller_Action
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
        		"Lugares"		=> 'active'
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
        $clave = $this->_getParam("clave");
        $Cercanos = new Application_Model_DbTable_cercanos();
        $cercanos = $Cercanos->getDetalle($param["nw_id"]);
        if ($clave == CLAVE_JSON) {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            $log = new Application_Model_DbTable_log();
            $log->insert(array("lo_fecha" => date("Y-m-d h:i:s"), "lo_msj" => CERCANOS));
            for ($i = 0;$i < sizeof($cercanos); $i ++){
                foreach ($cercanos[$i] as $key => $value){
                    $cercanos[$i][$key] = utf8_encode($value);
                    if ($key == "ce_googlemaps"){
                        $georeferencia = explode(",",str_replace(")","",str_replace("(","",$cercanos[$i][$key])));
                        $cercanos[$i][$key] = $georeferencia;
                    }
                }
            }
            echo json_encode($cercanos);exit();
        }

        $this->view->cercanos = $cercanos;
        $this->view->metaKeywords="Playas,PLaya,Vistas,Acantilados,Peces,Pesca,Pescados,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($cercanos[0]["ce_descripcion"]),0,140);
        $this->view->title = $cercanos[0]["ce_titulo"];
        $Cercanos->update(array("ce_visitas" => ($cercanos[0]["ce_visitas"] + 1)),"ce_id = ".$cercanos[0]["ce_id"]);
    }

    public function indexAction()
    {
        // DATOS
        $Cercanos = new Application_Model_DbTable_cercanos();
        $cercanos = $Cercanos->get();
        $this->view->cercanos = $cercanos;
                $this->view->metaKeywords="Playas,PLaya,Vistas,Acantilados,Peces,Pesca,Pescados,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Lugares en el Balneario y cercanos al mismo donde pasar el dia de playa, y atractivo turistico para un turismo alternativo en dias de viento";
        $this->view->title = "Lugares, Playas del Balneario el Condor";
    }
}

