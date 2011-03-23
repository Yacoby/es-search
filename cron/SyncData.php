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

    $factory = new Search_Parser_Factory();
    $si = new Search_Sync($factory);
    $si->syncAll();
}catch(Exception $e){
    Search_Logger::err('Unhandled Exception:' . $e->getMessage());
}