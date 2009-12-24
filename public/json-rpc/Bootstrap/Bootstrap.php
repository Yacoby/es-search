<?php /* l-b
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


class Bootstrap extends Search_Bootstrap_Abstract{

    protected function __initFirebugLogger() {
        $this->bootstrap('logger');
        assert($this->_logger);
        $writer = new Zend_Log_Writer_Firebug();
        $this->_logger->addWriter($writer);
    }


    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
                        'namespace' => 'Default',
                        'basePath'  => APPLICATION_PATH,
        ));
        return $autoloader;
    }

}