<?php
class PesTest extends PageTest {

    public function __construct() {
        parent::__construct('planetelderscrolls.gamespy.com');
    }

    public function testLinkStrip() {
        $factory = $this->getFactory();
        $url     = new Search_Url('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481');
        $page    = $factory->getSiteByUrl($url)
                           ->getPage($url, $this->getClient());

        $url1 = new Search_Url('http://planetelderscrolls.gamespy.com/View.php?category_show_all=1&persist_search=57384ddaa04c4881ff66ab8312ea6d27&view=Mods.List&Data_page=2');
        $url2 = new Search_Url('http://planetelderscrolls.gamespy.com/View.php?category_show_all=1&view=Mods.List&Data_page=2');

        $this->assertEquals(
            $url2->toString(),
            $page->preAddLink($url1)->toString()
        );
    }

    public function testMods() {
        $details = array(
            array(
                'Url'       => 'http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481',
                'Name'      => 'Pursuit Enhanced',
                'Author'    => 'Yacoby',
                'Version'   => '1.2.4',
                'Category'  => 'Tweaks',
                'Game'      => 'MW'
            ),
            array(
                'Url'       => 'http://planetelderscrolls.gamespy.com/View.php?view=OblivionUtilities.Detail&id=44',
                'Name'      => 'Wrye Python',
                'Author'    => 'Wrye',
                'Game'      => 'OB'
            ),
            array(
                'Url'       => 'http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=5770',
                'Name'      => 'Mod Delayers and Mod Tweaks',
                'Author'    => 'Aellis',
                'Game'      => 'OB',
                'Version'   => '1.03',
                'Category'  => 'Tweaks',
            ),
            array(
                'Url'       => 'http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=1446',
                'Name'      => 'At Home Alchemy',
                'Author'    => 'Syclonix',
                'Game'      => 'OB',
                'Version'   => '1.1',
                'Category'  => 'Alchemical',
            ),
            array(
                'Url'       => 'http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.Detail&id=5792',
                'Name'      => 'Hand to hand spell casting animations',
                'Author'    => 'Resurrection',
                'Game'      => 'OB',
                'Category'  => 'Miscellaneous',
            ),
        );
        $this->helpTestMods($details);
    }

    public function testModUrls() {
        $valid = array(
            'http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=4481',
        );
        $invalid = array(
            'http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=mymod',
        );

        $this->helpTestModUrls($valid, $invalid);
    }

    public function testModRemoved(){
        $this->helpModRemovedPage(
                new Search_Url('http://planetelderscrolls.gamespy.com/View.php?view=Mods.Detail&id=100000')
        );
        $this->helpModRemovedPage(
            new Search_Url('http://planetelderscrolls.gamespy.com/View.php?view=OblivionUtilities.Detail&id=44000')
        );
    }

}