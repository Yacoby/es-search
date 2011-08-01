<?php

/**
 * Class that listens for downloads and adds to the total amount
 * downloaded. It fails silently, in other words if it can't find the host
 * then it doesn't throw an error
 *
 * Used for byte limited sources
 */
class Search_Observer_Limits implements Search_Observer{
    private $_counter;

    function __construct(Search_Table_ByteLimitedSources $b = null) {
        $this->_counter = $b? $b : new Search_Table_ByteLimitedSources();
    }

    public function onRequestDownloaded($url, $response){
        $size = strlen($response->body());
        $site = $this->_counter->findOneByHost($url->getHost());
        if ( $site !== false ){
            $site->bytes_used += (int)$size;
            $site->save();
        }
    }
}
