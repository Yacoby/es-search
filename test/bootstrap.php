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

 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 ini_set('memory_limit','128M');


set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
)));

set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/../../php'),
        get_include_path(),
)));

//used to define a db resource
//require "DatabaseResource.php";
require "Funcs.php";
require "PageHelper.php";
require "Search/URL.php";

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'testing');


require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
spl_autoload_unregister(array($autoloader, 'autoload'));

Zend_Loader_Autoloader::resetInstance();
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('PHPUnit_');
$autoloader->registerNamespace('Search_');

/* -----------------------------------------------------------
 *  Create resources
 *----------------------------------------------------------- */

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/../config/application.ini'
);
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');

$dbAdapter = $bootstrap->getResource('db');


//store the db resource
//new DatabaseResource($dbAdapter);
Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

/* -----------------------------------------------------------
 *  Reset all state
 *----------------------------------------------------------- */
resetLucene();
resetDatabse();


?>