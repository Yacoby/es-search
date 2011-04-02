<?php
class BethsoftForumTest extends PageTest {

    public function __construct() {
        parent::__construct('forums.bethsoft.com');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'    => 'http://forums.bethsoft.com/index.php?/topic/1173065-relz-morrowind-overhaul-sounds-graphics/',
                'Name'   => 'Morrowind Overhaul - Sounds & Graphics',
                'Author' => 'KINGPIX',
            ),
        );
        $this->helpTestMods($details);
    }
/*
    public function testModUrls() {
        $valid = array(
            'http://forums.bethsoft.com/index.php?/topic/1173065-relz-morrowind-overhaul-sounds-graphics/',
            'http://forums.bethsoft.com/index.php?/topic/1176610-rel-morrowind-rebirth-beta/',
        );

        $invalid = array(
            'http://forums.bethsoft.com/index.php?/topic/1176610-rel-morrowind-rebirth-beta/page__st__20',
        );

        $this->helpTestModUrls($valid, $invalid);
    }
*/
}