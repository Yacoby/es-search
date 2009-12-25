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


class Search_UpdaterTest extends PHPUnit_Framework_TestCase {
    /**
     *
     * @var Search_Updater
     */
    protected $_updater;

    /**
     * Mock objects
     */
    protected $_mdb, $_vp, $_site;


    protected function setUp() {
        $this->_mdb = $this->getMock(
                'Search_Data_UnifiedModDatabase',
                array(),
                array(),
                '',
                false);

        $this->_vp      = $this->getMock('Search_Table_VisitedPage');
        $this->_site    = $this->getMock(
                'Search_Table_Website',
                array(),
                array(),
                '',
                false);


        $this->_updater = new Search_Updater(
                $this->_mdb,
                $this->_vp,
                $this->_site
        );
    }

    /**
     * checks that if no update pages need updating, then no updates are
     * preformed
     */
    function testAttemptUpdate() {
        //for no update pages to need updating, getSiteNeedingUpdate has
        //to return null

        $this->_vp->expects($this->any())
                ->method('getSiteNeedingUpdate')
                ->will($this->returnValue(null));

        $this->assertFalse($this->_updater->attemptUpdatePage());
    }

    /**
     * Tests inputting data from a page into the database
     */
    function testupdateOrAddLink1() {
        $url = new URL('http://example.com');

        $this->_vp->expects($this->atLeastOnce())
                ->method('hasPage')
                ->will($this->returnValue(false));

        $this->_vp->expects($this->atLeastOnce())
                ->method('addPage')
                ->with($this->equalTo($url));

        $this->_updater->updateOrAddLink($url);
    }

    function testupdateOrAddLink2() {
        $url = new URL('http://example.com');

        $this->_vp->expects($this->atLeastOnce())
                ->method('hasPage')
                ->will($this->returnValue(true));

        $this->_vp->expects($this->never())
                ->method('addPage');

        $std = new stdClass();
        $std->NeedRevisit = 0;
        $std->URL = $url;
        $this->_vp->expects($this->any())
                ->method('getPage')
                ->will($this->returnValue($std));

        $this->_vp->expects($this->atLeastOnce())
                ->method('setNeedVisit');

        $this->_updater->updateOrAddLink($url);
    }


    /**
     * If it is a mod page, then it is expected to at least run through mods
     * that need adding
     */
    function testAddPageData1() {
        $page = $this->getMock(
                'Search_Parser_Page',
                array(),
                array(),
                '',
                false);

        $page->expects($this->atLeastOnce())
                ->method('isValidModPage')
                ->will($this->returnValue(true));

        $page->expects($this->atLeastOnce())
                ->method('mods')
                ->will($this->returnValue(array()));

        $page->expects($this->atLeastOnce())
                ->method('links')
                ->will($this->returnValue(array()));

        $this->_updater->addPageData($page);
    }


    /**
     * If it is not a mods page, then the mods function shouldn't be called
     */
    function testAddPageData2() {
        $page = $this->getMock(
                'Search_Parser_Page',
                array(),
                array(),
                '',
                false);

        $page->expects($this->atLeastOnce())
                ->method('isValidModPage')
                ->will($this->returnValue(false));

        $page->expects($this->never())
                ->method('mods')
                ->will($this->returnValue(array()));

        $page->expects($this->atLeastOnce())
                ->method('links')
                ->will($this->returnValue(array()));

        $this->_updater->addPageData($page);
    }

}