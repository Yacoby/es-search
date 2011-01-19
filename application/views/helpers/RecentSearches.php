<?php

class Zend_View_Helper_RecentSearches{
    public function recentSearches(){
        $s = new Default_Model_Recent();
        $history = $s->getRecentHistory(8);

        $searches = array();
        foreach ( $history as $item ){
            $searches[] = $this->formatAsLink($item);
        }

        return implode($searches, ' | ');
    }
    public function formatAsLink($item){
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $url     = '?page=1&game=' . $item['game_id'];
        if ( isset($item['general']) ){
            $url .= "&general={$item['general']}";
            return "<a href='{$baseUrl}/search{$url}'>{$item['general']}</a>";
        }

        $strings  = array();
        $urlParts = array();
        foreach ( array('name', 'author', 'description') as $key ){
            if ( !empty($item[$key])){
                $strings[]  = $item[$key];
                $urlParts[] = "{$key}={$item[$key]}";
            }
        }
        $string = implode(' + ', $strings);
        $url   .= "&" . implode('&', $urlParts);
        return "<a href='{$baseUrl}/search{$url}'>{$string}</a>";
    }
}