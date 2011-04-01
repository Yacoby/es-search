<?php
class MwModHistoryTest extends PageTest {

    public function __construct() {
        parent::__construct('modhistory.fliggerty.com');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'    => 'http://modhistory.fliggerty.com/index.php?dlid=1740',
                'Name'   => 'Unholy Temple Armor',
                'Author' => 'Blackshark64'
            ),
            array(
                'Url'    => 'http://modhistory.fliggerty.com/index.php?dlid=3916',
                'Name'   => 'All my stuff 3',
                'Author' => 'Adam'
            ),
            array(
                'Url'  => 'http://modhistory.fliggerty.com/index.php?dlid=3242',
                'Name' => 'secretcave',
            ),
            array(
                'Url'  => 'http://modhistory.fliggerty.com/index.php?dlid=3296',
                'Name' => 'Martistan Castle',
            ),
            array(
                'Url'  => 'http://modhistory.fliggerty.com/index.php?dlid=1783',
                'Name' => 'Amazon (Females Only) Race v1.0',
            ),
            array(
                'Url'  => 'http://modhistory.fliggerty.com/index.php?dlid=3277',
                'Name' => 'MORIA',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://modhistory.fliggerty.com/index.php?dlid=2944',
        );

        $invalid = array(
            'http://modhistory.fliggerty.com/index.php?dlid=jd'
        );

        $this->helpTestModUrls($valid, $invalid);
    }
    public function testUrls() {
        $valid = array(
            'http://modhistory.fliggerty.com/index.php?cid=8',
            'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC&limit=30'
        );

        $invalid = array(
            'http://modhistory.fliggerty.com/index.php?cid=a8',
            'http://modhistory.fliggerty.com/index.php?cid=8a',
            'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC&limit=a',
            'http://modhistory.fliggerty.com/index.php?cid=25&sortvalue=date&order=ASC'
        );
        $this->helpTestUrls($valid, $invalid);
    }

    public function testLinks() {
        $links = array(
            'http://modhistory.fliggerty.com/index.php?cid=9',
            'http://modhistory.fliggerty.com/index.php?cid=12'
        );
        $this->helpPageHasLinks(
            new Search_Url('http://modhistory.fliggerty.com/index.php?cid=5'),
            $links
        );
    }

}