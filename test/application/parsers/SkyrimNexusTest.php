<?php


class SkyrimNexusTest extends PageTest {

    public function __construct() {
        parent::__construct('www.skyrimnexus.com');
    }

	public function testGetPageTest_HasLinks() {
		$url = new Search_Url('http://www.skyrimnexus.com/downloads/recent.php');
		$page = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());
		$links = $page->links();
		$this->assertTrue(count($links) > 10);
	}

	public function testGetPageTest_NoMods() {
		$url = new Search_Url('http://www.skyrimnexus.com/downloads/recent.php');
		$page = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());
		$this->assertEquals(0, count($page->mods()));
	}

	public function testInvalidPage() {
		$url = new Search_Url('http://www.skyrimnexus.com/downloads/file.php?id=9876543');
		$this->setExpectedException('Search_Parser_Exception_InvalidPage');
		$site = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());

	}

    public function testModRemoved(){
        $this->helpModRemovedPage(
                new Search_Url('http://www.skyrimnexus.com/downloads/file.php?id=1')
        );
        $this->helpModRemovedPage(
            new Search_Url('http://www.skyrimnexus.com/downloads/file.php?id=9999999')
        );
    }

    public function testMods() {
        $details = array(
            array(
                'Url'      => 'http://www.skyrimnexus.com/downloads/file.php?id=28',
                'Name'     => 'No Spiders',
                'Category' => 'Animals, creatures, mounts & horses',
                'Game'     => 'SK',
                'Author'   => 'Luthien Anarion',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
                'http://www.skyrimnexus.com/downloads/file.php?id=23065',
                'http://www.skyrimnexus.com/downloads/file.php?id=23'
        );

        $invalid = array(
                'http://www.skyrimnexus.com/downloads/file.php?id=d',
                'http://www.skyrimnexus.com/downloads/file.php?id=',
                'http://www.skyrimnexus.com/downloads'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://www.skyrimnexus.com/downloads/cat.php?id=83',
        );
        $this->helpPageHasLinks(
            new Search_Url("http://www.skyrimnexus.com/downloads/file.php?id=28"),
            $links
        );
    }

}
