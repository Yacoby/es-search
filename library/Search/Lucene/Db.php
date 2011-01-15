<?php

/**
 * This is a wrapper around the lucene db that provides all the methods needed
 *
 * It also has a couple of helper functions, that may or may not be a good idea
 * to have. The main benifit of them that i can see if that they abstract how
 * the descriptions are dealt with (the fact they are merged into one big one)
 *
 * @todo This class isn't particuarly unit testaable.
 */
class Search_Lucene_Db{
    /**
     * Adds the mod to all game databases. The mod will not be added
     * if there are no locations
     * 
     * @param Doctrine_Record $mod This record must contain all locations and games
     *                              associated with the mod
     */
    public static function staticAddMod($mod){
        //No need to add a mod if there are no locations
        if ( $mod->Locations->count() == 0 ){
            return;
        }
        
        //Merge the description data from all locations
        $descriptionData = '';
        foreach ( $mod->Locations as $location ){
            $descriptionData .= $location->description . ' ';
        }
        trim($descriptionData);

        foreach ( $mod->Games as $game ){
            $db = new self($game->id);
            $db->addMod($mod->id, $mod->name, $mod->author, $descriptionData);
        }

    }

    /**
     * This removes the mod from all databases that the mod is in.
     * 
     * @param Doctrine_Reocrd $mod
     */
    public static function staticRemoveMod(Doctrine_Reocrd $mod){
        foreach ( $mod->Games as $game ){
            $db = new self($game->id);
            $db->removeMod($mod->id);
        }
    }

    /**
     * This is a cahce so that when we add multiple mods through the static
     * function we don't continually open the database
     * 
     * @var array
     */
    private static $_cache = array();

    /**
     * This is the current database, which the object is using
     *
     * @var Zend_Search_Lucene_Interface
     */
    private $_db;

    /**
     *
     * @param int $game
     */
    public function  __construct($game) {
        $this->setDefaults();

        if ( array_key_exists($game, self::$_cache) ){
            $this->_db = self::$_cache[$game];
        }else{
            $this->_db = $this->openOrCreate($game);
            self::$_cache[$game] = $this->_db;
        }

    }
    private function setDefaults(){
        $defaultAnalysis = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $instanceOf = ($defaultAnalysis instanceOf Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive);
        if ( !$instanceOf ) {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(
                    new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive()
            );
        }
    }

    /**
     * Opens or creates a database, depending on if the database exists or not
     *
     * @param int $game
     * @return Zend_Search_Lucene_Interface
     */
    private function openOrCreate($game){
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

    public function addMod($modId, $name, $author, $desc){

        //remove the mod so we don't add twice
        $this->removeMod($modId);

        $doc = new Zend_Search_Lucene_Document();

        $encoding = 'iso-8859-1';
       
        $doc->addField(Zend_Search_Lucene_Field::Keyword('mod_id', $modId, $encoding));
        $doc->addField(Zend_Search_Lucene_Field::text('name', $name , $encoding));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('author', $author, $encoding));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('description',  $desc, $encoding));
        $this->_db->addDocument($doc);
 
    }

    public function removeMod($modId){
        $hits = $this->_db->find('mod_id:' . $modId);
        foreach ($hits as $hit) {
            $this->_db->delete($hit->id);
        }
    }

    /**
     *
     * @param string $query
     * @param int $lowerBound
     * @param int $length
     * @return Search_Lucene_SearchResults
     */
    public function searchSimple($query, $lowerBound, $length){
        return $this->search($query, $lowerBound, $length);
    }

    /**
     *
     * @param <type> $name
     * @param <type> $author
     * @param <type> $description
     * @param <type> $lowerBound
     * @param <type> $length
     * @return Search_Lucene_SearchResults
     */
    public function searchAdvanced($name, $author, $description, $lowerBound, $length){
         $input = array(
                'name'          => $name,
                'author'        => $author,
                'description'   => $description
        );

        $query = array();
        foreach ( $input as $indexName => $value ) {
            if ( $value != null ) {
                $query[] = "( {$indexName}:({$value}) )";
            }
        }

        return $this->search(
                implode(' AND ', $query),
                $lowerBound,
                $length
        );
    }

    /**
     *
     * @param stromg $queryStr
     * @param int $lowerBound
     * @param int $length
     * @return Search_Lucene_SearchResults
     */
    private function search($queryStr, $lowerBound, $length) {
        $query = null;
        try {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
        } catch (Zend_Search_Lucene_Search_QueryParserException $e) {
            throw new Exception("Query syntax error: " . $e->getMessage()); //pass exception up
        }
        $result = $this->_db->find($query);
        return self::queryHitToResults(
                array_slice($result, $lowerBound, $length),
                count($result)
        );
    }

    /**
     * @return Search_Lucene_SearchResults
     */
    private static function queryHitToResults(array $qh, $total) {
        $results = array();
        foreach ( $qh as $h) {
            $results[] = new Search_Lucene_Result(array(
                            'mod_id' => $h->mod_id,
                            'name'   => $h->name,
                            'score'  => $h->score,
            ));
        }
        return new Search_Lucene_SearchResults($results, $total);
    }

}