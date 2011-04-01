<?php
class WiwilandMwTest extends PageTest {

    public function __construct() {
        parent::__construct('morromods.wiwiland.net');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article181',
                'Name'   => 'Adventurer Wakim\'s',
                'Author' => 'Ethaniel'
            ),
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article994',
                'Name'   => 'Remplacement d\'Almalexia',
                'Author' => 'Westly, cam de Not Quite Dead.',
            ),
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article643',
                'Name'   => 'Chute d\'eau',
                'Author' => 'de Heremod Production - Camembérisé par Zunder',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://morromods.wiwiland.net/spip.php?article255',
            'http://morromods.wiwiland.net/spip.php?article0'
        );

        $invalid = array(
            'http://morromods.wiwiland.net/spip.php?article',
            'http://morromods.wiwiland.net/spip.php?article0x',
            'http://morromods.wiwiland.net/spip.php?nx0',
        );
        $this->helpTestModUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://morromods.wiwiland.net/spip.php?article979',
            'http://morromods.wiwiland.net/spip.php?article255',
        );
        $this->helpPageHasLinks(
            new Search_Url("http://morromods.wiwiland.net/spip.php?page=classemois"),
            $links
        );
    }
}