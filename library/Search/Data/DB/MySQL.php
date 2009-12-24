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
 * Implements a MySQL (Well, any realtional db) storage for mods
 *
 */
class Search_Data_DB_MySQL extends Search_Data_StoreDatabase {

    /**
     *
     * @var Search_Table_Mods
     */
    private $_mods;

    /**
     *
     * @var Search_Table_ModLocation
     */
    private $_locations;

    /**
     *
     * @param Search_Table_Mods $mods
     *      If null, it creates a new object, otherwise the
     *      supplied object is used
     *
     * @param Search_Table_ModLocation $locations
     *      If null, it creates a new object, otherwise the
     *      supplied object is used
     */
    public function __construct(
            Search_Table_Mods $mods = null,
            Search_Table_ModLocation $locations = null
    ) {

        if ( !$mods ) {
            $mods = new Search_Table_Mods();
        }
        $this->_mods = $mods;

        if ( !$locations ) {
            $locations = new Search_Table_ModLocation();
        }
        $this->_locations = $locations;
    }

    /**
     * Selects the highest ID and increments it by one
     * @return int
     */
    public function getNewID() {
        return $this->_mods->getNextID();
    }

    /**
     * Ensures all valid keys are added.
     *
     * @param array $a
     * @return array
     */
    private function addKeys(array $a) {
        $d = array(
                'Description'   => '',
                'Version'       => '',
                'Category'      => ''
        );
        return array_merge($d, $a);
    }

    /**
     *
     * @param string $game Should be OB or MW
     * @param int $mid
     * @param array $details
     *          An array of mod details. Should contain:
     *              Name, Author, Description, URL (can be string or URL)
     */
    public function addMod($game, $mid, array $details) {

        //array of required keys
        $required = array(
            'Name', 'Author', 'Description', 'URL'
        );
        //that are then checked
        foreach ( $required as $value ){
            if ( !isset($details[$value]) ){
                throw new Exception('The key, '.$value.' was not found in $details');
            }
        }

        //merges array with keys that are not required, so there are
        //no missing keys
        $details = $this->addKeys($details);

        //shouldn't be needed any more :)
        $url = ($details['URL'] instanceof URL) ? $details['URL'] : new URL($details['URL']);

        if ( $this->_mods->getMod($mid) ) {
            $this->_mods->updateMod(
                    $mid,
                    $game,
                    $details['Name'],
                    $details['Author']
            );
        }else {
            $this->_mods->addMod(
                    $mid,
                    $game,
                    $details['Name'],
                    $details['Author']
            );
        }

        $location = $this->_locations->getLocation($mid, $url);
        if ( !$location ) {
            $this->_locations->addLocation(
                    $mid,
                    $url,
                    $details['Category'],
                    $details['Version'],
                    $details['Description']
            );
        }else {
            $this->_locations->updateLocation(
                    $mid,
                    $url,
                    $details['Category'],
                    $details['Version'],
                    $details['Description']
            );
        }
    }

    public function hasMod($mid) {
        self::checkNumeric($mid);
        return $this->_mods->getMod($mid) != null;
    }


    public function hasLocation($mid, URL $url) {
        self::checkNumeric($mid);
        assert($url->isValid());
        return $this->_locations->getLocation($mid, $url) != null;
    }

    public function removeLocation($mid, URL $url) {
        self::checkNumeric($mid);
        $this->_locations->removeLocation($mid, $url);
    }

    public function getModLocations($mid) {
        assert(is_numeric($mid));
        return $this->_locations->getLocations($mid)->toArray();
    }

    public function getLocationCount($mid){
        assert(is_numeric($mid));
        return $this->_locations->getLocationCount($mid);
    }

    /**
     * Gets a mod id if there is a mod with an exact match
     *
     * @param string $game
     * @param string $name
     * @param string $author
     * @return int mod id. -1 if there is no match
     */
    public function searchExact($game, $name, $author) {
        return $this->_mods->getID($game, $name, $author);
    }
    public function searchByUrl($game, URL $url) {
        return $this->_locations->getIdByUrl($url);
    }

    public function getResultDetails(Search_Data_SearchResults $sr) {
        if ( count($sr->results()) == 0 ) {
            return new Search_Data_SearchResults(array(),0);
        }

        $ids = array();
        foreach ( $sr->results() as $r ) {
            $ids[] = $r->ModID;
        }


        $details = $this->getMultipleDetails($ids);
        $newDetails = array();

        //sort items
        foreach ( $details->toArray() as $d ) {
            $newDetails[$d['ModID']] = $d;
        }
        $details = $newDetails;



        foreach ( $sr->results() as $rk => $r) {
            assert(array_key_exists($r->ModID, $details));
            foreach ( $details[$r->ModID] as $dk => $dv ) {

                if ( $r->hasVaraible($dk) ) {
                    if ( $r->$dk != $dv ) {
                        $r->error = true;
                    }
                }else {
                    $r->$dk = $dv;
                }
            }
        }

        return $sr;

    }


    /**
     * Gets all file ids listed in the array
     * @param array $ids
     * @return array
     */
    private function getMultipleDetails(array $ids) {
        return $this->_mods->getMods($ids);
    }

    private static function checkNumeric($d) {
        if ( !is_numeric($d) ) {
            throw new Exception('Value wasn\'t a number');
        }
    }


}