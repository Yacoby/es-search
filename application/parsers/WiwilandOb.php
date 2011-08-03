<?php

require_once 'super/Wiwiland.php';

final class WiwlandObPage extends Super_Wiwland_page {
    function __construct($response){
        parent::__construct($response, 'oblimods');
    }
    function getGame() {
        return "OB";
    }
}
