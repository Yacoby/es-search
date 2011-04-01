<?php
class ShsForumsTest extends PageTest {

    public function __construct() {
        parent::__construct('www.shsforums.net');
    }

    public function testStrip1() {
        $url = new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=60');
        $p   = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);
        $url = $p->stripFromLinks($url);
        $this->assertTrue(
                $p->isValidPage($url)
        );

    }

    public function testStrip2() {
        $t = new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=52');
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($t)->getPage($t);


        $l   = 'http://www.shsforums.net/index.php?s=847ed92ad1c25dd7bd9f9106b0fe7ee5&amp;automodule=downloads&showcat=3';
        $url = new URL(html_entity_decode($l), $t);

        $url = $p->stripFromLinks($url);

        $this->assertTrue(
                $p->isValidPage($url)
        );

    }

    public function testMods() {
        $details = array(
            array(
                'Url'  => 'http://www.shsforums.net/index.php?autocom=downloads&showfile=568',
                'Name' => 'White Stallion Lodge replacement',
                'Author' => 'Spirited Treasure',
                'Category' => 'Buildings and Factions',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showfile=568'
        );

        $invalid = array(
                'http://www.shsforums.net/index.php?autocom=downloads',
                'http://www.shsforums.net/index.php?autocom=downloads&showfile=568&x',
        );
        $this->helpTestModUrls($valid, $invalid);
    }

    public function testUrls(){
        $valid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=51',
                'http://www.shsforums.net/index.php?automodule=downloads&showcat=3',
        );

        $invalid = array(
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=mymod',
                'http://www.shsforums.net/index.php?autocom=downloads&showcat=51x',
        );
        $this->helpTestUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
                'http://www.shsforums.net/index.php?automodule=downloads&showcat=3'
        );
        $this->helpPageHasLinks(
                new URL('http://www.shsforums.net/index.php?autocom=downloads&showcat=52'),
                $links
        );
    }

}