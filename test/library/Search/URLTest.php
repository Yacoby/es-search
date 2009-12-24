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

class URLTest extends PHPUnit_Framework_TestCase {

    public function testConstructString() {
        $s = "http://yacoby.silgrad.com";
        $u = new URL($s);
        $this->assertEquals($s, $u->toString());
    }

    public function testConstructRel1() {
        $c = new URL("http://yacoby.silgrad.com/FS/index.html");
        $s = "../index.html";
        $u = new URL($s, $c);
        $this->assertEquals($u->toString(), "http://yacoby.silgrad.com/index.html");
    }

    public function testConstructRel2() {
        $url = new URL('../index.php', new URL('http://foobar.com/xyz/index.htm'));
        $this->assertEquals('http://foobar.com/index.php', $url->toString());
    }

    public function testIsValid() {
        $valid = array(
            'http://foo.bar.com',
            'http://www.foo.bar.com',
            'http://www.foobar.com',
            'http://foobar.com',
            'http://foobar.net',
        );

        $invalid = array(
            'httpfoo.bar.com',
            'http://',
        );

        foreach ( $valid as $v ) {
            $u = new URL($v);
            $this->assertTrue($u->isValid());
        }
        foreach ( $invalid as $i ) {
            $u = new URL($i);
            $this->assertFalse($u->isValid());
        }
    }

    public function testToString1() {
        $su = 'http://foo.bar.com/foobar';
        $u = new URL($su);
        $this->assertEquals($su, $u->toString());
    }

    public function testToString2() {
        $s = "http://www.yacoby.net";
        $u = new URL($s);
        $this->assertEquals($s, $u->toString());
    }

    public function testGetHost1() {
        $s = "http://www.yacoby.net";
        $u = new URL($s);
        $this->assertEquals($u->getHost(), "www.yacoby.net");
    }
    public function testGetHost2() {
        $s = "http://yacoby.silgrad.com";
        $u = new URL($s);
        $this->assertEquals($u->getHost(), "yacoby.silgrad.com");
    }
    
    public function testGetHost3() {
        $u = new URL('http://foo.bar.com/foobar');
        $this->assertEquals('foo.bar.com', $u->getHost());
    }

    public function testGetHost4() {
        $u = new URL('http://www.foo.bar.com');
        $this->assertEquals('www.foo.bar.com', $u->getHost());
    }

    public function testGetHost5() {
        $u = new URL('http://www.foobar.com');
        $this->assertEquals('www.foobar.com', $u->getHost());
    }

    public function testGetHost6() {
        $u = new URL('http://foobar.com');
        $this->assertEquals('foobar.com', $u->getHost());
    }
}
?>
