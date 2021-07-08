<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        
    }

    public function indexAction()
    {

    	if (Zend_Auth::getInstance()->hasIdentity()) {
         $this->_redirect('/novedades/listado');
         exit();}


    }
    
	public function loginAction()
   {

	     $usuario = $this->_getParam('usuario');
	     $clave = $this->_getParam('contrasena');
	
	     if ($usuario != "" && $clave != "") {
				Zend_Session::setOptions(array(
			    'cookie_lifetime' => 36000,
			    'gc_maxlifetime'  => 36000));
	         $adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
	         $adapter->setTableName('usuarios')->setIdentityColumn('us_nick')->setCredentialColumn('us_clave');
	
	         $adapter->setIdentity($usuario);
	         //$clave = traigo la clave ingresada en el form
	       
	         $adapter->setCredential(sha1($clave));
	         //accedo a Zend_Auth
	         $auth = Zend_Auth::getInstance();
	         
	         
	
	         //autentico contra la tabla usuarios de la base de datos
	         $resultado = $auth->authenticate($adapter);
	         if ($resultado->isValid()) {
	         	
	             $user = $adapter->getResultRowObject();
	             $auth->getStorage()->write($user);
				 echo "OK";
	         } else {
	             echo "20";
	         }
	     } else {
	         echo "10";
	     }
	     exit();
	}

	public function cerrarsesionAction(){
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index'); // back to login page
	}

}
