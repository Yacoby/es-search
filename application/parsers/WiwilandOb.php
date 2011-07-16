<?php

require_once 'super/Wiwiland.php';

final class WiwlandObPage extends Super_Wiwland_page {
    function __construct($url, $html){
        parent::__construct($url, $html, 'oblimods');
    }
    function getGame() {
        return "OB";
    }
}
