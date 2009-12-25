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
 * This isn't tested much as it is a basic wrapper arround Search_Table_Website
 */
class Search_HTTP_LimitsTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_Table_Website
     */
    protected $_limits;

    private $_sites;

    protected function setUp() {
        $this->_sites = $this->getMock(
                'Search_Table_Website',
                array(),
                array(),
                '',
                false);

        $this->_limits = new Search_HTTP_Limits($this->_sites);
    }


    public function testCanGetPage1() {
        $this->_sites->expects($this->once())
                ->method('getLimits')
                ->will($this->returnValue(array('BytesUsed' => 20, 'ByteLimit' => 10)));

        $this->assertFalse(
                $this->_limits->canGetPage(new URL('http://example.com'))
        );
    }
    public function testCanGetPage2() {
        $this->_sites->expects($this->once())
                ->method('getLimits')
                ->will($this->returnValue(array('BytesUsed' => 10, 'ByteLimit' => 20)));

        $this->assertTrue(
                $this->_limits->canGetPage(new URL('http://example.com'))
        );
    }

    public function testCanGetPage3() {
        $this->_sites->expects($this->once())
                ->method('getLimits')
                ->will($this->returnValue(array('BytesUsed' => 10, 'ByteLimit' => 10)));

        $this->assertFalse(
                $this->_limits->canGetPage(new URL('http://example.com'))
        );
    }

}


?>

