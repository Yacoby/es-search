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

class ModTableTest extends PHPUnit_Framework_TestCase {

    public function setUp(){
        resetDatabse();
    }

    public function testGetNewID() {
        $tbl = new Search_Table_Mods();
        $this->assertEquals($tbl->getNextID(), 0);

        $tbl->addMod(0, 'MW', 'Name', 'Author');
        $this->assertEquals($tbl->getNextID(), 1);
    }

    public function testGetID() {
        $tbl = new Search_Table_Mods();
        $tbl->addMod(0, 'MW', 'Name', 'Author');


        $this->assertEquals(
            $tbl->getID('MW', 'Name', 'Author'),
            0
        );
        $this->assertEquals(
            $tbl->getID('MW','XName', 'Author'),
            -1
        );
        $this->assertEquals(
            $tbl->getID('OB', 'Name', 'Author'),
            -1
        );
    }

}

?>
