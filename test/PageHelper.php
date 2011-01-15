<?php /* l-b
 * This file is part of ES Search.
 * 
 * Copyright (c) 2009 Jacob Essex
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */

/**
 * Helper class for testing website pages
 */
class PageTest extends PHPUnit_Framework_TestCase {

    private $_type;
    private $_url;
    private $_factory;

    protected $_client;


    public function __construct($type, Search_Url $url) {
        $this->_factory = new Search_Parser_Factory();

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

        $this->_type = $type;
        $this->_url = $url;
    }

    public function helpTestInstance(Search_Url $url) {
        $p = $this->_factory->getSiteByURL($url)->getPage($url, $this->_client);
        $this->assertTrue($p instanceof $this->_type || $p instanceof $this->_type."_page" );
    }

    public function helpTestModUrls(array $valid, array $invalid) {
        $p = $this->_factory
                ->getSiteByURL($this->_url)
                ->getPage($this->_url, $this->_client);


        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidModPage(new Search_Url($v)));
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidModPage(new Search_Url($v)));
        }
    }

    public function helpTestUrls(array $valid, array $invalid) {
        $p = $this->_factory
                ->getSiteByURL($this->_url)
                ->getPage($this->_url, $this->_client);

        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidPage(new Search_Url($v)));
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidPage(new Search_Url($v)));
        }
    }

    public function helpTestModPage(Search_Url $url, $numMods, array $details) {
        $p = $this->_factory
                ->getSiteByURL($url)
                ->getPage($url, $this->_client);
        $this->assertTrue($p->isValidModPage());

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
    public function helpRequiredLinks(Search_Url $url, array $links) {
        if ( !$this->helpHasAnyLinkOf($url, $links) ) {
            $this->assertFalse($l1);
        }
        
    }

    public function helpHasAnyLinkOf(Search_Url $url, array $links){
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
