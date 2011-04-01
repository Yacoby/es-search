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
class Search_Updater_SiteTest_PageStub extends Doctrine_Record{
    public function __construct() { }
    public function save(Doctrine_Connection $conn = null){}
    public $site_id, $revisit, $url_suffix;
}
class Search_Updater_SiteTest_SiteStub extends Doctrine_Record{
    public function __construct() { }
    public $id = 0;
    public $mod_source_id = 0;
}

class Search_Updater_SiteTest extends PHPUnit_Framework_TestCase {
    /**
     *
     * @var Search_Updater
     */
    protected $_updater;

    /**
     * Mock objects
     */
    protected $_factory, $_pages, $_sites;

    protected function setUp() {
        $this->_factory = $this->getMock(
                'Search_Parser_Factory',
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


        $this->_updater = new Search_Updater_Site(
                $this->_factory,
                $this->_sites,
                $this->_pages
        );
    }    

    /**
     * If it is a mod page, then it is expected to at least run through mods
     * that need adding
     */
    function testAddPageData() {
        $page = $this->getMock(
                'Search_Parser_Site_Page',
                array(),
                array(),
                '',
                false);
        
        $page->expects($this->atLeastOnce())
                ->method('getUrl')
                ->will($this->returnValue(new Search_Url('http://www.bbc.co.uk')));

        $page->expects($this->atLeastOnce())
                ->method('isValidModPage')
                ->will($this->returnValue(true));

        $page->expects($this->atLeastOnce())
                ->method('mods')
                ->will($this->returnValue(array()));

        $page->expects($this->atLeastOnce())
                ->method('links')
                ->will($this->returnValue(array()));

         $this->_sites->expects($this->atLeastOnce())
                ->method('__call')
                ->with($this->equalTo('findOneByHost'), $this->anything())
                ->will($this->returnValue(new Search_UpdaterTest_SiteStub()));

        $this->_updater->processPageData($page);
    }



}