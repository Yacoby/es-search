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
 * Class is used in testing to check that the class conforms to a set of standards
 */
class SiteValidator {
    /**
     * @var ReflectionClass
     */
    private $_reflect = null;

    /**
     *
     * @param string $class The name of the class
     */
    public function __construct($class) {
        $this->_reflect = new ReflectionClass($class);
    }

    public function hasHostDefined() {
        return $this->_reflect->hasMethod('getHost') &&
                $this->_reflect->getMethod('getHost')->isStatic() &&
                $this->_reflect->getMethod('getHost')->isPublic();
    }

    public function isFinal() {
        return $this->_reflect->isFinal();
    }

}


/**
 * Helper class for testing website pages
 */
class PageTest extends PHPUnit_Framework_TestCase {

    private $_type;
    private $_url;

    public function __construct($type, URL $url) {
        $this->_type = $type;
        $this->_url = $url;

        $sv = new SiteValidator($type);
        if ( $sv->isFinal() ) {
            assert($sv->hasHostDefined());
        }
    }

    public function helpTestInstance(URL $url) {
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);
        $this->assertTrue($p instanceof $this->_type || $p instanceof $this->_type."_page" );
    }

    public function helpTestModUrls(array $valid, array $invalid) {
        $p = Search_Parser_Factory::getInstance()->
                getSiteByURL($this->_url)->
                getPage($this->_url);


        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidModPage(new URL($v)));
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidModPage(new URL($v)));
        }
    }

    public function helpTestUrls(array $valid, array $invalid) {
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($this->_url)->getPage($this->_url);

        foreach ( $valid as $v ) {
            $this->assertTrue($p->isValidPage(new URL($v)));
        }
        foreach ( $invalid as $v ) {
            $this->assertFalse($p->isValidPage(new URL($v)));
        }
    }

    public function helpTestModPage(URL $url, $numMods, array $details) {
        $p = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url)
                ->getPage($url);
        $this->assertTrue($p->isValidModPage());

        $mods = $p->mods();
        //var_dump($mods);

        $this->assertEquals($numMods, count($mods));

        $mod = $mods[0];

        foreach ( $details as $key => $val ) {
            $this->assertEquals($val, $mod[$key]);
        }
    }

    public function helpRequiredLinks(URL $url, array $links) {
        $p = Search_Parser_Factory::getInstance()->getSiteByURL($url)->getPage($url);
        foreach ( $links as $l1 ) {
            $found = false;
            foreach ($p->links() as $l2) {
                if ( $l2->toString() == $l1) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->assertFalse($l1);
            }
        }
    }



}

?>
