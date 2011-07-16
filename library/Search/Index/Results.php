<?php

/**
 * Class holds a segment of results. Returned from functions in UnifiedModDatabase
 */
class Search_Index_Results {
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
