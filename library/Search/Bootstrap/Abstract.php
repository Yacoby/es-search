<?php
/* l-b
 * This file is part of ES Search.
 * 
 * Copyright (c) 2009 Jacob Essex
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */


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
        $host = $options['host'];
        $user = $options['username'];
        $pass = $options['password'];
        $dbnm = $options['dbname'];
        $c = Doctrine_Manager::connection("mysql://{$user}:{$pass}@{$host}/{$dbnm}");
        $c->setCharset('utf8');
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
        $w = new Search_Log_Writer_Doctrine('ErrorLog', $cols);

        $this->_logger->addWriter($w);

    }
}