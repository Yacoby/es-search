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

class Search_Data_ResultTest extends PHPUnit_Framework_TestCase {

    protected $_result;

    public function setUp(){
        $this->_result = new Search_Data_Result();
    }

    public function testCheckErrorSet() {
        $this->assertEquals(false, $this->_result->error);
    }

    public function testCheckSetGet() {
        $this->_result->myint = 1;
        $this->assertEquals(1, $this->_result->myint);
    }

    public function testHasVar(){
        $this->_result->var = 1;
        $this->assertTrue($this->_result->hasVaraible('var'));
        $this->assertFalse($this->_result->hasVaraible('not_var'));
    }


}