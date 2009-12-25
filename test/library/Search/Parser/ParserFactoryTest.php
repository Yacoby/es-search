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


class TestSite extends Search_Parser_Site {}
/**
 * Bugger to test atm as everything is grabbed from the db
 */
class Search_Parser_FactoryTest extends PHPUnit_Framework_TestCase {

    public function setUp() {}


    public function testSingleton() {
        $pf1 = Search_Parser_Factory::getInstance();
        $pf2 = Search_Parser_Factory::getInstance();
        $this->assertEquals($pf1, $pf2);
    }

}


?>
