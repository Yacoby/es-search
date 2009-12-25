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

/**
 *
 * A lot of functions are not tested as they are simple wrappers
 * to either Search_Table_Mods or Search_Table_ModLocation
 */
class Search_Data_DB_MySQLTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Search_Data_DB_MySQL
     */
    private $_store;

    private $_modTable, $_locTable;


    function setUp() {
        $this->_modTable = $this->getMock('Search_Table_Mods');
        $this->_locTable = $this->getMock('Search_Table_ModLocation');

        $this->_store = new Search_Data_DB_MySQL(
                $this->_modTable,
                $this->_locTable
        );
    }

    /**
     * Checks that if the mod exists, update mod is called. Same for location
     */
    public function testAddMod1() {
        $mid = 1;

        $this->_modTable->expects($this->atLeastOnce())
                ->method('getMod')
                ->with($this->equalTo($mid))
                ->will($this->returnValue(true));

        $this->_modTable->expects($this->once())
                ->method('updateMod');

        $this->_modTable->expects($this->never())
                ->method('addMod');


        $this->_locTable->expects($this->atLeastOnce())
                ->method('getLocation')
                ->will($this->returnValue(true));

        $this->_locTable->expects($this->never())
                ->method('addLocation');

        $this->_locTable->expects($this->once())
                ->method('updateLocation');


        $this->_store->addMod('MW', $mid, array(
                'Name'          => 'N',
                'Author'        => 'A',
                'Description'   => 'D',
                'URL'           => 'http://www.example.com'
        ));
    }

    /**
     * Checks that if the mod doesn't exist, it is added. Same for location
     */
    public function testAddMod2() {
        $mid = 1;
        $url = new URL('http://www.example.com');

        $this->_modTable->expects($this->atLeastOnce())
                ->method('getMod')
                ->with($this->equalTo($mid))
                ->will($this->returnValue(false));

        $this->_modTable->expects($this->once())
                ->method('addMod');

        $this->_modTable->expects($this->never())
                ->method('updateMod');


        $this->_locTable->expects($this->atLeastOnce())
                ->method('getLocation')
                ->will($this->returnValue(false));

        $this->_locTable->expects($this->once())
                ->method('addLocation')
                ->with($this->equalTo($mid), $this->equalTo($url));

        $this->_locTable->expects($this->never())
                ->method('updateLocation');


        $this->_store->addMod('MW', $mid, array(
                'Name'          => 'N',
                'Author'        => 'A',
                'Description'   => 'D',
                'URL'           => $url
        ));
    }


    /**
     * checks what happens when invalid args are passed
     */
    public function testAddMod3() {
        $this->setExpectedException('Exception');

        $this->_store->addMod('MW', $mid, array(
                'Author'        => 'A',
                'Description'   => 'D',
                'URL'           => 'http://example.com'
        ));
    }


    /**
     * @todo Implement Test
     */
    public function getResultDetails() {
  
    }


}