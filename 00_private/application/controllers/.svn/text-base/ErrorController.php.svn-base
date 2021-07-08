<?php


class ErrorController extends Zend_Controller_Action
{


    public function errorAction()
{
        $errors = $this->_getParam('error_handler');
   $exception = $errors->exception;
        mail("jmarroni@gmail.com", "Error en turismo", $exception->getMessage(). "\n" . $exception->getTraceAsString()."\n URL --->http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        echo "error";
        exit();
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
 
                // ... get some output to display...
                break;
            default:
                // application error; display error page, but don't change
                // status code
 
                // ...
 
                // Log the exception:
                $exception = $errors->exception;
                /*
                $log = new Zend_Log(
                    new Zend_Log_Writer_Stream(
                        '/tmp/applicationException.log'
                    )
                );*/
                echo '<pre>';
                echo $exception->getMessage(). "\n" . $exception->getTraceAsString() ; 
                /*$log->debug($exception->getMessage() . "\n" .
                            $exception->getTraceAsString());*/
                break;
        }
}
    

  


}


