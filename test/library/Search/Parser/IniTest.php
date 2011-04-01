<?php

class Search_Parser_IniTest extends PHPUnit_Framework_TestCase {


    protected $_ini1 = "[a section]\n abc = true\n alpha:xyz = n";
    protected $_ini2 = "[a section]\n alpha:xyz = m";

    protected $_ini3 = "[new section:a section]\n";


    public function testParse() {
        $o = new Search_Parser_Ini();
        $o->parse($this->_ini1);

        $this->assertEquals(1, count($o->sections()));

        $as = $o->section('a section');

        $this->assertEquals('1', $as->abc);
        $this->assertEquals('n', $as->alpha->xyz);
    }

    public function testMerge() {
        $o = new Search_Parser_Ini();
        $o->parse($this->_ini1);
        $o->merge($this->_ini2);

        $this->assertEquals(1, count($o->sections()));
        
        $as = $o->section('a section');
        $this->assertEquals('1', $as->abc);
        $this->assertEquals('m', $as->alpha->xyz);
    }

    public function testInheritance(){
        $o = new Search_Parser_Ini();
        $o->parse($this->_ini1);
        $o->merge($this->_ini3);

        $as = $o->section('new section');

        $this->assertEquals('n', $as->alpha->xyz);
    }

}
?>
