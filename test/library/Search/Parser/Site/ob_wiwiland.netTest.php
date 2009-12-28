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


class Oblivion_WiwlandTest extends PageTest {

    function __construct() {
        parent::__construct(
                'Oblivion_Wiwland',
                new URL('http://oblimods.wiwiland.net/spip.php?article228')
        );
    }


    function testInstance() {
        $this->helpTestInstance(new URL('http://oblimods.wiwiland.net/spip.php?article228'));
    }

    function testMod1() {
        $mod = array(
                'Name' => 'Compagnon Spriggan',
                'Author' => 'Drunkgoblin',
                //'Description' => 'Un savant mélange des mods Wakim’s Game Improvments et The Adventurers',
        );
        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article228'),
                1,
                $mod
        );

    }
    /*
    function testMod2() {
        $mod = array(
            'Name' => 'Amulette du Nécromancien et Heaume de Verdesang',
            'Author' => 'Ancestral Ghost',
        );
        $this->helpTestModPage(
            new URL('http://oblimods.wiwiland.net/spip.php?article325'),
            1,
            $mod
        );
    }
    */

    function testMod3() {
        $mod = array(
                'Name' => 'Son attaque puissante',
                'Author' => 'Ashkhan',
        );
        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article80'),
                1,
                $mod
        );
    }

    function testMod4() {
        $mod = array(
                'Name' => 'De l\'eau pour le Peuple !',
                'Author' => 'Khornate et Qazaaq, traduction de Mag1c Wind0w',
        );

        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article343'),
                1,
                $mod
        );

    }

    function testMod5() {
        $mod = array(
                'Name' => 'Exnem EyeCandy - Nouvelles Armures féminines',
        );

        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article327'),
                1,
                $mod
        );

    }

    function testMod6() {

        $mod = array(
                'Name' => 'Amulette du Nécromancien et Heaume de Verdesang',
        );

        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article325'),
                1,
                $mod
        );
    }

    function testMod7() {
        $mod = array(
                'Name' => 'Races Mazkens & Auréals',
        );

        $this->helpTestModPage(
                new URL('http://oblimods.wiwiland.net/spip.php?article315'),
                1,
                $mod
        );
    }



    function testModURLs() {
        $valid = array(
                'http://oblimods.wiwiland.net/spip.php?article80',
                'http://oblimods.wiwiland.net/spip.php?article0'
        );

        $invalid = array(
                'http://oblimods.wiwiland.net/spip.php?article',
                'http://oblimods.wiwiland.net/spip.php?article0x',
                'http://oblimods.wiwiland.net/spip.php?nx0',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

}

?>