<?php

class NovedadesController extends Zend_Controller_Action
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
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => 'class="activo"',
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
        $Novedades = new Application_Model_DbTable_novedades();
        $arrNovedades = $Novedades->getDetalle($param["id"]);
        $this->view->arrNovedades = $arrNovedades;
        $layout = $this->_helper->layout(); 
        $this->view->metaKeywords="Noticias,Actividades,Novedades,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription=substr(strip_tags($arrNovedades[0]["nov_descripcion"]),0,140);
        $this->view->title = $arrNovedades[0]["nov_titulo"];
        $this->view->canonica = "noticias-y-actividades/".$arrNovedades[0]["nov_keyword"];
        // Me fijo si quiere la clave
        $clave = $this->_getParam("clave");
        if ($clave == CLAVE_JSON){
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            foreach ($arrNovedades[0] as $key => $value) {
                $arrNovedades[0][$key] = strip_tags(utf8_encode($value),"<br>");
            }
            $respuesta = json_encode($arrNovedades,JSON_UNESCAPED_UNICODE);
            echo $respuesta;
            exit();
        }

        $arrNovedades5 = $Novedades->get(5,null,$arrNovedades[0]["cn_id"],$arrNovedades[0]["nov_id"]);
        $this->view->arrNovedades5 = $arrNovedades5;
        
    }

    public function indexAction()
    {
        $Novedades = new Application_Model_DbTable_novedades();
        $arrNovedades = $Novedades->get(20);
        $this->view->arrNovedades = $arrNovedades;
        $this->view->metaKeywords="Noticias,Actividades,Novedades,Interes,Publico,El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Todas las noticias relevantes de la Zona Balnearia, desde El Condor, hasta Las Grutas, todo sobre el camino, las rutas y demas cuestioens que nos interesan";
        $this->view->title = "Novedades, Noticias del Balneario el Condor";
        $this->view->canonica = "noticias-y-actividades";
    }

    public function getultimanovedadAction(){
        header('Content-Type: application/json');
        $clave = $this->_getParam("clave");
        // DATOS
        $Novedades = new Application_Model_DbTable_novedades();
        $arrNovedades = $Novedades->get(1);
        if ($clave == CLAVE_JSON){
            $log = new Application_Model_DbTable_log();
            $log->insert(array("lo_fecha" => date("Y-m-d h:i:s"),"lo_msj" => NOVEDADES_ULTIMA));
            header('Access-Control-Allow-Origin: *');
            foreach ($arrNovedades[0] as $key => $value) {
                    $arrNovedades[0][$key] = strip_tags(utf8_encode($value),"<br>");
            }
            
            $respuesta = json_encode($arrNovedades,JSON_UNESCAPED_UNICODE);
            switch (json_last_error ()) {
                case JSON_ERROR_NONE :
                    $this->request ["status"] = ' - Sin errores';
                    break;
                case JSON_ERROR_DEPTH :
                    $this->request ["status"] = ' - Excedido tamano maximo de la pila';
                    break;
                case JSON_ERROR_STATE_MISMATCH :
                    $this->request ["status"] = ' - Desbordamiento de buffer o los modos no coinciden';
                    break;
                case JSON_ERROR_CTRL_CHAR :
                    $this->request ["status"] = ' - Encontrado caracter de control no esperado';
                    break;
                case JSON_ERROR_SYNTAX :
                    $this->request ["status"] = ' - Error de sintaxis, JSON mal formado';
                    break;
                case JSON_ERROR_UTF8 :
                    $this->request ["status"] = ' - Caracteres UTF-8 malformados, posiblemente estan mal codificados';
                    break;
                default :
                    $this->request ["status"] = ' - Error desconocido';
                    break;
            }
            //if (isset($this->request)) print_r($this->request);
            echo $respuesta;
            exit();
        }
        exit();

    }
}

