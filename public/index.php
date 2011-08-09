<?php

//Needs to be here as it defines include directories
require "../AppLoader.php";


//@todo is this any good here. It was split from a class but does it need to go back?
require "Zend/Cache.php";

//lifetime in minutes
$lifeTimeMult = APPLICATION_ENV == 'production' ? 2 : 0;

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

if ( $application->getOption('pagecache') != 1 ){
    $cache->cancel()
}

$application->run();
