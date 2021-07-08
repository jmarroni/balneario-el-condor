<?php

class NewsletterController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function suscripcionAction(){
        $param = $this->_getAllParams();
        $newsletter = new Application_Model_DbTable_newsletter();

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
           $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
            $ip = $_SERVER['REMOTE_ADDR'];



        $newsletter->insert(array(
                                'nw_mail'   => $param["suscripcion"],
                                'nw_ip'     => $ip,
                                'nw_fecha'  => date('Y-m-d h:i:s'),
                                'nw_envio'  => 1
                                ));
        echo '{"Mensaje":"Se ha ingresado correctamente el mail en la base de datos, pronto tendra noticias nuestras!!"}';
       // echo "1";
        exit();

    }

}

