<?php

require realpath(dirname(__FILE__).'/../AppLoader.php');
$app = createApplication(realpath(dirname(__FILE__).'/Bootstrap/Bootstrap.php'));

//scheduled needs a far longer time limit
set_time_limit(60*10);

try{
    $uw      = new Search_UpdateWorker();
    $parserPath = APPLICATION_PATH . '/parsers/';
    $factory = new Search_Parser_Factory($parserPath . 'defaults.ini',
                                         $parserPath . 'parsers.ini');
    $ud      = new Search_Updater_Scheduled($factory);

    $uw->runUpdateTask($ud);

}catch(Search_Parser_Exception_Parse $e){
    Search_Logger::warn('Parser Error: ' . $e->getMessage());
}catch(Exception $e){
    Search_Logger::err('Unhandled Exception: ' . $e->getMessage());
}
