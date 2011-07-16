<?php

require_once 'super/Wiwiland.php';

final class WiwlandMwPage extends Super_Wiwland_page {
    function __construct( $url, $html){
        parent::__construct($url, $html, 'morromods');
    }
    function getGame() {
        return "MW";
    }
}
