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
            call_user_func_array(array($observer, $name), $arguments);
        }
    }
}

/**
 * Superclass for an observable object.
 */
class Search_Observable {
    private $_observers = array();

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