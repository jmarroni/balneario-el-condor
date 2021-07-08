<?php

class ContactoController extends Zend_Controller_Action
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

    }

    public function colectivoAction(){

        $this->view->metaKeywords="colectivo,horario,recorrido,ida,vuelta,tomar,parada,boleto,ceferino,empresa";
        $this->view->metaDescription="Días, Horarios y Recorrido del Colectivo que conecta el Balneario El Condor con Viedma ida y vuelta";
        $this->view->title = "Recorrido en Colectivo";
    }
    public function indexAction()
    {
        $this->view->metaKeywords="Contactenos, Contactese, Mail de Encuentro, El Condor,Balneario,Viedma,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Contactese con nosotros por información, promociones o información que quiera subamos al sitio web";
        $this->view->title = "Contacto Balneario El Condor";
    }

    public function envioAction(){
        $param = $this->_getAllParams();
        $newsletter = new Application_Model_DbTable_newsletter();

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
         $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        // // Tengo que mandar el mail :)
        // mail('contacto@balneario-el-condor.com.ar','[Sitio Web] - '.$param["asuntoForm"].','.$param["deForm"].' - '.$param["correoForm"].' - '.$param["mensajeForm"]);
        // echo '{"Mensaje":"Se ha ingresado correctamente el mail en la base de datos, pronto tendra noticias nuestras!!"}';

            $to = 'contacto@balneario-el-condor.com.ar';
            $subject = $param["asuntoForm"];
            $message = 'De: '.$param["deForm"].'-Ip: '.$ip.'-Teléfono: '.$param["asuntoForm"].'\n'.$param["mensajeForm"];
        // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            if(mail($to, $subject, $message ,$headers)){
                echo '{"Mensaje":"Se ha ingresado correctamente el mail en la base de datos, pronto tendra noticias nuestras!!"}';
            }
            else{
                echo '{"Mensaje":"Ocurrió un error al enviar el mail!!"}';
            }

        }
        exit();

    }
}

