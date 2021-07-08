<?php

class CercanosController extends Zend_Controller_Action
{
    public function init()
    {
    	/* Initialize action controller here */
	  	$this->_helper->layout()->setLayout('layout-admin');
		if (!Zend_Auth::getInstance()->hasIdentity()) {
         $this->_redirect('/');
         exit();}
        $ad_nombre_apellido = Zend_Auth::getInstance()->getIdentity()->ad_nombre_apellido;
		$ad_imagen = Zend_Auth::getInstance()->getIdentity()->ad_imagen;
        
		$this->view->ad_nombre_apellido = $ad_nombre_apellido;
		$this->view->ad_imagen = $ad_imagen;
        $this->view->menu ='cercanos';
    }

    public function indexAction()
    {
    	$this->_redirect('/');
         exit();
    }

    public function listadoAction(){
        $tabla = new Application_Model_cercanosMapper();
        $this->view->seccion = "cercanos";

        $param = $this->_getAllParams();

        $registros = $tabla->get();

        //Paginador
        $this->view->cercanos = $registros;
    }

    public function accionAction(){
        
    }

}