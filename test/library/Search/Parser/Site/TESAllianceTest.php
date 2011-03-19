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



class tes_alliance_pageTest extends PageTest {

    function __construct() {
        parent::__construct(
                'tes_alliance',
                new Search_Url('http://tesalliance.org/forums/index.php?/files/file/785-alternative-beginnings/')
        );
    }

    function testInstance() {
        $this->helpTestInstance(new Search_Url('http://tesalliance.org/forums/index.php?/files/file/785-alternative-beginnings/'));
    }

    function testModURLs() {
        $valid = array(
            'http://tesalliance.org/forums/index.php?/files/file/653-rivet-city-room/',
            'http://tesalliance.org/forums/index.php?/files/file/817-oblivion-xp/',
        );

        $invalid = array(
            'http://tesalliance.org/forums/index.php?/files/category/70-fallout-3/',
            'http://tesalliance.org/forums/index.php?/files/category/79-dungeons/',
            'http://tesalliance.org/forums/',
            'http://tesalliance.org/forums/index.php?/topic/708-forum-rules-read-this-first-before-you-post/',
            'http://tesalliance.org/forums/index.php?app=core&module=search&do=user_activity&search_app=downloads&mid=443',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    function testURLs() {

        $valid = array(
            'http://tesalliance.org/forums/index.php?/files/category/70-fallout-3/',
            'http://tesalliance.org/forums/index.php?/files/category/79-dungeons/'
        );

        $invalid = array(
            'http://tesalliance.org/forums/',
            'http://tesalliance.org/forums/index.php?/topic/708-forum-rules-read-this-first-before-you-post/',
            'http://tesalliance.org/forums/index.php?app=core&module=search&do=user_activity&search_app=downloads&mid=443',
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
                'Name'      => 'Alternative Beginnings',
                'Author'    => 'Arthmoor',
                'Category'  => 'Game Mechanics',
                'Game'      => 'OB',
        );
        $this->helpTestModPage(
                new Search_Url('http://tesalliance.org/forums/index.php?/files/file/785-alternative-beginnings/'),
                1,
                $mod
        );
    }

    function testMod2() {
        $mod = array(
                'Name'      => 'A Vivec hideout',
                'Author'    => 'Pushkatu',
                'Category'  => 'Houses',
                'Game'      => 'MW',
        );
        $this->helpTestModPage(
                new Search_Url('http://tesalliance.org/forums/index.php?/files/file/434-a-vivec-hideout/'),
                1,
                $mod
        );
    }

}
