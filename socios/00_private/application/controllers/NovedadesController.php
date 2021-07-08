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
        $this->view->menu ='novedades';
    }

    public function indexAction()
    {
    	$this->_redirect('/');
         exit();
    }

    public function listadoAction(){
        $objNovedades = new Application_Model_DbTable_Novedades();
        $arrNovedades = $objNovedades->getNovedades();
        $relacion_imagen_novedad = new Application_Model_DbTable_Imgarticulos();
        for ($i=0; $i < sizeof($arrNovedades); $i++) { 
            $imagen = $relacion_imagen_novedad->getImg(array("novedades" => $arrNovedades[$i]["nov_id"]));
            if (sizeof($imagen) > 0){
                $arrNovedades[$i]["imagen"] = $imagen[0]["img_nombre"];
            }else{
                $arrNovedades[$i]["imagen"] = RUTA_IMG."/imagenes/novedad/no-disponible.jpg";
            }
        }

        $this->view->arrNovedades = $arrNovedades;
    }

    public function accionAction(){
        $id = $this->_getParam("id");

        if (isset($id)){
            $objNovedades = new Application_Model_DbTable_Novedades();
            $arrNovedades = $objNovedades->getNovedades(array("id" => $id));
            $this->view->arrNovedades = $arrNovedades;

            $relacion_imagen_novedad = new Application_Model_DbTable_Imgarticulos();
            $arrImgenesArticulo = $relacion_imagen_novedad->getImg(array("novedades" => $id));
            $this->view->arrImgenesArticulo = $arrImgenesArticulo;
        }else{
            
        }

    }

    public function uploadAction(){
        $uploadMaterial  = new Custom_SubirImagenes(); 
        $uploadMaterial->setpath(RUTA_FIJA."/htdocs/imagenes/novedad/");
        //$uploadMaterial->setpath("/imagenes/novedad/");
        $uploadMaterial->setconvert_imagick(false);
        $arrMaterial = $uploadMaterial->guardarImagen();
        $materialSubido = $uploadMaterial->getrelative_path().$arrMaterial[0];
        $relacion_imagen_novedad = new Application_Model_DbTable_Imgarticulos();
        $idRelacion = $relacion_imagen_novedad->insertImg(array("nombre" => str_replace(RUTA_FIJA."/htdocs/", "", $materialSubido), "nov_id" => 9999));
        print_r($idRelacion);
        exit();
   }

   public function saveAction()
    {
      $imagen = new Application_Model_DbTable_Imgarticulos();

      $tabla = new Application_Model_DbTable_Novedades();
      $id = $this->_getParam("nov_id");

      $post = $this->_getAllParams();


      if (isset($post["nov_fecha"])){
        $fecha = explode("/", $post["nov_fecha"]);
        $post["fecha"] = $fecha[2]."/".$fecha[1]."/".$fecha[0];
      }else $post["fecha"] = date("Y/m/d");


        if (isset($id) && $id != "") {
            //esta actualizando
         // print_r($post);
            $update = $tabla->updateNovedades($id,$post);
            if ($update != 'ok'){
               echo json_encode(array("proceso" => "error"));
               exit();
            }
        } else {
            //inserta el carrusel (funciona)
            $insert = $tabla->insertNovedades($post);
            if (substr($insert, 0, 16) == 'Ocurrio un error'){
               echo json_encode(array("proceso" => "error"));
               exit();
            }
            $id = $insert;
            
        }

        // Actualizo las imagenes de la novedad
        if ($post["logo"] != ""){
            $relacion_imagen_novedad = new Application_Model_DbTable_Imgarticulos();
            //Borro primero todas las imagenes que pudiera tener
            $arrImagenesArticulos = $relacion_imagen_novedad->getImg(array("novedades" => $id));
            
            $arrLogo = explode(",", $post["logo"]);
            // Elimino las imagenes que se elminaron desde el front
            foreach ($arrImagenesArticulos as $key1 => $value1) {
                $yaExiste = FALSE;
                foreach ($arrLogo as $key => $value) {
                    if ($value1["img_id"] == $value){
                        $yaExiste = TRUE;
                    }
                }
                if (!($yaExiste)) $relacion_imagen_novedad->eliminarImg($value1["img_id"],NULL);
            }
            // Actualizo las imagenes cargadas
            foreach ($arrLogo as $key => $value) {
                $relacion_imagen_novedad->updateImg($value,array("nov_id" => $id));
            }
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