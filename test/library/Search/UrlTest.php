<?php

class Search_UrlTest extends PHPUnit_Framework_TestCase {

    public function testConstructString() {
        $s = "http://yacoby.silgrad.com";
        $u = new Search_Url($s);
        $this->assertEquals($s, $u->toString());
    }

    public function testConstructRel1() {
        $c = new Search_Url("http://yacoby.silgrad.com/FS/index.html");
        $s = "../index.html";
        $u = new Search_Url($s, $c);
        $this->assertEquals($u->toString(), 
                            "http://yacoby.silgrad.com/index.html");
    }

    public function testConstructRel2() {
        $url = new Search_Url('../index.php',
                              new Search_Url('http://foobar.com/xyz/index.htm'));
        $this->assertEquals('http://foobar.com/index.php',
                            $url->toString());
    }

    public function testConstructRel3(){
        $url = new Search_Url('?bar',
                              new Search_Url('http://foobar.com/n/index.htm'));
        $this->assertEquals('http://foobar.com/n/index.htm?bar',
                            $url->toString());
    }

    public function testConstructRel4(){
        $url = new Search_Url('spip.php?article979',
                              new Search_Url('http://morromods.wiwiland.net/spip.php?page=classemois'));
        $this->assertEquals('http://morromods.wiwiland.net/spip.php?article979',
                            $url->toString());
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
            $u = new Search_Url($v);
            $this->assertTrue($u->isValid());
        }
        foreach ( $invalid as $i ) {
            $u = new Search_Url($i);
            $this->assertFalse($u->isValid());
        }
    }

    public function testToString1() {
        $su = 'http://foo.bar.com/foobar';
        $u = new Search_Url($su);
        $this->assertEquals($su, $u->toString());
    }

    public function testToString2() {
        $s = "http://www.yacoby.net";
        $u = new Search_Url($s);
        $this->assertEquals($s, $u->toString());
    }

    public function testGetHost1() {
        $s = "http://www.yacoby.net";
        $u = new Search_Url($s);
        $this->assertEquals($u->getHost(), "www.yacoby.net");
    }
    public function testGetHost2() {
        $s = "http://yacoby.silgrad.com";
        $u = new Search_Url($s);
        $this->assertEquals($u->getHost(), "yacoby.silgrad.com");
    }
    
    public function testGetHost3() {
        $u = new Search_Url('http://foo.bar.com/foobar');
        $this->assertEquals('foo.bar.com', $u->getHost());
    }

    public function testGetHost4() {
        $u = new Search_Url('http://www.foo.bar.com');
        $this->assertEquals('www.foo.bar.com', $u->getHost());
    }

    public function testGetHost5() {
        $u = new Search_Url('http://www.foobar.com');
        $this->assertEquals('www.foobar.com', $u->getHost());
    }

    public function testGetHost6() {
        $u = new Search_Url('http://foobar.com');
        $this->assertEquals('foobar.com', $u->getHost());
    }
}