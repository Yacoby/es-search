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


class shsforums_netTest extends PageTest {

    function __construct() {
        parent::__construct(
                'shsforums_net',
                new URL('http://www.shsforums.net/index.php?autocom=downloads&showfile=568')
        );
    }

    function testInstance() {
        $this->helpTestInstance(
                new URL('http://www.shsforums.net/index.php?autocom=downloads&showfile=568')
        );
    }

    function testMod1() {
        $mod = array(
                'Name' => 'White Stallion Lodge replacement',
                'Author' => 'Spirited Treasure',
                'Category' => 'Buildings and Factions',
        );
        $this->helpTestModPage(
                new URL('http://www.shsforums.net/index.php?autocom=downloads&showfile=568'),
                1,
                $mod
        );

    }

    function testModURLs() {
        $valid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showfile=568'
        );

        $invalid = array(
                'http://www.shsforums.net/index.php?autocom=downloads',
                'http://www.shsforums.net/index.php?autocom=downloads&showfile=568&x',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testURLs() {
        $valid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=51',
                'http://www.shsforums.net/index.php?automodule=downloads&showcat=3',
        );

        $invalid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=mymod',
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=51x',
        );
        $this->helpTestUrls($valid, $invalid);

    }

    function testLinks() {
        $links = array(
                'http://www.shsforums.net/index.php?automodule=downloads&showcat=3'
        );


        $this->helpRequiredLinks(
                new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=52'),
                $links
        );
    }

    function testStrip1() {
        $url = new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=60');
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);
        $url = $p->stripFromLinks($url);
        $this->assertTrue(
                $p->isValidPage($url)
        );

    }

    function testStrip2() {
        $t = new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=52');
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($t)->getPage($t);


        $l = 'http://www.shsforums.net/index.php?s=847ed92ad1c25dd7bd9f9106b0fe7ee5&amp;automodule=downloads&showcat=3';
        $url = new URL(html_entity_decode($l), $t);

        $url = $p->stripFromLinks($url);

        $this->assertTrue(
                $p->isValidPage($url)
        );

    }


}

?>
