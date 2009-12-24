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
 * Class containg the functions for the class that stores the bulk of the data
 */
abstract class Search_Data_StoreDatabase extends Search_Data_ModDatabase{

     /**
     * Takes the result from a search, and gets all the details from another database.
     *
     * There is no requirment for any Database to implement this
     *
     * Optionally this function can flag a result as having an error (in other words
     * being inconstant accross databases). This causes the mod to be removed form
     * all databases
     *
     * @param SearchResult $sr
     * @return SearchResult
     */
    public function getResultDetails(SearchResult $sr){
        self::notImplemented(__FUNCTION__);
    }

}
