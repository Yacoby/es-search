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
 * Class holds a segment of results. Returned from functions in UnifiedModDatabase
 */
class Search_Data_SearchResults {
    private $_results, $_count;

    /**
     *
     * @param array $results An array of Search_Data_Result objects
     * @param int $count
     */
    public function __construct(array $results, $count) {
        $this->_results = $results;
        $this->_count = $count;
    }

    /**
     * This function gets the TOTAL number of results. This is not the same as
     * count(results), as the SearchResults only contains a secgment of the total
     * results
     *
     * @return int
     */
    public function count() {
        return $this->_count;
    }

    /**
     * Gets the results
     *
     * @return array
     */
    public function results() {
        return $this->_results;
    }

    /**
     * Gets a result at a given index
     *
     * @param int $index
     * @return Search_Data_Result
     */
    public function getResult($index) {
        return $this->_results[$index];
    }

    /**
     * Sets a result at a given index
     *
     * @param int $index
     * @param Search_Data_Result $result
     */
    public function setResult($index, Search_Data_Result $result) {
        $this->_results[$index] = $result;
    }

}
