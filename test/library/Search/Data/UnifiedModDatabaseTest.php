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

class Search_Data_UnifiedModDatabaseTest extends PHPUnit_Framework_TestCase {
    private $_umd, $_store, $_search;

    public function setup() {
        $this->_store = $this->getMock('Search_Data_DB_MySQL');
        $this->_search  = $this->getMock('Search_Data_DB_Lucene');

        $this->_umd = new Search_Data_UnifiedModDatabase($this->_store, $this->_search);
    }

    public function testAddMod() {
        $this->_store->expects($this->once())
                ->method('addMod');
        $this->_search->expects($this->once())
                ->method('addMod');

        $modDetails = array(
                'Name'          => 'Amazing',
                'Author'        => 'Great',
                'Description'   => 'Mod',
                'URL'           => 'http://foobar.com',
                'Game'          => 'MW',
        );

        $this->_umd->addMod($modDetails);
    }


    public function testSearch() {
        $this->_search->expects($this->once())
                ->method('search')
                ->will($this->returnValue(new Search_Data_SearchResults(array(), 0)));

        $this->_store->expects($this->once())
                ->method('getResultDetails')
                ->will($this->returnValue(array()));

        $this->_umd->search('MW', 'Amazing', 0, 15);
    }

    public function testAddMod1() {
        $this->_store->expects($this->any())
                ->method('searchByUrl')
                ->will($this->returnValue(-1));

        $this->_store->expects($this->any())
                ->method('searchExact')
                ->will($this->returnValue(-1));

        $this->_store->expects($this->once())
                ->method('getNewID');

        $modDetails = array(
                'Name'          => 'Amazing',
                'Author'        => 'Great',
                'Description'   => 'Mod',
                'URL'           => 'http://foobar.com',
                'Game'          => 'MW',
        );


        $this->_umd->addMod($modDetails);

    }

    public function testAddMod2() {
        $this->_store->expects($this->any())
                ->method('searchByUrl')
                ->will($this->returnValue(0));

        $this->_store->expects($this->any())
                ->method('searchExact')
                ->will($this->returnValue(-1));

        $this->_store->expects($this->once())
                ->method('removeLocation');

        $this->_store->expects($this->once())
                ->method('getLocationCount')
                ->will($this->returnValue(0));

        $this->_store->expects($this->once())
                ->method('removeMod');

        $this->_search->expects($this->once())
                ->method('removeMod');


        $modDetails = array(
                'Name'          => 'Amazing',
                'Author'        => 'Great',
                'Description'   => 'Mod',
                'URL'           => 'http://foobar.com',
                'Game'          => 'MW',
        );


        $this->_umd->addMod($modDetails);
    }


    public function testAddMod3() {

        $this->_store->expects($this->any())
                ->method('searchByUrl')
                ->will($this->returnValue(0));

        $this->_store->expects($this->any())
                ->method('searchExact')
                ->will($this->returnValue(-1));

        $this->_store->expects($this->once())
                ->method('removeLocation');

        $this->_store->expects($this->once())
                ->method('getLocationCount')
                ->will($this->returnValue(1));

        $this->_store->expects($this->never())
                ->method('removeMod');

        $this->_search->expects($this->never())
                ->method('removeMod');


        $modDetails = array(
                'Name'          => 'Amazing',
                'Author'        => 'Great',
                'Description'   => 'Mod',
                'URL'           => 'http://foobar.com',
                'Game'          => 'MW',
        );


        $this->_umd->addMod($modDetails);

    }


    public function testAddMod4() {
        $this->_store->expects($this->any())
                ->method('searchByUrl')
                ->will($this->returnValue(1));

        $this->_store->expects($this->any())
                ->method('searchExact')
                ->will($this->returnValue(2));

        $this->_store->expects($this->once())
                ->method('removeLocation');

        $this->_store->expects($this->once())
                ->method('getLocationCount')
                ->will($this->returnValue(1));

        $this->_store->expects($this->never())
                ->method('removeMod');

        $this->_search->expects($this->never())
                ->method('removeMod');


        $modDetails = array(
                'Name'          => 'Amazing',
                'Author'        => 'Great',
                'Description'   => 'Mod',
                'URL'           => 'http://foobar.com',
                'Game'          => 'MW',
        );


        $this->_umd->addMod($modDetails);

    }


}
?>
