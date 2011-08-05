<?php


class WolfloreTest extends PageTest {

    public function __construct() {
        parent::__construct('www.wolflore.net');
    }

	public function testGetPageTest_NoMods() {
        #screenshot thread
		$url = new Search_Url('http://www.wolflore.net/viewtopic.php?f=73&t=709');
		$page = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());
		$this->assertEquals(0, count($page->mods()));
	}

	public function testLogin() {
		$url = new Search_Url('http://www.wolflore.net/viewtopic.php?f=73&t=762');
		$site = $this->getFactory()
				->getSiteByHost($url->getHost())
				->getPage($url, $this->getClient());

		$mod = $site->mod(0);

		$this->assertEquals($mod['Name'], 'Sexy Ice Armor v1.1');
	}

    public function testMods() {
        $details = array(
            array(
                'Url'      => 'http://www.wolflore.net/viewtopic.php?f=73&t=762',
                'Name'     => 'Sexy Ice Armor v1.1',
                'Game'     => 'MW',
                'Author'   => 'Cenobite',
            ),
            array(
                'Url'      => 'http://www.wolflore.net/viewtopic.php?f=76&t=786',
                'Name'     => 'Snow Elves',
                'Game'     => 'MW',
                'Author'   => 'Slaanesh',
            ),
            array(
                'Url'    => 'http://www.wolflore.net/viewtopic.php?f=60&t=660',
                'Name'   => 'Slof\'s Snow Leopard Race',
                'Game'   => 'OB',
                'Author' => 'Slof',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
                'http://www.wolflore.net/viewtopic.php?f=60&t=660',
        );

        $invalid = array(
                'http://www.wolflore.net/viewtopic.php?f=60',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

}
