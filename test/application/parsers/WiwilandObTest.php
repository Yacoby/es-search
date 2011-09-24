<?php
class WiwilandObTest extends PageTest {

    public function __construct() {
        parent::__construct('oblimods.wiwiland.net');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'    => 'http://oblimods.wiwiland.net/spip.php?article228',
                'Name'   => 'Compagnon Spriggan',
                'Author' => 'Drunkgoblin',
            ),
            array(
                'Url'    => 'http://oblimods.wiwiland.net/spip.php?article80',
                'Name'   => 'Son attaque puissante',
                'Author' => 'Ashkhan',
            ),
            array(
                'Url'    => 'http://oblimods.wiwiland.net/spip.php?article343',
                'Name'   => html_entity_decode('De l&#8217;eau pour le Peuple !', ENT_QUOTES, 'UTF-8'),
                'Author' => 'Khornate et Qazaaq, traduction de Mag1c Wind0w',
            ),
            array(
                'Url'  => 'http://oblimods.wiwiland.net/spip.php?article327',
                'Name' => 'Exnem EyeCandy - Nouvelles Armures féminines',
            ),
            array(
                'Url'  => 'http://oblimods.wiwiland.net/spip.php?article325',
                'Name' => 'Amulette du Nécromancien et Heaume de Verdesang',
            ),
            array(
                'Url'  => 'http://oblimods.wiwiland.net/spip.php?article315',
                'Name' => 'Races Mazkens & Auréals',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
                'http://oblimods.wiwiland.net/spip.php?article80',
                'http://oblimods.wiwiland.net/spip.php?article0'
        );

        $invalid = array(
                'http://oblimods.wiwiland.net/spip.php?article',
                'http://oblimods.wiwiland.net/spip.php?article0x',
                'http://oblimods.wiwiland.net/spip.php?nx0',
        );
        $this->helpTestModUrls($valid, $invalid);
    }


}
