<?php

class EncuestaController extends Zend_Controller_Action
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
                "Novedades"     => '',
                "Clasificados"  => '',
                "Fauna"         => '',
                "Informacion"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
    }

    public function fundacionAction()
    {
        $param = $this->_getAllParams();
        // DATOS
        $objEncuesta = new Application_Model_DbTable_encuesta();
        $Encuesta = $objEncuesta->get(1,10);
        $this->view->Encuesta = $Encuesta;

        $conabilizacionEncuesta = $objEncuesta->getCantidadVotos(1);
        $Cvotos = array();
        foreach ($conabilizacionEncuesta as $key => $value) {
          $Cvotos[$value["en_opcion"]] = $value["idEncuesta"];
        }
        $this->view->conabilizacionEncuesta = $Cvotos;
        

        $this->view->metaKeywords="Encuesta, Fundacion, Balneario, El Condor, Massini, Hundimiento, Naufragio";
        $this->view->metaDescription="Encuesta - Cuando se fundo el balneario el condor, dejanos tu opinion";

    }

    public function fundacionpostAction(){
        $param = $this->_getAllParams();
        if (isset ($param["fecha"]) && $param["fecha"] > 0){
            $objEncuesta = new Application_Model_DbTable_encuesta();
            /*
            $Encuesta = $objEncuesta->insert(array("en_id_grupo" => 1,
                "en_opcion" => $param["fecha"],
                "en_comentario" => $param["comentario"],
                "en_fecha" => date('Y-m-d h:i:s'),
                "en_ip" => $this->getrealip(),
                "en_mail" => $param["mail"],
                "en_acepto" => (isset($param["deseo"]))?$param["deseo"]:"0"
                ));
*/
            $conabilizacionEncuesta = $objEncuesta->getCantidadVotos(1);
            $Cvotos = array();
            foreach ($conabilizacionEncuesta as $key => $value) {
              $Cvotos[$value["en_opcion"]] = $value["idEncuesta"];
            }
            $this->view->conabilizacionEncuesta = $Cvotos;
            // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
           // mail('jmarroni@gmail.com',"[Balneario El Condor] - Se realizo un voto",'Datos '.implode("|", $param),$cabeceras);
        }
    }

    private function getrealip()
    {
     
       if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
       {
          $client_ip = 
             ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
                $_SERVER['REMOTE_ADDR'] 
                : 
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
                   $_ENV['REMOTE_ADDR'] 
                   : 
                   "unknown" );
     
          // los proxys van añadiendo al final de esta cabecera
          // las direcciones ip que van "ocultando". Para localizar la ip real
          // del usuario se comienza a mirar por el principio hasta encontrar 
          // una dirección ip que no sea del rango privado. En caso de no 
          // encontrarse ninguna se toma como valor el REMOTE_ADDR
     
          $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);
     
          reset($entries);
          while (list(, $entry) = each($entries)) 
          {
             $entry = trim($entry);
             if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
             {
                // http://www.faqs.org/rfcs/rfc1918.html
                $private_ip = array(
                      '/^0\./', 
                      '/^127\.0\.0\.1/', 
                      '/^192\.168\..*/', 
                      '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', 
                      '/^10\..*/');
     
                $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
     
                if ($client_ip != $found_ip)
                {
                   $client_ip = $found_ip;
                   break;
                }
             }
          }
       }
       else
       {
          $client_ip = 
             ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
                $_SERVER['REMOTE_ADDR'] 
                : 
                ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
                   $_ENV['REMOTE_ADDR'] 
                   : 
                   "unknown" );
       }
     
       return $client_ip;
     
    }

    }

