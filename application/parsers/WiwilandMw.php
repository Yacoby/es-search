<?php

require_once 'super/Wiwiland.php';

final class WiwlandMwPage extends Super_Wiwland_page {

    function __construct($response){
        parent::__construct($response, 'morromods');
    }
    function getGame() {
        return "MW";
    }
}
