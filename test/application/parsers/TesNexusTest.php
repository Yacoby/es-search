<?php


class TesNexusTest extends PageTest {

    public function __construct() {
        parent::__construct('www.tesnexus.com');
    }

	public function testGetPageTest_HasLinks() {
		$url = new Search_Url('http://www.tesnexus.com/downloads/recent.php');
		$page = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());
		$links = $page->links();
		$this->assertTrue(count($links) > 10);
	}

	public function testGetPageTest_NoMods() {
		$url = new Search_Url('http://www.tesnexus.com/downloads/recent.php');
		$page = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());
		$this->assertEquals(0, count($page->mods()));
	}

	public function testLogin() {
		$url = new Search_Url('http://www.tesnexus.com/downloads/file.php?id=15802');
		$site = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());

		$mod = $site->mod(0);

		$this->assertEquals(
                new Search_Unicode('HG EyeCandy Body'),
				$mod['Name']
		);
	}

	public function testInvalidPage() {
		$url = new Search_Url('http://www.tesnexus.com/downloads/file.php?id=15268');
		$this->setExpectedException('Search_Parser_Exception_InvalidPage');
		$site = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());

	}

    public function testModRemoved(){
        $this->helpModRemovedPage(
                new Search_Url('http://www.tesnexus.com/downloads/file.php?id=1')
        );
        $this->helpModRemovedPage(
            new Search_Url('http://www.tesnexus.com/downloads/file.php?id=9999999')
        );
    }

    public function testMods() {
        $details = array(
            array(
                'Url'      => 'http://www.tesnexus.com/downloads/file.php?id=22938',
                'Name'     => 'Pursuit Enhanced',
                'Category' => 'Gameplay Effects and Changes',
                'Game'     => 'MW',
                'Author'   => 'Yacoby',
                'Version'  => '1.2.4',
            ),
            array(
                'Url'      => 'http://www.tesnexus.com/downloads/file.php?id=1647',
                'Name'     => 'Vivec NPC',
                'Category' => 'NPCs',
                'Game'     => 'MW',
                'Author'   => 'Arakhor',
            ),
            array(
                'Url'    => 'http://www.tesnexus.com/downloads/file.php?id=982',
                'Name'   => 'Clean wooden longbow(s) v 2.01',
                'Game'   => 'MW',
                'Author' => 'Unknown',
            ),
            array(
                'Url'    => 'http://www.tesnexus.com/downloads/file.php?id=24160',
                'Name'   => 'Early Music Addon',
                'Game'   => 'OB',
                'Author' => 'lord_equinox',
            ),
            array(
                'Url'    => 'http://www.tesnexus.com/downloads/file.php?id=13512',
                'Name'   => 'UFF Archers Desire',
                'Game'   => 'OB',
                'Author' => 'Petrovich',
            ),
            array(
                'Url'    => 'http://www.tesnexus.com/downloads/file.php?id=8623',
                'Name'   => 'The Sword of Cyrodiil',
                'Game'   => 'OB',
                'Author' => 'Jip2',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
                'http://www.tesnexus.com/downloads/file.php?id=23065',
                'http://www.tesnexus.com/downloads/file.php?id=23'
        );

        $invalid = array(
                'http://www.tesnexus.com/downloads/file.php?id=d',
                'http://www.tesnexus.com/downloads/file.php?id=',
                'http://www.tesnexus.com/downloads'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://www.tesnexus.com/downloads/cat.php?id=15',
        );
        $this->helpPageHasLinks(
            new Search_Url("http://www.tesnexus.com/downloads/file.php?id=23065"),
            $links
        );
    }

}
