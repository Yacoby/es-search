<?php
class YacobyTest extends PageTest {

    public function __construct() {
        parent::__construct('yacoby.silgrad.com');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'  => 'http://yacoby.silgrad.com/MW/Mods/PursuitEnhanced.htm',
                'Name' => 'Pursuit Enhanced',
            ),
            array(
                'Url'  => 'http://yacoby.silgrad.com/MW/Mods/SwimmingRealism.htm',
                'Name' => 'Swimming Realism',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://yacoby.silgrad.com/MW/Mods/myMod.htm'
        );

        $invalid = array(
            'http://yacoby.silgrad.com/MW/Mods/index.htm',
            'http://yacoby.silgrad.com/MW/Mods/Files/zyx.htm',
            'http://yacoby.silgrad.com/MW/Mods/xyzhtm'
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://yacoby.silgrad.com/MW/Mods/VampireDoorsBugfix.htm'
        );
        $this->helpPageHasLinks(
            new Search_Url("http://yacoby.silgrad.com/MW/Mods/SwimmingRealism.htm"),
            $links
        );
    }

}