<?php
/* l-b
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



class modding_history_pageTest extends PageTest {

    function __construct() {
        parent::__construct(
                'modding_history',
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=3848')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=2436'));
    }

    function testModURLs() {
        $valid = array(
                'http://modhistory.fliggerty.com/index.php?dlid=2944',
        );

        $invalid = array(
                'http://modhistory.fliggerty.com/index.php?dlid=jd'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testURLs() {

        $valid = array(
                'http://modhistory.fliggerty.com/index.php?cid=8',
                'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC&limit=30'
        );

        $invalid = array(
                'http://modhistory.fliggerty.com/index.php?cid=a8',
                'http://modhistory.fliggerty.com/index.php?cid=8a',
                'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC&limit=a',
                'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC'
        );

        $this->helpTestUrls($valid, $invalid);
    }

    
    function testLinks() {
        $links = array(
                'http://modhistory.fliggerty.com/index.php?cid=9',
                'http://modhistory.fliggerty.com/index.php?cid=12'
        );
        $this->helpRequiredLinks(
                new Search_Url('http://modhistory.fliggerty.com/index.php?cid=5'),
                $links
        );
    }
    


    function testMod1() {
        $mod = array(
                'Name'      => 'Unholy Temple Armor',
                'Author'    => 'Blackshark64'
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=1740'),
                1,
                $mod
        );
    }

    function testMod2() {
        $mod = array(
                'Name'      => 'All my stuff 3',
                'Author'    => 'Adam'
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=3916'),
                1,
                $mod
        );
    }


    function testMod3() {
        $mod = array(
                'Name' => 'secretcave',
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=3242'),
                1,
                $mod
        );
    }


    function testMod4() {
        $mod = array(
                'Name' => 'Martistan Castle',
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=3296'),
                1,
                $mod
        );
    }


    function testMod5() {
        $mod = array(
                'Name' => 'MORIA',
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=3277'),
                1,
                $mod
        );
    }

    function testMod6() {
        $mod = array(
                'Name' => 'Amazon (Females Only) Race v1.0',
        );
        $this->helpTestModPage(
                new Search_Url('http://modhistory.fliggerty.com/index.php?dlid=1783'),
                1,
                $mod
        );
    }

}
