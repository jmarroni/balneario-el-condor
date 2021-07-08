<?php
class Application_Model_DbTable_usuarios extends Zend_Db_Table_Abstract
{
 
	protected $_us_id;
	protected $_us_nombre;
	protected $_us_apellido;
	protected $_us_apodo;
	protected $_us_fbc_id;
	protected $_us_twitter;
	protected $_us_clave;
	protected $_us_sexo;
	protected $_us_telefono;
	
	public function setus_telefono($us_telefono){
		$this->_us_telefono = $us_telefono;
	}
	
	public function getus_telefono(){
		return $this->_us_telefono;
	}
	
	public function setus_sexo($us_sexo){
		$this->_us_sexo = $us_sexo;
	}
	
	public function getus_sexo(){
		return $this->_us_sexo;
	}
	
	
	public function setus_clave($us_clave){
		$this->_us_clave = $us_clave;
	}
	
	public function getus_clave(){
		return $this->_us_clave;
	}
	
	public function setus_twitter($us_twitter){
		$this->_us_twitter = $us_twitter;
	}
	
	public function getus_twitter(){
		return $this->_us_twitter;
	}
	
	public function setus_fbc_id($us_fbc_id){
		$this->_us_fbc_id = $us_fbc_id;
	}
	
	public function getus_fbc_id(){
		return $this->_us_fbc_id;
	}
	
	public function setus_apodo($us_apodo){
		$this->_us_apodo = $us_apodo;
	}
	
	public function getus_apodo(){
		return $this->_us_apodo;
	}
	
	public function setus_apellido($us_apellido){
		$this->_us_apellido = $us_apellido;
	}
	
	public function getus_apellido(){
		return $this->_us_apellido;
	}
	
	public function setus_nombre($us_nombre){
		$this->_us_nombre = $us_nombre;
	}
	
	public function getus_nombre(){
		return $this->_us_nombre;
	}
	
	public function setus_id($us_id){
		$this->_us_id = $us_id;
	}
	
	public function getus_id(){
		return $this->_us_id;
	}
	
	
 public function __construct(array $options = null)
{
        if (is_array($options)){
            $this->setOptions($options); 
        }
}


public function setOptions(array $options){

	$this->setus_apellido($options["us_apellido"]);
	$this->setus_apodo($options["us_apodo"]);
	$this->setus_clave($options["us_clave"]);
	$this->setus_fbc_id($options["us_fbc_id"]);
	$this->setus_id($options["us_id"]);
	$this->setus_nombre($options["us_nombre"]);
	$this->setus_twitter($options["us_twitter"]);
	$this->setus_sexo($options["us_sexo"]);
	$this->setus_telefono($options["us_telefono"]);
        
}
 
}