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

//lifetime in minutes
$lifeTimeMult = APPLICATION_ENV == 'production' ? 5 : 0;

$frontendOptions = array(
        'lifetime'                          => 60*$lifeTimeMult,
        'automatic_cleaning_factor'         => 100,
        'cache_with_get_variables'          => true,
        'cache_with_cookie_variables'       => true,
        'cache_with_session_variables'      => true,
        'make_id_with_session_variables'    => false,

        //I suspect there is a bug somewhere for this to be required
        'default_options' => array(
                'lifetime'                          => 60*$lifeTimeMult,
                'automatic_cleaning_factor'         => 100,
                'cache_with_get_variables'          => true,
                'cache_with_cookie_variables'       => true,
                'cache_with_session_variables'      => true,
                'make_id_with_session_variables'    => false
        ),

        'regexps' => array(
                '/search\?'            => array('cache' => false),
                '/search\?.*page=1'    => array('cache' => true),
        )

);

$cache = Zend_Cache::factory(
        'Page',
        'File',
        $frontendOptions,
        array('cache_dir' => ROOT_PATH.'/cache/page')
);
$cache->start();

$application = createApplication();

$application->run();
