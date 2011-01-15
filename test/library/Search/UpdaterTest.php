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
class Search_UpdaterTest_PageStub extends Doctrine_Record{
    public function __construct() { }
    public function save(){}
    public $site_id, $revisit, $url_suffix;
}
class Search_UpdaterTest_SiteStub extends Doctrine_Record{
    public function __construct() { }
    public $id = 0;
}

class Search_UpdaterTest extends PHPUnit_Framework_TestCase {
    /**
     *
     * @var Search_Updater
     */
    protected $_updater;

    /**
     * Mock objects
     */
    protected $_mods, $_pages, $_sites;



    protected function setUp() {
        $this->_mods   = $this->getMock(
                'Search_Table_Mods',
                array(),
                array(),
                '',
                false);

        $this->_pages   = $this->getMock(
                'Search_Table_Pages',
                array(),
                array(),
                '',
                false);
        $this->_sites   = $this->getMock(
                'Search_Table_Sites',
                array(),
                array(),
                '',
                false);

        $conn          = $this->getMock(
                'Doctrine_Connection',
                array(),
                array(),
                '',
                false);
        
        $this->_pages->expects($this->any())
                     ->method('getConnection')
                     ->will($this->returnValue($conn));


        $this->_updater = new Search_Updater(
                $this->_sites,
                $this->_pages,
                $this->_mods
        );
    }

    /**
     * checks that if no update pages need updating, then no updates are
     * preformed
     */
    function testAttemptUpdate() {
        //for no update pages to need updating, getSiteNeedingUpdate has
        //to return null

        $pf = $this->getMock(
                'Search_Parser_Factory',
                array(),
                array(),
                '',
                false);

        $this->_sites->expects($this->any())
                     ->method('__call')
                     //->with($this->equalTo('findOneByUpdateRequired'), $this->anything())
                     ->will($this->returnValue(false));

        $this->assertFalse($this->_updater->attemptUpdatePage($pf));
    }

    /**
     * Tests inputting data from a page into the database
     */
    function testupdateOrAddLink1() {
        $url = new Search_Url('http://example.com');

        $this->_sites->expects($this->any())
                ->method('__call')
                ->with($this->equalTo('findOneByHost'), $this->anything())
                ->will($this->returnValue(new Search_UpdaterTest_SiteStub()));

        $this->_pages->expects($this->atLeastOnce())
                     ->method('findOneByUrl')
                     ->will($this->returnValue(false));

        $this->_pages->expects($this->atLeastOnce())
                     ->method('create')
                     ->will($this->returnValue(new Search_UpdaterTest_PageStub()));

        //TODO THis doesn't work?
        //$this->_updater->addOrUpdateLink($url);
    }

    function testupdateOrAddLink2() {
        $url = new Search_Url('http://example.com');

        $this->_pages->expects($this->atLeastOnce())
                ->method('__call')
                ->with($this->equalTo('findOneByUrl'), $this->anything())
                ->will($this->returnValue(new Search_UpdaterTest_PageStub()));

        $this->_pages->expects($this->never())
                ->method('create');

        $this->_updater->addOrUpdateLink($url);
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
                ->method('mods');

        $page->expects($this->atLeastOnce())
                ->method('links')
                ->will($this->returnValue(array()));

        $this->_updater->addPageData($page);
    }

}