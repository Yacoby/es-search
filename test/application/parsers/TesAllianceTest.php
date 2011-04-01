<?php
class TesAllianceTest extends PageTest {

    public function __construct() {
        parent::__construct('tesalliance.org');
    }


    public function testMods() {
        $details = array(
            array(
                'Url'       => 'http://tesalliance.org/forums/index.php?/files/file/785-alternative-beginnings/',
                'Name'      => 'Alternative Beginnings',
                'Author'    => 'Arthmoor',
                'Category'  => 'Game Mechanics',
                'Game'      => 'OB',
            ),
            array(
                'Url'       => 'http://tesalliance.org/forums/index.php?/files/file/434-a-vivec-hideout/',
                'Name'      => 'A Vivec hideout',
                'Author'    => 'Pushkatu',
                'Category'  => 'Houses',
                'Game'      => 'MW',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://tesalliance.org/forums/index.php?/files/file/653-rivet-city-room/',
            'http://tesalliance.org/forums/index.php?/files/file/817-oblivion-xp/',
        );

        $invalid = array(
            'http://tesalliance.org/forums/index.php?/files/category/70-fallout-3/',
            'http://tesalliance.org/forums/index.php?/files/category/79-dungeons/',
            'http://tesalliance.org/forums/',
            'http://tesalliance.org/forums/index.php?/topic/708-forum-rules-read-this-first-before-you-post/',
            'http://tesalliance.org/forums/index.php?app=core&module=search&do=user_activity&search_app=downloads&mid=443',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

}