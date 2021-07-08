<?php

class Application_Model_DbTable_landing extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'primavera';


	public function insertPrimavera($param){
		try{
			$this->insert($param);
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}

	public function participar($mail,$recibir){
		
		if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		$_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
			$ip = $_SERVER['REMOTE_ADDR'];
		
	try{
			$this->insert(array("pri_nombre" => "Sandro","pri_apellido" => "Fogel","pri_mail" => "$mail","pri_telefono" => "02920-153	5353","pri_comentario" => date("Y-m-d h:m:i"),"pri_ip" => $ip,"pri_entradas" => $recibir));
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}
	}
	
}

