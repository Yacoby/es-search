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
 * l-b */ ?>

<?php
/**
 * This should setup the application to the point where it can load its
 * bootstrap file. This is bascially a few constants
 *
 * Most of the configueration occures in the bootsrap classes
 */


//Set the library directory
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/library'),
        get_include_path(),
)));

//Zend directory
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/../php'),
        get_include_path(),
)));

//Setup app wide constants
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
define('CONFIG_PATH', realpath(dirname(__FILE__) . '/config'));
define('APPLICATION_ENV', 'production');
//define('APPLICATION_ENV', 'development');


/*
  * @param $path the path to Bootstrap.php, if not set it will default to the
  *             application bootstrap
  * @return Zend_Application
*/
function createApplication($path = null) {
    require_once 'Zend/Application.php';

    $application = new Zend_Application(
            APPLICATION_ENV,
            CONFIG_PATH . '/application.ini'
    );

    if ( $path ) {
        $application->setBootstrap($path, 'Bootstrap')
                ->bootstrap();
    }else{
        $application->setBootstrap(APPLICATION_PATH."/Bootstrap.php")
                ->bootstrap();
    }

    return $application;
}