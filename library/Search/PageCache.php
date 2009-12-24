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

/*
 * This require is required because it may be used before the application has been
 * bootstrapped.
 */
require "Zend/Cache.php";

/**
 * This is a cache for pages. It is heled at the application level, but shouldn't
 * cache on errors
 *
 * @todo Really needs reducing into something more refined.
 *      Removing singleton for example
 *      Fixing __call
 *      Remvoing alltogether
 */
class Search_PageCache {
    /**
     * @var Zend_Cache_Frontend_Page
     */
    private $_cache;

    private function __construct() {
        $lifeTimeMult = APPLICATION_ENV == 'production' ? 1 : 0;
        $frontendOptions = array(
                'lifetime' => 3600*$lifeTimeMult,
                'automatic_cleaning_factor' => 20,
                'regexps' => array('^/search\\?' => array('cache' => false),),
        );

        $backendOptions = array(
                'cache_dir' => '../cache/page/' // Directory where to put the cache files
        );


        // getting a Zend_Cache_Core object
        $this->_cache = Zend_Cache::factory('Page',
                'File',
                $frontendOptions,
                $backendOptions);
    }

    public function __call($name, $args) {
        if ( count($args) ) {
            throw new Exception('Doesn\'t work with args');
        }
        return $this->_cache->{$name}();
    }

    static public function getInstance() {
        static $instance = null;
        if ( !$instance ) {
            $instance = new self();
        }
        return $instance;
    }
}
