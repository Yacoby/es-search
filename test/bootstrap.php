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

error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", 1);
ini_set('memory_limit','128M');

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

ini_set('memory_limit','128M');
