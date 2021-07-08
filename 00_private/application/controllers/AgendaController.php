<?php

class AgendaController extends Zend_Controller_Action{
	protected $agendas;
	public function init() {

		$this->view->metaKeywords="noche de Bares, El Condor,Balneario,Al Reparo, Lupe´s, Trentinos, Arenas del Sur";
        $this->view->metaDescription="Noche de los bares, presente en el Balneario el Condor el sabado 25 de febrero de 2017";


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
	
	public function redireccionAction(){
		$param = $this->_getAllParams();
		Header("HTTP/1.1 301 Moved Permanently");
		Header("Location: http://www.balneario-el-condor.com.ar/agenda/detalle/".$param["seguido"]);
	}

	public function detalleAction()
	{
		$param = $this->_getAllParams();
		// DATOS
		//         $Clasificados = new Application_Model_DbTable_clasificados();
		$arrAgendas = $this->agendas->getDetalle($param["espectaculo"]);

		
		$this->view->arrAgendas = $arrAgendas;
		$this->view->title = $arrAgendas[0]["ag_titulo"];
		//$this->agendas->updateVisitas($arrAgendas[0]["ag_id"], array("ag_visitas" => $arrAgendas[0]["ag_visitas"] + 1));
		$this->view->metaKeywords="Agenda, Verano, Playa, Espectaculos, Recitales, Teatro, Titeres";
		$this->view->metaDescription=$arrAgendas[0]["ag_titulo"]." - ".substr(strip_tags($arrAgendas[0]["ag_descripcion_corta"]),0,140);

	}

	function indexAction()
	{
        $clave = $this->_getParam("clave");
        if ($clave == CLAVE_JSON) {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            $log = new Application_Model_DbTable_log();
            $log->insert(array("lo_fecha" => date("Y-m-d h:i:s"), "lo_msj" => AGENDA));
            $arrAgendas =$this->agendas->get(1);
            for ($i = 0;$i < sizeof($arrAgendas); $i ++){
                foreach ($arrAgendas[$i] as $key => $value){
                    $arrAgendas[$i][$key] = utf8_encode($value);
                }
            }
            echo json_encode($arrAgendas);exit();

        }
		//         $Clasificados = new Application_Model_DbTable_clasificados();
		$mensaje=$this->_getParam("mensaje");
		$arrAgendas =$this->agendas->get(1);
		$this->view->arrAgendas = $arrAgendas;
		// $arrDestacados =$this->agendas->get(1);
		// $this->view->arrDestacados = $arrDestacados;
		$this->view->metaDescription="agenda, calendario, actividades, verano, voley, titeres, cine, show, espectaculos, recitales";
		$this->view->title = "Agenda del Balneario El Condor";
		if($mensaje !=''){
			$this->view->mensaje="error";
			$this->view->errorMensaje="error";
		}
		$this->view->reCaptcha = "";
		$this->view->canonica = "agenda";
	}

    public function nochebaresAction(){
    }
	
}
