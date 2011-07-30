<?php

/**
 * This class calls every function called on the given objects
 *
 *
 * @todo nest in Search_Observable when PHP supports it
 */
class _ObservableFunctionCaller {
    protected $_observers;
    public function  __construct(array &$observers) {
        $this->_observers = &$observers;
    }
    
    public function  __call($name,  $arguments) {
        foreach ( $this->_observers as $observer ) {
            if ( method_exists($observer, $name) ){
                call_user_func_array(array($observer, $name), $arguments);
            }
        }
    }
}

/**
 * Superclass for an observable object.
 */
class Search_Observable {
    private $_observers = array();

    public function __construct(){
        $cls = get_class($this);
        if ( isset(self::$_alwaysAttachObservers[$cls]) ){
            foreach ( self::$_alwaysAttachObservers[$cls] as $o ){
                $this->attach($o);
            }
        }
    }

    static private $_alwaysAttachObservers = array();
    /**
     * This adds all the given observer to all new objects of this type
     *
     * This does NOT add it to objects that have already been created
     */
    public static function alwaysAttach(Search_Observer $observer){
        $cls = get_class($this);
        if ( !isset(self::$_alwaysAttachObservers[$cls]) ){
            self::$_alwaysAttachObservers[$cls] = array();
        }
        self::$_alwaysAttachObservers[$cls][] = $observer;
    }

    /**
     * Function to pass the called function onto all events
	 *
	 * @return _ObservableFunctionCaller
     */
    protected function event() {
        return new _ObservableFunctionCaller($this->_observers);
    }

    /**
     * Attach an observer to the observable object
     */
    public function attach(Search_Observer $observer) {
        $this->_observers[] = $observer;
    }
}
