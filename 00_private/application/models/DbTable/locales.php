<?php
class Application_Model_DbTable_locales extends Zend_Db_Table_Abstract
{
 protected $_lo_id;
 protected $_lo_nombre;
 protected $_lo_latitud;
 protected $_lo_longitud;
 protected $_lo_es_principal;
 protected $_lo_razon_social;
 protected $_lo_calle;
 protected $_lo_altura;
 protected $_lo_piso;
 protected $_lo_oficina;
 protected $_lo_depto;
 protected $_lo_codigo_postal;
 protected $_lo_mail;
 protected $_lo_telefono1;
 protected $_lo_telefono2;
 protected $_lo_telefono3;
 protected $_em_id;
 protected $_ci_id;
 protected $_lo_distancia;
 protected $_unidad_distancia;
 protected $_lo_web;
 
 public function setlo_calle($lo_calle){
 	$this->_lo_calle = (isset($lo_calle))?$lo_calle:"";
 }
 
 public function getlo_calle(){
 	return $this->_lo_calle;
 }
 public function setlo_web($web){
 	$this->_lo_web = "";
 }
 
 public function getlo_web(){
 	return $this->_lo_web;
 }

 protected $_lo_impresiones;

public function setlo_razon_social($lo_razon_social){
	$this->_lo_razon_social = (isset($lo_razon_social)?$lo_razon_social:"");
}

public function getlo_razon_social(){
	return $this->_lo_razon_social;
}

 public function setlo_codigo_postal($lo_codigo_postal){
 	$this->_lo_codigo_postal = (isset($lo_codigo_postal)?$lo_codigo_postal:"");
 }
 
 public function getlo_codigo_postal(){
 	return $this->_lo_codigo_postal;
 }
 
 public function setlo_mail($lo_mail){
 	$this->_lo_mail = (isset($lo_mail)?$lo_mail:"");
 }
 
 public function getlo_mail(){
 	return $this->_lo_mail;
 }
 
 public function setci_id($ci_id){
 	$this->_ci_id = (isset($ci_id)?$ci_id:"");
 }
 
 public function getci_id(){
 	return $this->_ci_id;
 }
 
 public function setlo_telefono1($lo_telefono1){
 	$this->_lo_telefono1 = (isset($lo_telefono1)?$lo_telefono1:"");
 }
 
 public function getlo_telefono1(){
 	return $this->_lo_telefono1;
 }
 
 public function __construct(array $options = null)
{
        if (is_array($options)){
            $this->setOptions($options); 
        }
}


public function setOptions(array $options){

        //$this->setunidad_distancia($options["unidad_distancia"]);
        $this->setlo_distancia($options["distancia"]);
        $this->setci_id($options["ci_id"]);
        $this->setem_id($options["em_id"]);
        $this->setlo_telefono3($options["lo_telefono_3"]);
        $this->setlo_telefono2($options["lo_telefono_2"]);
        $this->setlo_telefono1($options["lo_telefono_1"]);
        $this->setlo_mail($options["lo_mail"]);
        $this->setlo_codigo_postal($options["lo_codigo_postal"]);
        $this->setlo_depto($options["lo_depto"]);
        $this->setlo_oficina($options["lo_oficina"]);
        $this->setlo_piso($options["lo_piso"]);
        $this->setlo_altura($options["lo_altura"]);
        $this->setlo_razon_social($options["lo_razon_social"]);
        $this->setlo_calle($options["lo_calle"]);
        $this->setlo_es_principal($options["lo_es_principal"]);
        $this->setlo_longitud($options["lo_longitud"]);
        $this->setlo_latitud($options["lo_latitud"]);
        $this->setlo_nombre($options["lo_nombre"]);
        $this->setlo_id($options["lo_id"]);
        $this->setlo_web("");
        
}
 
 public function setunidad_distancia($unidad_distancia){
 	$this->_unidad_distancia = (isset($unidad_distancia)?$unidad_distancia:"");
 }
 
 public function getunidad_distancia(){
 	return $this->_unidad_distancia;
 }   

 
 public function setlo_distancia($lo_distancia){
 	$lo_distancias = (intval($lo_distancia) == 0)?round($lo_distancia*1000):round($lo_distancia,2);
 	$this->_lo_distancia = $lo_distancias;
 	$this->setunidad_distancia((intval($lo_distancia) == 0)?"mts.":"km.");
 }
 
 public function getlo_distancia(){
 	return $this->_lo_distancia;
 }
 
 public function setem_id($em_id){
 	$this->_em_id = (isset($em_id)?$em_id:"");
 }
 
 public function getem_id(){
 	return $this->_em_id;
 }
 
 public function setlo_telefono3($lo_telefono3){
 	$this->_lo_telefono3 = (isset($lo_telefono3)?$lo_telefono3:"");
 }
 
 public function getlo_telefono3(){
 	return $this->_lo_telefono3;
 }
 
 public function setlo_telefono2($lo_telefono2){
 	$this->_lo_telefono2 = (isset($lo_telefono2)?$lo_telefono2:"");
 }
 
 public function getlo_telefono2(){
 	return $this->_lo_telefono2;
 }

 public function setlo_depto($lo_depto){
 	$this->_lo_depto = (isset($lo_depto)?$lo_depto:"");
 }
 
 public function getlo_depto(){
 	return $this->_lo_depto;
 }
 
 public function setlo_oficina($lo_oficina){
 	$this->_lo_oficina = (isset($lo_oficina)?$lo_oficina:"");
 }
 
 public function getlo_oficina(){
 	return $this->_lo_oficina;
 }
 
 public function setlo_piso($lo_piso){
 	$this->_lo_piso = (isset($lo_piso)?$lo_piso:"");
 }
 
 public function getlo_piso(){
 	return $this->_lo_piso;
 }
 
 public function setlo_altura($lo_altura){
 	$this->_lo_altura = (isset($lo_altura)?$lo_altura:"");
 }
 
 public function getlo_altura(){
 	return $this->_lo_altura;
 }

 
 public function setlo_es_principal($lo_es_principal){
 	$this->_lo_es_principal = (isset($lo_es_principal)?$lo_es_principal:"");
 }
 
 public function getlo_es_principal(){
 	return $this->_lo_es_principal;
 }
 
 public function setlo_longitud($lo_longitud){
 	$this->_lo_longitud = (isset($lo_longitud)?$lo_longitud:"");
 }
 
 public function getlo_longitud(){
 	return $this->_lo_longitud;
 }
 
 public function setlo_latitud($lo_latitud){
 	$this->_lo_latitud = (isset($lo_latitud)?$lo_latitud:"");
 }
 
 public function getlo_latitud(){
 	return $this->_lo_latitud;
 }
 
 public function setlo_nombre($lo_nombre){
 	$this->_lo_nombre = (isset($lo_nombre)?$lo_nombre:"");
 }
 
 public function getlo_nombre(){
 	return $this->_lo_nombre;
 }
 
 public function setlo_id($lo_id){
 	$this->_lo_id = (isset($lo_id)?$lo_id:"");
 }
 
 public function getlo_id(){
 	return $this->_lo_id;
 }
}