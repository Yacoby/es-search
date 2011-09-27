<?php

class Search_Index_Sphinx extends Search_Observable implements Search_Index_Abstract {
    private $_game;
    
    /**
     *
     * @var SphinxClient
     */
    private $_client;
    
    private function setDefaults(){
        $this->_client->setMatchMode(SPH_MATCH_EXTENDED);
        //$this->_client->setMaxQueryTime(3);
        $this->_client->setServer("localhost", 9312);
    }

    public function __construct($game) {
        $this->attach(new Search_Observer_Search());
        
        $this->_client = new SphinxClient();
        $this->_client->SetFilter('game', array($game));
        $this->setDefaults();
    }


    public function searchSimple($query, $offset, $limit) {
        $this->event()->searchSimple($this->_game, $query);
        $this->_client->setLimits($offset, $limit);
        return $this->parseResult($this->_client->query($query));
    }
    
    public function searchAdvanced($name, $author, $description, $offset, $limit) {
        $this->event()->searchAdvanced($this->_game, $name, $author, $description);
        $this->_client->setLimits($offset, $limit);
        
        $query = '';
        
        if ( $name ){
            $query .= "@name {$name} ";
        }
        if ( $author ){
            $query .= "@author {$author} ";
        }
        if ( $description ){
            $query .= "@description {$author} ";
        }
        
        return $this->parseResult($this->_client->query($query));
    }
    
    private function parseResult($results){
        $parsedResults = array();
        
        if ( $results['total'] > 0 ){
            foreach ( $results['matches'] as $id => $attr ) {
                $parsedResults[] = array(
                                'mod_id' => $id,
                                'score'  => $attr['weight'],
                );
            }
        }
        return new Search_Index_Results($parsedResults, $results['total_found']);
    }

}
