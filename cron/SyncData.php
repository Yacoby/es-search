<?php
/**
 * Updates everything. This should be run every one in a while to ensure that
 * the files and the database are synced
 */

require '../AppLoader.php';


createApplication(realpath(dirname(__FILE__).'/Bootstrap/Bootstrap.php'));
try{
    if (@preg_match('/\pL/u', 'a') != 1) {
        throw new Exception('PCRE UTF-8 is not enabled. Please enable it');
    }
    if( !function_exists('mb_strtolower') ){
        throw new Exception('The module mbstring is not installed!');
    }

    $factory = new Search_Parser_Factory(APPLICATION_PATH . '/parsers/defaults.ini',
                                         APPLICATION_PATH . '/parsers/parsers.ini');
    $si = new Search_Sync_Site($factory);
    $si->syncAll();
}catch(Exception $e){
    Search_Logger::err('Unhandled Exception:' . $e->getMessage());
}