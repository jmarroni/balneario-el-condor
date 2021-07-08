<?php

class Application_Model_DbTable_clasificadomail extends Zend_Db_Table_Abstract
{
	/**
	 * Variable que contiene el nombre de la tabla.
	 * @var string
	 */
	protected $_name = 'clasificados_mail';

	public function getClasMail($identificador){
		if ($identificador){
			$select = $this->select();
			$select->setIntegrityCheck(false);
			$select->where('cla_keyword = ?',$identificador);
			return $this->_fetch($select);
		}else{
			return array();
		}
	}
	public function getMailDest($identificador){
		if ($identificador){
			$select = $this->select();
    		$select->setIntegrityCheck(false);
    		$select->from(array("cla" => "clasificados"));
    		$select->where("cla.cla_id = ?",$identificador);
    		$select->group("cla_mail_contacto");
    		return $this->_fetch($select);
			
		}
	}
	
	public function insertClaMail($id, $mailDest, $direccionip, $nomape, $correo, $telefono, $fechahora, $comentario){
		try{
		if($id != null){
		$id = $this->insert(array(
				"cla_id" => $id,
				"cla_ip_envio" => $direccionip,
				"cla_correo_contacto" => $correo,
				"cla_nom_ape" => $nomape,
				"cla_comentario" => $comentario,
				"cla_fecha_envio" => $fechahora,
				"cla_telefono_contacto" => $telefono,
				"cla_mail_dest" => $mailDest));
		return $id;
	}
		}catch(Zend_Db_Adapter_Exception $e){
			return "error";
		}

}}?>