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
 * Class containg the functions that must exist in the inherited classes
 */
abstract class Search_Data_SearchDatabase extends Search_Data_ModDatabase {

    /**
     * Searches for mods that match the arguments
     *
     * @param string $game Must be OB or MW
     * @param string $name
     * @param string $author
     * @param string $description
     * @param string $lb The lowerbound of the results to return
     * @param string $count The number of results to return (at max)
     */
    public function searchAdvanced($game, $name, $author, $description, $lb, $count) {
        self::notImplemented(__FUNCTION__);
    }

    /**
     * A more general search that searches all fields

     * @param string $game Must be OB or MW
     * @param string $term The search string
     * @param string $lb The lowerbound of the results to return
     * @param string $count The number of results to return (at max)
     */
    public function search($game, $term, $lb, $c) {
        self::notImplemented(__FUNCTION__);
    }
}
