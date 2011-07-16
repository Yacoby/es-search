<?php

class Bootstrap extends Search_Bootstrap_Abstract {

    protected function _initRoutes() {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addConfig(
                new Zend_Config($this->getOptions()),
                'routes'
        );
    }

    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
                        'namespace' => 'Default',
                        'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }

    /**
     * Bootstrap the view doctype
     * @return void
     */
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');

        $view->addHelperPath('Helper/View', 'Helper_View');
    }

    protected function _initRegenSession(){
        Zend_Session::regenerateId();
    }
}
