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
            $url .= "&general=" . htmlspecialchars($item['general'], ENT_QUOTES);
            return "<a href='{$baseUrl}/search{$url}'>"
                 . htmlspecialchars($item['general'], ENT_QUOTES)
                 . "</a>";
        }

        $strings  = array();
        $urlParts = array();
        foreach ( array('name', 'author', 'description') as $key ){
            if ( isset($item[$key]) && trim($item[$key]) != '' ){
                $i          = htmlspecialchars($item[$key], ENT_QUOTES);
                $strings[]  = $i;
                $urlParts[] = "{$key}={$i}";
            }
        }
        $string = implode(' + ', $strings);
        $url   .= "&" . implode('&', $urlParts);
        return "<a href='{$baseUrl}/search{$url}'>{$string}</a>";
    }
}