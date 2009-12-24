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

class elricm_com_pagetest extends PageTest {

    function __construct(){
        parent::__construct(
            'elricm_com',
            new URL('http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=4')
        );
    }

    function testInstance(){
        $this->helpTestInstance(new URL('http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=4'));
    }

    function testMod1(){
        $mod = array(
            'Name' => 'Ascadian Rose Cottage',
            'Author' => 'Korana',
            'Version' => '1.0'
        );
        $this->helpTestModPage(
            new URL('http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28'),
            10,
            $mod
        );

    }


    function testModURLs(){
        $valid = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21&min=10&orderby=titleA&show=10'
        );

        $invalid = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=mymod',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21m',
            'http://yacoby.silgrad.com/MW/Mods/xyzhtm',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28&orderby=dateA'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

        function testLinks() {
        $links = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28&min=10&orderby=titleA&show=10'
        );
        $this->helpRequiredLinks(
            new URL("http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28"),
            $links
        );
    }

}

?>
