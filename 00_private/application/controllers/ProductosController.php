<?php

class ProductosController extends Zend_Controller_Action{
	public function init() {

		$this->view->metaKeywords="Pesca, accesorios, productos, cañas, reel, linternas, carnada";
        $this->view->metaDescription="Todo lo que necesitas para ir de pesca lo conseguis en nuestro e-shop";


		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');*/
		$this->agendas = new Application_Model_DbTable_agenda();
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
				"Fauna"         => '',
                "Informacion"   => '',
				"Historia"      => 'class="radiusUltimo"'
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
	


	public function reelAction()
	{
		$this->view->title = "Reel SeaMaster 5000";
	}

	public function linternaAction()
	{
		$this->view->title = "Linterna de cabeza";
	}

	public function comprarAction(){
		date_default_timezone_set ("America/Argentina/Buenos_Aires");
		
		$params = $this->_getAllParams();
		$objClientes = new Application_Model_DbTable_clientes();
		$cliente_insert = array('cl_nombre' 		=> $params["nombre"], 
								'cl_apellido' 		=> $params["apellido"],
								'cl_mail' 			=> $params["mail"],
								'cl_telefono' 		=> $params["telefono"],
								'cl_direccion' 		=> $params["direccion"],
								'cl_comentario' 	=> $params["comentario"],
								'cl_medio_pago' 	=> $params["tipo_pago"],
								'cl_precio' 		=> 1400,
								'cl_fecha' 			=> date("Y-m-d H:i:s"));
		echo $objClientes->insert($cliente_insert);
		mail("jmarroni@gmail.com", "Comenzo un proceso de compra Realizada el usuario".$params["nombre"].", ".$params["apellido"], "Se comenzo un proceso de compra, los datos, ".implode(", ", $cliente_insert));
		exit();
	}

    public function nochebaresAction(){
    }
	
}
