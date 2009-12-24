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
 * Manages a search and a storage database, removing the complexity of having
 * to deal with multiple storage databases
 */
class Search_Data_UnifiedModDatabase {

    /**
     *
     * @var SearchDatabase
     */
    private $_search = null;

    /**
     *
     * @var StoreDatabase
     */
    private $_store = null;

    /**
     * Adds a new mod database to the list of mod databases
     */
    public function __construct(
            Search_Data_StoreDatabase $store,
            Search_Data_SearchDatabase $search) {
        $this->_store = $store;
        $this->_search = $search;
    }
    /**
     * Attempts to add the mod to all databases. If adding to any database fails
     * it tries to rollback.
     *
     * @param array $modDetails
     *
     * @todo Writing a transaction class may be better?
     */
    public function addMod(array $modDetails) {

        if ( !($modDetails['URL'] instanceof URL) ) {
            $modDetails['URL'] = new URL($modDetails['URL']);
        }

        $modDetails = $this->validateMod($modDetails);
        //echo "Adding/Updating Mod: ".$modDetails['Name']."<br />\n";


        $mid = $this->getMid($modDetails);

        $game = $modDetails['Game'];
        unset($modDetails['Game']);

        try {
            $this->_search->addMod(
                    $game,
                    $mid,
                    $modDetails
            );

        }catch(Exception $e ) {
            throw $e;
        }

        //ensures rollback on errors
        try {
            $this->_store->addMod(
                    $game,
                    $mid,
                    $modDetails
            );
        }catch(Exception $e) {
            $this->_search->removeMod($game, $mid);
            throw $e;
        }
    }

    /**
     * Attempts to find a mod id for the given mod, this is destructive as it
     * will alter the database to ensure that the mod fits including removing mods
     *
     * @param array $modDetails
     *
     * @todo rename fuction to reflect what it does
     * @todo contains duplicated code
     */
    private function getMid($modDetails) {
        $url = new URL($modDetails['URL']);

        //check for a mod id with this exact URL
        $urlMid = $this->searchByUrl(
                $modDetails['Game'],
                $modDetails['URL']
        );

        $nameMid = $this->searchExact(
                $modDetails['Game'],
                $modDetails['Name'],
                $modDetails['Author']
        );

        //should result in a mod being updated, as there exists both
        //a mod with this name
        if ( $urlMid !== -1 &&
                $nameMid !== -1 &&
                $nameMid === $urlMid ) {
            return $urlMid;
        }

        //urlMid == -1, nameMid not
        //we can assue that the mod exists, but doesen't know about this location
        if ( $urlMid === -1 && $nameMid !== -1 ) {
            return $nameMid;
        }

        //nameMid == -1, urlMid not.
        //Remove this location, create a new mod with the new details
        if ( $urlMid !== -1 && $nameMid === -1 ) {

            //remove mod location
            $this->_store->removeLocation($urlMid, $url);

            if (  $this->_store->getLocationCount($urlMid) == 0 ) {
                $this->removeMod($modDetails['Game'], $urlMid);
            }

            return $this->_store->getNewID();
        }

        //possible case, in which the location should be removed from
        //its present mod, and added to the mod which matches the new mods name
        if ( $urlMid !== -1 &&
                $nameMid !== -1 &&
                $url !== $nameMid ) {

            //remove mod location $urlMid
            $this->_store->removeLocation($urlMid, $url);

            if (  $this->_store->getLocationCount($urlMid) == 0 ) {
                $this->removeMod($modDetails['Game'], $urlMid);
            }

            return $nameMid;
        }

        //allocate new ID if not found at all
        if ( $urlMid === -1 && $nameMid === -1  ) {
            return $this->_store->getNewID();
        }

        throw new Exception('Not all cases result in a return value');

    }

    /**
     * Validates a list of mods
     *
     * @param array $modDetails
     * @return array
     * @throws ModValidatorException, Exception
     */
    private function validateMod(array $modDetails) {
        $validator = new Search_Data_ModValidator();
        foreach ( $modDetails as $key => $val ) {
            $modDetails[$key] = call_user_func(array($validator, "validate$key"), $val);
        }
        $validator->checkHasRequiredKeys($modDetails);
        return $modDetails;
    }

    /**
     * Removes a mod from the database
     *
     * @param string $game
     * @param int $mid
     */
    public function removeMod($game, $mid) {
        $this->_search->removeMod($game, $mid);
        $this->_store->removeMod($game, $mid);
    }

    /**
     * @param string $game
     * @param string $name
     * @param string $author
     * @param string $description
     * @return SearchResult
     */
    public function searchAdvanced(
            $game,
            $name,
            $author,
            $description,
            $lowerBound,
            $count) {


        return $this->searchFunction(
                __FUNCTION__,
                $game,
                $name,
                $author,
                $description,
                $lowerBound,
                $count
        );
    }
    /**
     * Searches for the general term
     *
     * @param string $game
     * @param string $term
     * @return SearchResult
     */
    public function search($game, $term, $lowerBound, $count) {
        return $this->searchFunction(
                __FUNCTION__,
                $game,
                $term,
                $lowerBound,
                $count
        );
    }
    /**
     * Searches for an exact match. Should return a id
     *
     * @param string $game
     * @param string $name
     * @param string $author
     * @return int id or 0 if none found
     */
    public function searchExact($game, $name, $author) {
        if ( $this->_store->hasMethod('searchExact') ) {
            return $this->_store->searchExact($game, $name, $author);
        }
        if ( $this->_search->hasMethod('searchExact') ) {
            return $this->_search->searchExact($game, $name, $author);
        }
        throw new Exception('Nothing implements the fucntion '.__FUNCTION__);
    }

    public function searchByUrl($game, URL $url) {
        if ( $this->_store->hasMethod('searchByUrl') ) {
            return $this->_store->searchByUrl($game, $url);
        }
        if ( $this->_search->hasMethod('searchByUrl') ) {
            return $this->_search->searchByUrl($game, $url);
        }
        throw new Exception('Nothing implements the fucntion '.__FUNCTION__);
    }



    /**
     * Calls a search function given by $name on the search database.
     *
     * Although the docs say only one arg, pass as many as needed by the function
     *
     * @param string $name
     * @return SearchResult

     */
    private function searchFunction($name) {
        $args = array_slice(func_get_args(),1);

        $result = call_user_func_array(array($this->_search, $name), $args);
        $result = $this->_store->getResultDetails($result);

        //remove errors
        foreach ( $result as $rk => $r ) {
            if ( $r->error ) {
                Logger::log(
                        Logger::CORRUPT_DATA,
                        "Mod was corrupt as data was inconsistant. Now removed from DB",
                        __FILE__,
                        __LINE__
                );

                $this->removeMod($args[0], $r->ModID);
                unset($result[$rk]);
            }
        }

        return $result;
    }



}