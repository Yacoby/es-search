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