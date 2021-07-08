<?php

class Application_Model_DbTable_cercanos extends Zend_Db_Table_Abstract
{
 
    protected $_ce_id;
    protected $_ce_titulo;
        
    public function setce_id($ce_id){
        $this->_ce_id = $ce_id;
    }
    
    public function getce_id(){
        return $this->_ce_id;
    }
    
    public function setce_descripcion($ce_descripcion){
        $this->_ce_descripcion = $ce_descripcion;
    }
    
    public function getce_descripcion(){
        return $this->_ce_descripcion;
    }

    public function setce_googlemaps($ce_googlemaps){
        $this->_ce_googlemaps = $ce_googlemaps;
    }
    
    public function getce_googlemaps(){
        return $this->_ce_googlemaps;
    }
    
    public function setce_direccion($ce_direccion){
        $this->_ce_direccion = $ce_direccion;
    }
    
    public function getce_direccion(){
        return $this->_ce_direccion;
    }

    public function setcat_id($cat_id){
        $this->_cat_id = $cat_id;
    }
    
    public function getcat_id(){
        return $this->_cat_id;
    }

    public function setce_visitas($ce_visitas){
        $this->_ce_visitas = $ce_visitas;
    }
    
    public function getce_visitas(){
        return $this->_ce_visitas;
    }

    public function setce_keyword($ce_keyword){
        $this->_ce_keyword = $ce_keyword;
    }
    
    public function getce_keyword(){
        return $this->_ce_keyword;
    }
    
    public function setce_titulo($ce_titulo){
        $this->_ce_titulo = $ce_titulo;
    }
    
    public function getce_titulo(){
        return $this->_ce_titulo;
    }
    
    
    


public function __construct(array $options = null)
{
        if (is_array($options)){
            $this->setOptions($options); 
        }
}


public function setOptions(array $options){

    $this->setce_id((isset($options["ce_id"]))?$options["ce_id"]:"");
    $this->setce_descripcion((isset($options["ce_descripcion"]))?$options["ce_descripcion"]:"");
    $this->setce_googlemaps((isset($options["ce_googlemaps"]))?$options["ce_googlemaps"]:"");
    $this->setce_direccion((isset($options["ce_direccion"]))?$options["ce_direccion"]:"");
    $this->setcat_id((isset($options["cat_id"]))?$options["cat_id"]:"");
    $this->setce_keyword((isset($options["ce_keyword"]))?$options["ce_keyword"]:"");
    $this->setce_visitas((isset($options["ce_visitas"]))?$options["ce_visitas"]:"");
    $this->setce_titulo((isset($options["ce_titulo"]))?$options["ce_titulo"]:"");
    
}
 
}