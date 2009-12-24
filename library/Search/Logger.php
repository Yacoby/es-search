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
 * l-b */ ?>

<?php

/**
 * Extends the zend logging class so it can be used as a static. This must be
 * constructed  before it can be used
 *
 * @todo when PHP 5.3 hits extend with __callStatic
 *
 */
class Search_Logger extends Zend_Log {

    /**
     *
     * @var Zend_Log
     */
    protected static $_logger = null;

    /**
     * This must be called, don't just start calling the static functions!
     *
     * This is where the object instance is set
     */
    public function  __construct() {
        assert(!self::$_logger);

        parent::__construct();
        self::$_logger = $this;
    }

    /**
     *
     * @return Zend_Log
     */
    static function getInstance(){
        assert(self::$_logger);
        return self::$_logger;
    }
    
    static function _log($msg, $lvl) {
        assert(self::$_logger);
        self::$_logger->log($msg, $lvl);
    }

    static function info($msg) {
        self::_log($msg, Zend_Log::INFO);
    }

    static function warn($msg) {
        self::_log($msg, Zend_Log::WARN);
    }

    static function err($msg) {
        self::_log($msg, Zend_Log::ERR);
    }

    static function emerg($msg) {
        self::_log($msg, Zend_Log::EMERG);
    }

    static function crit($msg) {
        self::_log($msg, Zend_Log::CRIT);
    }
    
    static function debug($msg) {
        self::_log($msg, Zend_Log::DEBUG);
    }}
