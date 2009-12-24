<?php /* l-b
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
 * l-b */ ?>

<?php
require 'Req/wiwiland_super.php';

final class Morrowind_Wiwland extends Wiwland_Super {
    function __construct(){
        parent::__construct('morromods');
    }
    public static function getHost(){
        return 'morromods.wiwiland.net';
    }
}

final class Morrowind_Wiwland_page extends Super_Wiwland_page {
    function __construct(URL $url, Search_Parser_Dom $html){
        parent::__construct($url, $html, 'morromods');
    }
    function getGame() {
        return "MW";
    }
}


final class Oblivion_Wiwland extends Wiwland_Super {
    function __construct(){
        parent::__construct('oblimods');
    }
    public static function getHost(){
        return 'oblimods.wiwiland.net';
    }
}

final class Oblivion_Wiwland_page extends Super_Wiwland_page {
    function __construct(URL $url, Search_Parser_Dom $html){
        parent::__construct($url, $html, 'oblimods');
    }
    function getGame() {
        return "OB";
    }
}
