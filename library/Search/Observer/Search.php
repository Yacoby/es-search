<?php

/**
 * Class for storing recent searches.
 *
 * This attempts to avoid letting people put bad stuff up there, however
 * it isn't as good as it could be.
 */
class Search_Observer_Search{
    private $_history, $_bans;

    private $_badWords = array(
        'nudity',
        'adult',
        'sex',
        'boob',
        'cunt',
        'fuck',
        'shit',
        'arse',
        'ass',
        'gay',
        '*',
    );

    public function  __construct(
            Search_Table_SearchHistory $h = null,
            Search_Table_HistoryBanned $b = null
            ) {
        $this->_history = $h ? $h : new Search_Table_SearchHistory();
        $this->_bans    = $b ? $b : new Search_Table_HistoryBanned();
    }

    private function isBadTerm($term){
        foreach ( $this->_badWords as $word ){
            if ( stripos($term, $word) !== false ){
                return true;
            }
        }
        return false;
    }

    private function getWithDefaults($game){
        $row = $this->_history->create();
        $row->game_id     = $game;
        $row->ip          = $_SERVER['REMOTE_ADDR'];
        $row->search_time = time();
        return $row;
    }

    private function banUser(){ 
        $row = $this->_bans->create();
        $row->ip          = $_SERVER['REMOTE_ADDR'];
        $row->banned_time = time();
        $row->replace();        
    }

    public function searchSimple($game, $general){
        if ( trim($general) == "" ){
            return;
        }
        if ( $this->isBadTerm($general) ){
            $this->banUser();
            return;
        }
        $row          = $this->getWithDefaults($game);
        $row->general = $general;
        $row->replace();
    }

    public function searchAdvanced($game, $name, $author, $description){
        foreach ( array($name, $author, $description) as $v ){
            if ( trim($v) == "" ){
                return;
            }
        }

        if ( $this->isBadTerm($name . ' ' . $author . ' ' . $description) ){
            $this->banUser();
            return;
        }
        
        $row = $this->getWithDefaults($game);
        $row->name        = $name;
        $row->author      = $author;
        $row->description = $description;
        $row->replace();
    }

}
