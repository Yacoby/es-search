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

class Search_HTTP_ClientTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_HTTP_Client
     */
    protected $_client;
    protected $_rawClient, $_limits, $_jar;

    protected function setUp() {
        $this->_rawClient = $this->getMock('Zend_Http_Client');
        $this->_limits    = $this->getMock('Search_HTTP_Limits', array(),
                                           array(), '', false);
        $this->_jar       = $this->getMock('Search_Table_CookieJar',
                                           array(), array(), '', false);

    }


    public function testCanGetWebpage() {
        //debug call, it must return true
        $this->_limits->expects($this->any())
                ->method('hasLimits')
                ->will($this->returnValue(true));

        $url = new Search_Url('http://example.com');
        $this->_limits->expects($this->once())
                ->method('canGetPage')
                ->with($this->equalTo($url))
                ->will($this->returnValue(true));

        $client = new Search_HTTP_Client(
                $this->_rawClient,
                $this->_limits,
                $this->_jar
        );

        $this->assertTrue($client->canGetWebpage($url));
    }


    public function testGetWebpage() {
        $url = new Search_Url('http://example.com');
        /*
        $this->_limits->expects($this->once())
                ->method('canGetPage')
                ->with($this->equalTo($url))
                ->will($this->returnValue(true));
         */

        $this->_jar->expects($this->once())
                ->method('getCookies')
                ->will($this->returnValue(array()));


        $client = new Search_HTTP_Client(
                null,
                $this->_limits,
                $this->_jar
        );

        $result = $client->request($url)->withCache(false)->exec();
        $this->assertEquals(200, $result->getStatus());

        $index = stripos($result->getBody(), "RFC");
        $this->assertTrue($index!==false);
    }


}
?>
