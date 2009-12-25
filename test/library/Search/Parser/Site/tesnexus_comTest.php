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


class tesnexus_comTest extends PHPUnit_Framework_TestCase {

    public function testGetPageTest_HasLinks() {
        $url = new URL('http://www.tesnexus.com/downloads/recent.php');
        $page = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url)
                ->getPage($url);
        $links = $page->links();
        $this->assertTrue(count($links) > 10);
    }

    public function testGetPageTest_NoMods() {
        $url = new URL('http://www.tesnexus.com/downloads/recent.php');
        $page = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url)
                ->getPage($url);
        $this->assertEquals(0, count($page->mods()));
    }

    public function testGetPageTest_IsUpdate_True() {
        $url = new URL('http://www.tesnexus.com/downloads/recent.php');
        $site = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url);
        $this->assertTrue($site->isUpdatePage($url));
    }

    public function testGetPageTest_IsUpdate_False() {
        $url = new URL('http://www.tesnexus.com/');
        $site = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url);
        $this->assertFalse($site->isUpdatePage($url));
    }

    public function testLogin() {
        $url = new URL('http://www.tesnexus.com/downloads/file.php?id=24891');
        $site = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url)
                ->getPage($url);

        $mod = $site->mod(0);

        $this->assertEquals(
                $mod['Name'],
                'Kikai And Slof Male Clothing'
        );
    }
}
?>