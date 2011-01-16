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
 * Class for storing recent searches.
 *
 * This attempts to avoid letting people put bad stuff up there, however
 * it isn't as good as it could be.
 */
class Search_Observer_Search implements Search_Observer{
    private $_history, $_bans;

    private $_badWords = array(
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
        if ( $this->isBadTerm($general) ){
            $this->banUser();
            return;
        }
        $row = $this->getWithDefaults($game);
        $row->general     = $general;
        $row->replace();
    }

    public function searchAdvanced($game, $name, $author, $description){
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