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
/*
require_once 'PHPUnit/Framework.php';
require_once 'VisitedPage.php';

class VisitedPageTest extends PHPUnit_Framework_TestCase {
    protected $object;

    protected function setUp() {
        resetAll();
        $this->object = new VisitedPage();
    }

    public function testSetPageVisited() {
        $u = new URL("http://yacoby.silgrad.com/");
        $this->object->addPage($u);
        $this->object->setPageVisited($u);

        $r = $this->object->getPage($u);
        $this->assertEquals(0, $r['NeedRevisit']);
    }

    public function testHasPage_True() {
        $u = new URL("http://yacoby.silgrad.com/MW/");
        $this->object->addPage($u);
        $this->assertTrue($this->object->hasPage($u));
    }
    public function testHasPage_False() {
        $u = new URL("http://not.asite.com/MW/");
        $this->assertFalse($this->object->hasPage($u));
    }

    public function testGetPage_False() {
        $u = new URL("http://not.asite.com/MW/");
        $p = $this->object->getPage($u);
        $this->assertEquals(null, $p);
    }

    public function testGetPage_True() {
        $u = new URL("http://yacoby.silgrad.com/MW/");
        $this->object->addPage($u);
        $p = $this->object->getPage($u);

        $this->assertEquals($u->toString(), $p['URL']);
    }

    public function testSetNeedVisit() {

        $u = new URL("http://yacoby.silgrad.com/");
        $this->object->addPage($u);
        sleep(2);

        $r = $this->object->getPage($u);
        $this->assertTrue($r['NeedRevisit']>0);

    }
}
?>
*/