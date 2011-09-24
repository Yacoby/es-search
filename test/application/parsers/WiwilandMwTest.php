<?php
class WiwilandMwTest extends PageTest {

    public function __construct() {
        parent::__construct('morromods.wiwiland.net');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article181',
                'Name'   => html_entity_decode('Adventurer Wakim&#8217;s', ENT_QUOTES, 'UTF-8'),
                'Author' => 'Ethaniel'
            ),
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article994',
                'Name'   => html_entity_decode('Remplacement d&#8217;Almalexia', ENT_QUOTES, 'UTF-8'),
                'Author' => 'Westly, cam de Not Quite Dead.',
            ),
            array(
                'Url'    => 'http://morromods.wiwiland.net/spip.php?article643',
                'Name'   => html_entity_decode('Chute d&#8217;eau', ENT_QUOTES, 'UTF-8'),
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
