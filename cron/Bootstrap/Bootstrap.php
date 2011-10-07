<?php

/**
 *
 */
class Bootstrap extends Search_Bootstrap_Abstract {

    protected function _initTimeLimit() {
        set_time_limit(110);
    }

    protected function _initErrorReporting() {
        error_reporting(E_ALL|E_NOTICE);
        ini_set("display_errors", 1);
    }

    protected function _initMemLimit(){
        ini_set('memory_limit', '64M');
    }
    

    protected function _initStreamLogger() {
        $this->bootstrap('logger');
        assert($this->_logger);
        
        $writer = new Zend_Log_Writer_Stream('php://output');
        $this->_logger->addWriter($writer);
    }

}
