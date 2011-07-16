<?php

class Page2Test extends PHPUnit_Framework_TestCase {

    public function testGetDescriptionText() {
        $txt = Search_Parser_Site_Page::getDescriptionText('hello<br />the<br>end');
        $this->assertEquals("hello\nthe\nend", $txt);
    }
}
?>
