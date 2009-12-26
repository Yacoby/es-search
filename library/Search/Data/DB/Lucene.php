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
 * Holds the search data
 *
 * Lazy databasease. Will not open unless it has work todo.
 */
class Search_Data_DB_Lucene extends Search_Data_SearchDatabase {
    /**
     * @var array Zend_Search_Lucene_Interface
     */
    private $_db            = array();
    private $_hasModified   = false;
    private $_rebuild       = false;

    /**
     * Gets a single database. Should be used for searching. SHOULD NOT BE USED
     * WHEN ADDDING DATA
     *
     * @param string $game
     * @return Zend_Search_Lucene_Interface
     */
    private function getSingleDatabase($game) {
        switch ( $game ) {
            case 'UN': //Allow fallthough
            case 'MW':
                return $this->_getOrOpenDatabase('MW');
            case 'OB':
                return $this->_getOrOpenDatabase('OB');
        }
        throw new Exception("Unknown Game ($game)");
    }

    /**
     * Gets a array of databases that math $game
     *
     * If the database needs to be modified, dbs should be retrived via this func
     *
     * @param string $game
     * @return array
     */
    private function getDatabases($game) {
        switch ( $game ) {
            case 'MW':
            case 'OB'://Allow fallthough
                return array(
                        $this->getSingleDatabase($game)
                );
            case 'UN':
                return array(
                        $this->getSingleDatabase('MW'),
                        $this->getSingleDatabase('OB'),
                );
        }
        throw new Exception("Unknown Game ($game)");
    }

    /**
     *
     * @param <type> $game
     * @return Zend_Search_Lucene_Interface
     */
    private function _getOrOpenDatabase($game) {
        if ( array_key_exists($game, $this->_db) ) {
            return $this->_db[$game];
        }
        $this->_db[$game] = $this->_openDatabase($game);

        if ( $this->_rebuild ) {
            $this->_db[$game]->setMaxBufferedDocs(50);
            $this->_db[$game]->setMergeFactor(50);
        }

        return $this->_db[$game];
    }

    /**
     *
     * @param <type> $game
     * @return Zend_Search_Lucene_Interface
     */
    private function _openDatabase($game) {
        //if not set, the the defailt Zend_Search_Lucene_Analysis_Analyzer
        $defaultAnalysis = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $instanceOf = ($defaultAnalysis instanceOf Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive);
        if ( !$instanceOf ) {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(
                    new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive()
            );
        }


        //requirements
        if ( !defined('APPLICATION_PATH') ) {
            throw new Exception("APPLICATION_PATH is not defined");
        }
        if ( !defined('APPLICATION_ENV') ) {
            throw new Exception("APPLICATION_ENV is not defined");
        }

        //set the databse to be used
        $env = "lucene_" . APPLICATION_ENV;

        $db = APPLICATION_PATH.'/../data/'.$env.'/' . $game;

        if ( !file_exists($db) ) {
            return Zend_Search_Lucene::create($db);
        }else {
            return Zend_Search_Lucene::open($db);
        }
    }


    public function __destruct() {
        if ( $this->_hasModified == true ) {
            foreach ( $this->_db as $db ) {
                $db->commit();
                $db->optimize();
            }
        }
    }

    /**
     * @return Search_Data_SearchResults
     */
    public function searchAdvanced($game, $name, $author, $description, $lowerBound, $length) {
        $input = array(
                $name           => 'Name',
                $author         => 'Author',
                $description    => 'Description'
        );

        $query = array();
        foreach ( $input as $value => $indexName) {
            if ( $value != null ) {
                $query[] = "( {$indexName}:({$value}) )";
            }
        }

        return $this->doSearch(
                $this->getSingleDatabase($game),
                implode(' AND ', $query),
                $lowerBound,
                $length
        );
    }

    /**
     * @return Search_Data_SearchResults
     */
    public function search($game, $general, $lowerBound, $length) {
        $db = $this->getSingleDatabase($game);
        return $this->doSearch($db, $general, $lowerBound, $length);
    }
    /**
     * @return Search_Data_SearchResults
     */
    private function doSearch(Zend_Search_Lucene_Interface $db, $queryStr, $lowerBound, $length) {
        $query = null;
        try {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
        } catch (Zend_Search_Lucene_Search_QueryParserException $e) {
            throw new Exception("Query syntax error: " . $e->getMessage()); //pass exception up
        }
        $result = $db->find($query);
        $count = count($result);

        return self::queryHitToResults(
                array_slice($result, $lowerBound, $length),
                $count
        );
    }
    /**
     * @return Search_Data_SearchResults
     */
    private static function queryHitToResults(array $qh, $total) {
        $results = array();
        foreach ( $qh as $h) {
            $results[] = new Search_Data_Result(array(
                            'ModID' => $h->ModID,
                            'Name'  => $h->Name,
            ));
        }
        return new Search_Data_SearchResults($results, $total);
    }

    /*
     * adds a mod with the given mid, removing it if it already exists
    */
    public function addMod($game, $mid, array $details) {
        assert(isset($details['Name']));
        assert(isset($details['Author']));
        assert(isset($details['Description']));
        assert(in_array($game, array('OB', 'MW', 'UN')));

        $encoding = 'iso-8859-1';

        if ( !is_numeric($mid) ) {
            throw new Exception("Invlid id");
        }

        //remove the mod so we don't add twice
        $this->removeMod($game, $mid);

        $doc = new Zend_Search_Lucene_Document();
        {
            $doc->addField(Zend_Search_Lucene_Field::Keyword('ModID', $mid, $encoding)); //store the uid
            $doc->addField(Zend_Search_Lucene_Field::text('Name', $details['Name'] , $encoding));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('Author', $details['Author'], $encoding));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('Description',  $details['Description'], $encoding));
        }

        foreach ( $this->getDatabases($game) as $db ) {
            $db->addDocument($doc);
        }


        //allow optomizations on desctruct
        $this->_hasModified = true;

    }

    public function getModCount($game) {
        $db = $this->getSingleDatabase($game);
        return $db->count();
    }

    /**
     * Remove a mid, if it exists
     */
    public function removeMod($game, $mid) {
        $dbs = $this->getDatabases($game);

        foreach ( $dbs as $db ) {
            $hits = $db->find('ModID:' . $mid);
            foreach ($hits as $hit) {
                $db->delete($hit->id);
            }
        }
        $this->_hasModified = true;
    }

    /**
     * Must be done before any indexes are opened. Used for batch indexing
     *
     * @param bool $v
     */
    public function setRebuildMode($v = true) {
        $this->_rebuild = $v;
    }
}