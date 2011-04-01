<?php
class ElricMTest extends PageTest {

    public function __construct() {
        parent::__construct('www.elricm.com');
    }

    public function testMods() {
        $details = array(
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21&min=10&orderby=titleA&show=10'
        );

        $invalid = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=mymod',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=21m',
            'http://yacoby.silgrad.com/MW/Mods/xyzhtm',
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28&orderby=dateA'
        );
        $this->helpTestModUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28&min=10&orderby=titleA&show=10'
        );
        $this->helpPageHasLinks(
            new Search_Url("http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewsdownload&sid=28"),
            $links
        );
    }

}