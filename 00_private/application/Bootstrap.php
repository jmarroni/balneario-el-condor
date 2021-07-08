<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRutaimg() {
        define('RUTA_JS', "http://www.balneario-el-condor.com.ar" );
        define('RUTA_CSS', "http://www.balneario-el-condor.com.ar" );
        define('RUTA_SITIO', "http://www.balneario-el-condor.com.ar" );
        define('RUTA_IMAGENES', "http://static.balneario-el-condor.com.ar/" );
        define('RUTA_ROOT', "C:/xampp/htdocs/local.condor/htdocs" );
        define('CLAVE_JSON','CarmenLuca19812012');
        define('MAREAS','1');
        define('NOVEDADES_ULTIMA','2');
        define('AGENDA','3');
        define('CERCANOS','4');
        define('ALQUILERES','5');
        define('COLECTIVO','6');

        
        
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
        if ('production' == APPLICATION_ENV) {
            // $zfdebug = new ZFDebug_Controller_Plugin_Debug(array(
            //             'database_adapter' => $db, // Zend_Db_Adapter_Abstract
            //             'memory_usage' => true,
            //             'collect_view_vars' => true));
            // $frontController->registerPlugin($zfdebug);

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
                    '/horario-colectivo',
                    array(
                            'controller' => 'contacto',
                            'action'     => 'colectivo'
                    )
            );
            $router->addRoute('horario-colectivo', $routes);
            $routes = new Zend_Controller_Router_Route(
            		'/recetas/:keyword',
            		array(
            				'controller' => 'recetas',
            				'action'     => 'detalle'
            		)
            );
            $router->addRoute('recetas', $routes);
            $routes = new Zend_Controller_Router_Route(
                '/club-de-amigos/:cl_id',
                array(
                    'controller' => 'clubdeamigos',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('clubdeamigos', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/club-de-amigos',
                array(
                    'controller' => 'clubdeamigos',
                    'action'     => 'index'
                )
            );
            $router->addRoute('clubdeamigosTodas', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/playa/:nw_id',
                array(
                    'controller' => 'lugares',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('PlayaDetalle', $routes);
            
            $routes = new Zend_Controller_Router_Route(
                '/restaurantes-y-confiterias/',
                array(
                    'controller' => 'restaurantes',
                    'action'     => 'index'
                )
            );
            $router->addRoute('RestaurantesYConfiterias', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/restaurantes-y-confiterias/:id',
                array(
                    'controller' => 'restaurantes',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('RestaurantesYConfiteriasDetalle', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/clasificados/detalle/:keyword',
                array(
                    'controller' => 'clasificados',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('ClasificadosDetalle', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/noticias-y-actividades/:id',
                array(
                    'controller' => 'novedades',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('noticias-y-actividades-detalle', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/noticias-y-actividades',
                array(
                    'controller' => 'novedades',
                    'action'     => 'index'
                )
            );
            $router->addRoute('noticias-y-actividades', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/alquileres-y-ventas',
                array(
                    'controller' => 'clasificados',
                    'action'     => 'index'
                )
            );
            $router->addRoute('alquileres-y-ventas', $routes);
            
            $routes = new Zend_Controller_Router_Route(
            		'/alquiler',
            		array(
            				'controller' => 'alquiler',
            				'action'     => 'index'
            		)
            		);
            $router->addRoute('alquiler', $routes);
            
            $routes = new Zend_Controller_Router_Route(
            		'/alquileres-y-ventas/:seguido',
            		array(
            				'controller' => 'clasificados',
            				'action'     => 'redireccion'
            		)
            );
            $router->addRoute('alquileres-y-ventas2', $routes);
            
            $routes = new Zend_Controller_Router_Route(
                '/hoteles-camping-cabanias/:id',
                array(
                    'controller' => 'hospedaje',
                    'action'     => 'detalle'
                )
            );
            $router->addRoute('hoteles-camping-cabanias-detalle', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/hoteles-camping-cabanias',
                array(
                    'controller' => 'hospedaje',
                    'action'     => 'index'
                )
            );
            $router->addRoute('hoteles-camping-cabanias', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/boliches-pub/:id',
                array(
                    'controller' => 'nocturnos',
                    'action'     => 'detalle'
                )
            );

            $router->addRoute('boliches-pub-detalle', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/alquileres-ofrecidos-pedidos-temporada/:tipo',
                array(
                    'controller' => 'clasificados',
                    'action'     => 'index'
                )
            );

            $router->addRoute('alquileres-detallado', $routes);
            $routes = new Zend_Controller_Router_Route(
                '/boliches-pub/',
                array(
                    'controller' => 'nocturnos',
                    'action'     => 'index'
                )
            );
            $router->addRoute('boliches-pub', $routes);

            

            $routes = new Zend_Controller_Router_Route(
                '/tablas-mareas-pleamar',
                array(
                    'controller' => 'mareas',
                    'action'     => 'index'
                )
            );
            $router->addRoute('tablas-mareas-pleamar', $routes);
            
            $routes = new Zend_Controller_Router_Route(
                '/informacion-util',
                array(
                    'controller' => 'informacion',
                    'action'     => 'index'
                )
            );
            
            $router->addRoute('informacion-util', $routes);


            $routes = new Zend_Controller_Router_Route(
                '/tablas-mareas-pleamar/playa/:playa',
                array(
                    'controller' => 'mareas',
                    'action'     => 'index'
                )
            );
            $router->addRoute('tablas-mareas-pleamar-playa', $routes);

            $routes = new Zend_Controller_Router_Route(
                '/imagen/autor/:autor',
                array(
                    'controller' => 'imagen',
                    'action'     => 'index'
                )
            );
            $router->addRoute('ImagenesAutores', $routes);
            
            
            $routes = new Zend_Controller_Router_Route(
            		'/5ta-fiesta-nacional-del-tejo',
            		array(
            				'controller' => 'landing',
            				'action'     => 'tejo'
            		)
            );
            $router->addRoute('5tatejo', $routes);

            $routes = new Zend_Controller_Router_Route(
            		'/5ta-fiesta-nacional-del-tejo-el-condor',
            		array(
            				'controller' => 'landing',
            				'action'     => 'fiestatejo'
            		)
            );
            $router->addRoute('fiestaTejo', $routes);

            $routes = new Zend_Controller_Router_Route(
            		'/arenas-del-sur-cenashow',
            		array(
            				'controller' => 'landing',
            				'action'     => 'arenasdelsur'
            		)
            );
            $router->addRoute('arenasdelsurcenashow', $routes);

            $routes = new Zend_Controller_Router_Route(
            		'/dia-del-nino',
            		array(
            				'controller' => 'landing',
            				'action'     => 'fiestadianino'
            		)
            );
	    $router->addRoute('dia-del-nino', $routes);

        $routes = new Zend_Controller_Router_Route(
                    '/alquileres-clasificados/',
                    array(
                            'controller' => 'landing',
                            'action'     => 'clasificados'
                        )
            );
        $router->addRoute('clasificados-usuarios-2015', $routes);

        $routes = new Zend_Controller_Router_Route(
                    '/noche-de-bares/',
                    array(
                            'controller' => 'agenda',
                            'action'     => 'nochebares'
                        )
            );
        $router->addRoute('noche-de-bares-2016', $routes);

        }


}

