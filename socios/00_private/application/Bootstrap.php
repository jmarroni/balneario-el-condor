<?php 

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRutaimg() {
        define('RUTA', "http://local.neoworkshop.com/");
        define('SEMILLA', "$%^&$#C4RM3NT34M0");
        define('RUTA_FIJA',dirname(dirname(dirname(__FILE__))));
        define('RUTA_IMG',"http://static.balneario-el-condor.com.ar/");
        
    }

	protected function _initZFDebug() {
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->throwExceptions(true);

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');
        if ($this->hasPluginResource('db')) {
            $this->bootstrap('db');
            $db = $this->getPluginResource('db')->getDbAdapter();
            $options['plugins']['Database']['adapter'] = $db;
        }
        if ('development' == APPLICATION_ENV) {
            $zfdebug = new ZFDebug_Controller_Plugin_Debug(array(
                        'database_adapter' => $db, // Zend_Db_Adapter_Abstract
                        'memory_usage' => true,
                        'collect_view_vars' => true));
            $frontController->registerPlugin($zfdebug);

            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
         }
    }

     protected function _initRouters() 
        {
            $router = Zend_Controller_Front::getInstance()->getRouter(); 
            //ROUTEOS
            $routes = new Zend_Controller_Router_Route(
            		'/login/administrador',
            		array(
            				'controller' => 'index',
            				'action'     => 'login'
            		)
            );
            $router->addRoute('recetas', $routes);
           

        }


}

