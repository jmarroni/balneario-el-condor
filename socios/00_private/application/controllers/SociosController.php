<?php

class SociosController extends Zend_Controller_Action
{
    public function init()
    {
    	/* Initialize action controller here */
	  	$this->_helper->layout()->setLayout('layout-admin');
		if (!Zend_Auth::getInstance()->hasIdentity()) {
         $this->_redirect('/');
         exit();}
        $this->view->us_nombre = Zend_Auth::getInstance()->getIdentity()->us_nombre;
		$this->view->us_imagen = Zend_Auth::getInstance()->getIdentity()->us_imagen;
        
		//$this->view->ad_nombre_apellido = $ad_nombre_apellido;
		//$this->view->ad_imagen = $ad_imagen;
        $this->view->menu ='socios';
    }

    public function indexAction()
    {
    	$this->_redirect('/');
         exit();
    }

    public function listadoAction(){
        $objSocios = new Application_Model_DbTable_Socios();
        $this->view->socios = $objSocios->get();
    }

    public function toexcelAction(){
        $this->_helper->layout->disableLayout();
        $objSocios = new Application_Model_DbTable_Socios();
        $this->view->socios = $objSocios->get();
    }

    public function accionAction(){
        $id = $this->_getParam("id");

        if (isset($id)){
            $objSocios = new Application_Model_DbTable_Socios();
            $arrSocio = $objSocios->get(array("id" => $id));
            $this->view->arrSocio = $arrSocio;
        }else{
        }

    }


   public function saveAction()
    {

      $tabla = new Application_Model_DbTable_Socios();
      $id = $this->_getParam("IdSocio");

      $post = $this->_getAllParams();

      
      if ($post["FechaNacimiento"] != "00-00-00"){
        $fecha = explode("/", $post["FechaNacimiento"]);
        $post["FechaNacimiento"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["FechaNacimiento"] = NULL;

      if ($post["FechaNacConyuge"] != "00-00-00"){
        $fecha = explode("/", $post["FechaNacConyuge"]);
        $post["FechaNacConyuge"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["FechaNacConyuge"] = NULL;

      if ($post["FechaNacHijo1"] != "00-00-00"){
        $fecha = explode("/", $post["FechaNacHijo1"]);
        $post["FechaNacHijo1"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["FechaNacHijo1"] = NULL;

      if ($post["FechaNacHijo2"] != "00-00-00"){
        $fecha = explode("/", $post["FechaNacHijo2"]);
        $post["FechaNacHijo2"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["FechaNacHijo2"] = NULL;

      if ($post["FechaNacHijo3"] != "00-00-00"){
        $fecha = explode("/", $post["FechaNacHijo3"]);
        $post["FechaNacHijo3"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["FechaNacHijo3"] = NULL;




        if (isset($id) && $id != "") {
            //esta actualizando
         // print_r();
            unset($post["controller"]);
            unset($post["module"]);
            unset($post["action"]);
            unset($post["IdSocio"]);
            $update = $tabla->update($post,"IdSocio = $id");
        } else {
            //inserta el carrusel (funciona)
            $insert = $tabla->insertNovedades($post);
            if (substr($insert, 0, 16) == 'Ocurrio un error'){
               echo json_encode(array("proceso" => "error"));
               exit();
            }
            $id = $insert;
            
        }

        
        echo json_encode(array("proceso" => "OK"));
        exit();
    }

    public function eliminarAction(){
        $tabla = new Application_Model_DbTable_Novedades();
        $id = $this->_getParam("id");
        $id = intval($id);
        $tabla->eliminarNovedades($id);
        echo json_encode(array("proceso" => "OK"));
        exit();
    }

}