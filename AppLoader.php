<?php

/**
 * This should setup the application to the point where it can load its
 * bootstrap file (or do whatever is required).
 *
 * This should contain setup that should be global. It shouldn't
 * really include constants, as they should be in the application.ini
 * 
 */

date_default_timezone_set('Europe/London');

//Set the library directory
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/library'),
        get_include_path(),
)));


//Setup app wide constants
if ( getenv('TESSEARCH_ENV') !== False ){
    define('APPLICATION_ENV', getenv('TESSEARCH_ENV'))
}elseif ( !defined('APPLICATION_ENV') ){
    define('APPLICATION_ENV', 'development');
}
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', ROOT_PATH . '/application');
define('CONFIG_PATH', ROOT_PATH.'/config');

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


//Doctrine Configuration. This should probably. Posibly. Maybe. Be moved
//to the config file. Ug
$base = dirname(__FILE__).'/library/Search/';
Zend_Registry::set('doctrine_config', array(
        'data_fixtures_path'  =>  $base.'Doctrine/Data/Fixtures',
        'models_path'         =>  $base.'Model',
        'migrations_path'     =>  $base.'Doctrine/Migrations',
        'sql_path'            =>  $base.'Doctrine/Data/Sql',
        'yaml_schema_path'    =>  $base.'Doctrine/Schema',
));

$oldPath = get_include_path();
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/library/Search/Model/generated'),
        get_include_path(),
)));
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(dirname(__FILE__) . '/library/Search/Model'),
        get_include_path(),
)));
Doctrine::loadModels(array($base.'Model'));
set_include_path($oldPath);

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
