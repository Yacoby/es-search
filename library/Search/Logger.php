<?php

/**
 * Extends the zend logging class so it can be used as a static. This must be
 * constructed  before it can be used
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
