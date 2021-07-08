<?php

class MercadopagoController extends Zend_Controller_Action
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

    public function irapagarAction()
    {
        include("/var/www/virtual/balneario-el-condor.com.ar/00_private/application/MercadoPago/mercadopago.php");
        $mp = new MP ("977138487047422", "mkwDI3OwrNmpVyGcNbOPIbflvf1R2HkO");

        // $access_token = $mp->get_access_token();
        $cliente = $this->_getParam("cliente");
        $preference_data = array (
                                "items" => array (
                                    array (
                                        "title" => "Reel SeaMaster 5000",
                                        "quantity" => 1,
                                        "currency_id" => "ARS",
                                        "unit_price" => 1400.00,
                                        "picture_url" => "http://static.balneario-el-condor.com.ar/imagenes/productos/reel_seaMaster_5000_3.jpg"
                                    )
                                ),
                                "back_urls" => array(
                                    "success" => "http://www.balneario-el-condor.com.ar/productos/reel",
                                    "failure" => "http://www.balneario-el-condor.com.ar/productos/reel",
                                    "pending" => "http://www.balneario-el-condor.com.ar/productos/reel"
                                ),
                                "auto_return" => "approved",
                                "notification_url" => "http://www.balneario-el-condor.com.ar/mercadopago/ipn",
                                "external_reference" => $cliente,
                                "expires" => false
                            );

        $preference = $mp->create_preference($preference_data);

        $this->_redirect($preference["response"]["init_point"]);
        
        exit();
    }


    public function exitoAction(){
        $param = $this->_getAllParams();
        $print_r($param);
        mail("jmarroni@gmail.com", "Compra Realizada", "Se realizo una compra con exito");
        exit();
    }

    public function fracasoAction(){
        $param = $this->_getAllParams();
        $print_r($param);
        mail("jmarroni@gmail.com", "Fallo una Compra", "Se realizo una compra pero fallo");
        exit();
    }

    public function pendienteAction(){
        $param = $this->_getAllParams();
        $print_r($param);
        mail("jmarroni@gmail.com", "Compra Pendiente", "Se realizo una compra quedo pendiente");
        exit();
    }

    public function ipnAction(){
        $param = $this->_getAllParams();
        $print_r($param);
        mail("jmarroni@gmail.com", "Compra IPN", "Se realizo una compra quedo ipn");
        exit();
    }
}

