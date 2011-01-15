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
 * l-b */

/**
 * Holds an array of results. The only prefied value is error.
 */
class Search_Lucene_Result {
    private $_data = array();

    public function __construct(array $args = array()) {
        $this->_data = $args;
        $this->error = false;
    }
    public function __get($name) {
        return $this->_data[$name];
    }
    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }
    /**
     *
     * @param string $name
     * @return bool
     */
    public function hasVaraible($name) {
        return array_key_exists($name, $this->_data);
    }
}
