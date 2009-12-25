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


class VisitedPageTableTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        resetDatabse();
        $tbl = new Search_Table_VisitedPage();
        $tbl->delete('1=1'); //delete everything
    }

    public function testAddPage() {
        $tbl = new Search_Table_VisitedPage();
        $this->assertTrue($tbl->getPage(new URL('http://yacoby.silgrad.com')) == null);

        $tbl->addPage(new URL('http://yacoby.silgrad.com'));
        $this->assertTrue($tbl->getPage(new URL('http://yacoby.silgrad.com')) != null);
    }

    public function testPageVisited1() {
        $tbl = new Search_Table_VisitedPage();
        $tbl->addPage(new URL('http://yacoby.silgrad.com'), 1);
        $this->assertEquals(
            0,
            $tbl->getNumPagesNeedingVisit()
        );
    }

    public function testPageVisited2() {
        $tbl = new Search_Table_VisitedPage();
        $tbl->addPage(new URL('http://yacoby.silgrad.com'), -1); //note minus
        $this->assertEquals(
            1,
            $tbl->getNumPagesNeedingVisit()
        );
    }

}

?>
