<?php

class ErrorController extends Zend_Controller_Action{

    public function errorAction(){
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'The monkeys have gone on strike and refuse to get
                                        the requested page! They claim it cannot be
                                        found and demand better working conditions
                                        with less error pages.';
                $this->view->title = 'Page not found';
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Fatal error - Out of Bananas';
                $this->view->title = 'Banana Error';
                break;
        }
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }
}
