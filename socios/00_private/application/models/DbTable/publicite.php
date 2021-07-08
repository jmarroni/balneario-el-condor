<?php

class Application_Model_DbTable_publicite extends Zend_Db_Table_Abstract
{
	protected $_pu_id;
    protected $_pu_nombre;
    protected $_pu_comentario;
    protected $_pu_apellido;
    protected $_pu_email;
    protected $_pu_zona;
    
    public function setPu_email($pu_email){
    	$this->_pu_email = (isset($pu_email))?$pu_email:"";
    }
    
    public function getPu_email(){
    	return $this->_pu_email;
    }
     
    

    public function __construct(array $options = null)
    {
        if (is_array($options)){
            $this->setOptions($options); 
        }
    }


    public function setOptions(array $options){

        $this->setPu_id($options["pu_id"]);
        $this->setPu_apellido($options["pu_apellido"]);
        $this->setPu_zona($options["pu_zona"]);
        $this->setPu_nombre($options["pu_nombre"]);
        $this->setPu_comentario($options["pu_comentario"]);
        $this->setPu_email($options["pu_email"]);
        
    }

    // Setter
    public function setPu_id($pu_id){
        $this->_pu_id = (isset($pu_id))?$pu_id:"";
    }

    public function setPu_apellido($pu_apellido){
        $this->_pu_apellido = (isset($pu_apellido))?$pu_apellido:"";
    }

    public function setPu_zona($pu_zona){
        $this->_pu_zona = (isset($pu_zona))?$pu_zona:"";
    }

    public function setPu_nombre($pu_nombre){
        $this->_pu_nombre = (isset($pu_nombre))?$pu_nombre:"";
    }

    public function setPu_comentario($pu_comentario){
        $this->_pu_comentario = (isset($pu_comentario))?$pu_comentario:"";
    }

    

    // Getter
    public function getPu_id(){
        return $this->_pu_id;
    }

    public function getPu_zona(){
        return $this->_pu_zona;
    }
    public function getPu_apellido(){
        return $this->_pu_apellido;
    }

    public function getPu_nombre(){
        return $this->_pu_nombre;
    }

    public function getPu_comentario(){
        return $this->_pu_comentario;
    }
 
}