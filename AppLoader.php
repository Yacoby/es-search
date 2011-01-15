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

/** ***************************************************************************
 * This should setup the application to the point where it can load its
 * bootstrap file (or do whatever is required).
 *
 * This should contain setup that should be global. It shouldn't
 * really include constants, as they should be in the application.ini
 * 
 */

//Set the library directory
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/library'),
        get_include_path(),
)));

//Zend, doctrine etc directory
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/../php'),
        get_include_path(),
)));


//Setup app wide constants
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', ROOT_PATH . '/application');
define('CONFIG_PATH', ROOT_PATH.'/config');
if ( !defined('APPLICATION_ENV') ){
    define('APPLICATION_ENV', 'development');
}
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
//work around for a issue with Doctrine:
//http://www.doctrine-project.org/jira/browse/DC-288
$loader->registerNamespace('sfYaml')
       ->pushAutoloader(array('Doctrine', 'autoload'), 'sfYaml');

//required to use the doctrine loader
$loader->setFallbackAutoloader(true);

//useful debug into. TODO make dependant on APP ENV
$loader->suppressNotFoundWarnings(false);

$manager = Doctrine_Manager::getInstance();

//Doctrine Configuration. This should probably. Posibly. Maybe. Be moved
//to the config file. Ug
$base = dirname(__FILE__).'/library/Search/';
Zend_Registry::set('doctrine_config', array(
        'data_fixtures_path'  =>  $base.'Doctrine/Data/Fixtures',
        'models_path'         =>  $base.'Model',
        'migrations_path'     =>  $base.'Doctrine/Migrations',
        'sql_path'            =>  $base.'Doctrine/Data/Sql',
        'yaml_schema_path'    =>  $base.'Doctrine/Schema',
        /*'generate_models_options' => array(
          'classPrefix'=>'Search_Model_',
          'classPrefixFiles'=>false,
          'baseClassPrefix' =>  'Generated_',
          'baseClassDirectory' => 'Generated',
          'baseClassPrefixFiles' => false
    ),*/

));

Doctrine::loadModels(array($base.'Model'));

/*
 * @param $path the path to Bootstrap.php, if not set it will default to the
 *             application bootstrap
 * @return Zend_Application
 *
 * Allows general construction of a application
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