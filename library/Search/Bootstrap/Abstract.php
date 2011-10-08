<?php

/**
 * Contains startup code relevant to all application types (json-rpc server,
 * application and cronjobs)
 */
class Search_Bootstrap_Abstract extends Zend_Application_Bootstrap_Bootstrap {
    protected function _initMemLimit(){
        ini_set('memory_limit', '32M');
    }

    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    
    protected function _initLocale() {
        setlocale(LC_ALL, 'en_US.utf-8');
    }

    public function _initDb(){
        assert ($this->hasOption('doctrine'));
        $options = $this->getOption('doctrine');
        $dbtype = $options['type'];
        $host   = $options['host'];
        $user   = $options['username'];
        $pass   = $options['password'];
        $dbnm   = $options['dbname'];

        $conn = null;
        if ( $dbtype == 'sqlite' ){
            $path = $options['path'];
            $conn = Doctrine_Manager::connection("{$dbtype}://{$path}");
        }else{
            $conn = Doctrine_Manager::connection("{$dbtype}://{$user}:{$pass}@{$host}/{$dbnm}");
        }
        $conn->setCharset('utf8');
    }

    public function _initValidation(){
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
    }

    protected function _initLogger() {
        $this->_logger = new Search_Logger();

        $cols = array(
            'message'    => 'message',
            'level'      => 'priority',
            'level_name' => 'priorityName',
            'timestamp'  => 'timestamp',
        );
        $w = new Search_Log_Writer_Doctrine('Log', $cols);

        $this->_logger->addWriter($w);

    }
}
