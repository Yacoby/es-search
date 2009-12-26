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


//Needs to be here as it defines include directories
require "../AppLoader.php";


//@todo is this any good here. It was split from a class but does it need to go back?
require "Zend/Cache.php";

$lifeTimeMult = APPLICATION_ENV == 'production' ? 60 : 0;

$frontendOptions = array(
        'lifetime' => 5*$lifeTimeMult,
        'automatic_cleaning_factor' => 100,
        //@todo change regex to store the first page of results.
        'regexps' => array(
                '^/search\\?' => array('cache' => false)
        )
);

$cache = Zend_Cache::factory(
        'Page',
        'File',
        $frontendOptions,
        array('cache_dir' => '../cache/page/')
);
$cache->start();


$application = createApplication();
$application->run();
