<?php

/**
 * Helper class for testing website pages
 */
class PageTest extends PHPUnit_Framework_TestCase {

    private $_factory;

    protected $_client;

    private $_host;

    protected function getFactory(){
        return $this->_factory;
    }

    protected function getClient(){
        return $this->_client;
    }

    protected function getEmptyPage(){
        $cls = $this->_factory
                    ->getSiteByHost($this->_host)
                    ->getOption('pageClass');
        return new $cls(null, null);
    }


    public function __construct($host) {
        $this->_factory = new Search_Parser_Factory(APPLICATION_PATH . '/parsers/defaults.ini',
                                                    APPLICATION_PATH . '/parsers/parsers.ini');

        $limits = $this->getMock('Search_HTTP_Limits', array(), array(), '', false);
        $limits->expects($this->any())
                ->method('hasLimits')
                ->will($this->returnValue(true));
        $limits->expects($this->any())
                ->method('canGetPage')
                ->will($this->returnValue(true));
        $this->_client = new Search_HTTP_Client(null,
                                                $limits,
                                                Search_HTTP_CookieJar_Memory::getInstance());

        $this->_host = $host;
    }

    public function helpTestModUrls(array $valid, array $invalid) {
        $p = $this->getEmptyPage();

        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidModPage(new Search_Url($v)),
                              "Failed matching {$v}");
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidModPage(new Search_Url($v)),
                               "Matched {$v} which should have been invalid");
        }
    }

    public function helpTestUrls(array $valid, array $invalid) {
        $p = $this->getEmptyPage();

        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidPage(new Search_Url($v)));
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidPage(new Search_Url($v)));
        }
    }

    /**
     *
     * @param array $allDetails An array of mod detals. Each mod detail should
     *              include a mod url
     */
    public function helpTestMods(array $allDetails){
        foreach ( $allDetails as $modDetail ){
            $url  = $modDetail['Url'];
            $url  = $url instanceof Search_Url ? $url : new Search_Url($url);

            unset ( $modDetail['Url'] );
            
            $page = $this->_factory
                         ->getSiteByUrl($url)
                         ->getPage($url, $this->_client);


            if ( method_exists($page, 'isValidModPage') ){
                $this->assertTrue($page->isValidModPage(),
                                  "Failed asserting that {$url} is a valid mod page");
            }
            

            $mods = $page->mods();
            $mod  = array_shift($mods);

            foreach ( $modDetail as $key => $val ) {
                $this->assertEquals($val,
                                    $mod[$key],
                                    "Failed asserting that {$key} is equal with mod from {$url}");
            }
        }
    }

    public function helpTestModPage(Search_Url $url, $numMods, array $details) {
        $p = $this->_factory
                ->getSiteByURL($url)
                ->getPage($url, $this->_client);
        //$this->assertTrue($p->isValidModPage());

        $mods = $p->mods();
        $this->assertEquals($numMods, count($mods));
        $mod = $mods[0];

        foreach ( $details as $key => $val ) {
            $this->assertEquals($mod[$key], $val);
        }
    }

    /**
     * Checks if the url contains/has the given links.
     *
     * @param Search_Url $url
     * @param array $links
     */
    public function helpPageHasLinks(Search_Url $url, array $links) {
        $p = $this->_factory
                  ->getSiteByURL($url)
                  ->getPage($url, $this->_client);
        foreach ( $links as $link ) {
            foreach ( $p->links() as $linkOnPage ){
                if ( $linkOnPage->toString() == $link ){
                    continue 2;
                }
            }
            $this->fail("The page {$url} did not contain the link {$link}");
        }
    }

    public function helpPageHasAnyLinkOf(Search_Url $url, array $links){
        $p = $this->_factory
                  ->getSiteByURL($url)
                  ->getPage($url, $this->_client);
        foreach ( $links as $l1 ) {
            foreach ($p->links() as $l2) {
                if ( $l2->toString() == $l1) {
                    return true;
                }
            }
        }
        return false;
    }

    public function helpModRemovedPage(Search_Url $url){
        $this->setExpectedException('Search_Parser_Exception_ModRemoved');
        $p = $this->_factory
                  ->getSiteByURL($url)
                  ->getPage($url, $this->_client);
    }

}

?>
