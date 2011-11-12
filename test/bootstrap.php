<?php

error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", 1);

define('APPLICATION_ENV', 'testing');
//used to define a db resource
require "Funcs.php";
require "PageHelper.php";
date_default_timezone_set("GMT");


/* -----------------------------------------------------------
 *  Create resources
 *----------------------------------------------------------- */

$application = createApplication();

$cfg = Zend_Registry::get('doctrine_config');
$connection = Doctrine_Manager::getInstance()->getCurrentConnection();

try {
    $connection->dropDatabase();
}catch(Exception $e) {
    //meh. Ignore. We are possibly fine with the database not existing.
}
try {
    $connection->createDatabase();
    Doctrine_Core::createTablesFromModels($cfg['models_path']);
} catch (Exception $e) {
    echo $e->getMessage();
}

ini_set('memory_limit','256M');
