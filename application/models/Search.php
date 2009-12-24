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


class Default_Model_Search {
    /**
     *
     * @var ModData
     */
    private $_md;

    /**
     * @var SearchResults
     */
    private $_result;
    public function __construct(array $vals, $lowerBound, $count) {
        $this->_md = new Search_Data_UnifiedModDatabase(
                new Search_Data_DB_MySQL(),
                new Search_Data_DB_Lucene()
        );

        $this->_result = $this->search($vals, $lowerBound, $count);
        assert($this->_result instanceof Search_Data_SearchResults);
    }

    /**
     *
     * @return SearchResults
     */
    public function getResults() {
        return $this->_result;
    }

    private function search($vals, $lowerBound, $count) {
        if ( array_key_exists('general', $vals) ) {
            return $this->genSearch(
                    $vals['game'],
                    $vals['general'],
                    $lowerBound,
                    $count
            );
        }

        return $this->advancedSearch(
                $vals['game'],
                $vals['name'],
                $vals['author'],
                $vals['description'],
                $lowerBound,
                $count
        );
    }

    private function genSearch($game, $gen, $lb, $c) {
        return $this->_md->search($game, $gen, $lb, $c);
    }

    private function advancedSearch($game, $name, $author, $descript, $lb, $c) {
        return $this->_md->searchAdvanced($game, $name, $author, $descript, $lb, $c);
    }

}

?>
