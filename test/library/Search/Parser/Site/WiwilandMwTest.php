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
/**
 * @TODO disabled
 */

class Morrowind_WiwlandTest /*extends PageTest*/ {

    function __construct() {
        parent::__construct(
            'Morrowind_Wiwland',
            new Search_Url('http://morromods.wiwiland.net/spip.php?article181')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new Search_Url('http://morromods.wiwiland.net/spip.php?article181'));
    }

    function testMod1() {
        $mod = array(
            'Name' => 'Adventurer Wakim\'s',
            'Author' => 'Ethaniel'
        );
        $this->helpTestModPage(
            new Search_Url('http://morromods.wiwiland.net/spip.php?article181'),
            1,
            $mod
        );

    }
    /*
    function testMod2() {
        $mod = array(
            'Name' => 'Atronach Magie',
            'Author' => 'vinc106',
        );
        $this->helpTestModPage(
            new Search_Url('http://morromods.wiwiland.net/spip.php?article958'),
            1,
            $mod
        );
    }
     */

    function testMod3() {
        $mod = array(
            'Name' => 'Remplacement d\'Almalexia',
            'Author' => 'Westly, cam de Not Quite Dead.',
        );
        $this->helpTestModPage(
            new Search_Url('http://morromods.wiwiland.net/spip.php?article994'),
            1,
            $mod
        );
    }
/*
    function testMod4() {
        $mod = array(
            'Name' => 'Wanted : les Chasseurs de Tamriel',
            'Author' => 'Arcanthias, cam de Flop et Ethaniel, corrections Kafou',
        );
        $this->helpTestModPage(
            new Search_Url('http://morromods.wiwiland.net/spip.php?article996'),
            1,
            $mod
        );
    }
 */

        function testMod5() {
        $mod = array(
            'Name' => 'Chute d\'eau',
            'Author' => 'de Heremod Production - Camembérisé par Zunder',
        );
        $this->helpTestModPage(
            new Search_Url('http://morromods.wiwiland.net/spip.php?article643'),
            1,
            $mod
        );
    }

    function testModURLs() {
        $valid = array(
            'http://morromods.wiwiland.net/spip.php?article255',
            'http://morromods.wiwiland.net/spip.php?article0'
        );

        $invalid = array(
            'http://morromods.wiwiland.net/spip.php?article',
            'http://morromods.wiwiland.net/spip.php?article0x',
            'http://morromods.wiwiland.net/spip.php?nx0',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testLinks() {
        $links = array(
            'http://morromods.wiwiland.net/spip.php?article979',
            'http://morromods.wiwiland.net/spip.php?article255',
        );
        $this->helpRequiredLinks(
            new Search_Url("http://morromods.wiwiland.net/spip.php?page=classemois"),
            $links
        );
    }

}

?>
