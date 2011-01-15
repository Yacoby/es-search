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
 * l-b */

require_once "PageHelper.php";

class yacoby_silgrad_comTest extends PageTest {

    function __construct() {
        parent::__construct(
            'yacoby_silgrad_com',
            new Search_Url('http://yacoby.silgrad.com/MW/Mods/PursuitEnhanced.htm')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new Search_Url('http://yacoby.silgrad.com/MW/Mods/PursuitEnhanced.htm'));
    }

    function testMod1() {
        $mod = array(
            'Name' => 'Pursuit Enhanced'
        );
        $this->helpTestModPage(
            new Search_Url('http://yacoby.silgrad.com/MW/Mods/PursuitEnhanced.htm'),
            1,
            $mod
        );

    }
    function testMod2() {
        $mod = array(
            'Name' => 'Swimming Realism'
        );
        $this->helpTestModPage(
            new Search_Url('http://yacoby.silgrad.com/MW/Mods/SwimmingRealism.htm'),
            1,
            $mod
        );
    }

    function testURLs() {
        $valid = array(
            'http://yacoby.silgrad.com/MW/Mods/myMod.htm'
        );

        $invalid = array(
            'http://yacoby.silgrad.com/MW/Mods/index.htm',
            'http://yacoby.silgrad.com/MW/Mods/Files/zyx.htm',
            'http://yacoby.silgrad.com/MW/Mods/xyzhtm'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testLinks() {
        $links = array(
            'http://yacoby.silgrad.com/MW/Mods/VampireDoorsBugfix.htm'
        );
        $this->helpRequiredLinks(
            new Search_Url("http://yacoby.silgrad.com/MW/Mods/SwimmingRealism.htm"),
            $links
        );
    }

}
