<?php

class Application_Model_DbTable_climaextendido extends Zend_Db_Table_Abstract
{
	protected $id;
    protected $fecha_actual;
    protected $fecha_numero;
    protected $condicion;
    protected $temp_alta;
    protected $temp_baja;
    protected $icono;
    
    public function __construct(array $options = null)
    {
        if (is_array($options)){
            $this->setOptions($options); 
        }
    }

    public function setOptions(array $options){


        $this->setid($options["id"]);
        $this->setfecha_actual($options["fecha_actual"]);
        $this->setfecha_numero($options["fecha_numero"]);
        $this->setcondicion($options["condicion"]);
        $this->settemp_alta($options["temp_alta"]);
        $this->settemp_baja($options["temp_baja"]);
        $this->seticono($options["icono"]);
        
    }

    // Setter
    public function setid($id){
        $this->_id = (isset($id))?$id:"";
    }

    public function setfecha_actual($fecha_actual){
        $this->_fecha_actual = (isset($fecha_actual))?$fecha_actual:"";
    }

    public function setfecha_numero($fecha_numero){
        $this->_fecha_numero = (isset($fecha_numero))?$fecha_numero:"";
    }

    public function setcondicion($condicion){
        $this->_condicion = (isset($condicion))?$condicion:"";
    }

    public function settemp_alta($temp_alta){
        $this->_temp_alta = (isset($temp_alta))?$temp_alta:"";
    }

    public function settemp_baja($temp_baja){
        $this->_temp_baja = (isset($temp_baja))?$temp_baja:"";
    }
    
    public function seticono($icono){
        $this->_icono = (isset($icono))?$icono:"";
    }


    // Getter
    public function getid(){
        return $this->_id;
    }

    public function getfecha_actual(){
        return $this->_fecha_actual;
    }

    public function getfecha_numero(){
        return $this->_fecha_numero;
    }

    public function getcondicion(){
        return $this->_condicion; 
    }

    public function gettemp_alta(){
        return $this->_temp_alta;
    }

    public function gettemp_baja(){
        return $this->_temp_baja;
    }

    public function geticono(){
        return $this->_icono;
    }

}