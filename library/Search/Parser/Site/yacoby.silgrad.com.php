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
 * l-b */ ?>

<?php
//Search_Parser_Factory::getInstance()->register("yacoby.silgrad.com", "yacoby_silgrad_com");

/**
 */
final class yacoby_silgrad_com extends Search_Parser_Site {

    public static function getHost(){
        return 'yacoby.silgrad.com';
    }
/**
 * Maximum Usage Per day
 */
    public function getLimitBytes() {
        return 1048578;
    }

    /**
     * Gets the page used to update the mod.
     */
    public function getUpdatePage() {
        return array(
        "URL" => array(new URL("http://yacoby.silgrad.com/MW/Mods/index.htm") ),
        "UpdateF" => 31
        );
    }

    public function getInitialPages() {
        return array();
    }
}

final class yacoby_silgrad_com_page extends Search_Parser_Page {
/**
 * Gets data for checking which pages are valid
 *
 * @assert (URL("http://yacoby.silgrad.com/MW/Mods/index.htm")) == false
 * @assert (URL("http://yacoby.silgrad.com/MW/Mods/myMod.htm")) == true
 * @assert (URL("http://yacoby.silgrad.com/MW/Mods/Files/zyx.htm")) == false
 * @assert (URL("http://yacoby.silgrad.com/MW/Mods/xyzhtm")) == false
 */
    protected function doIsValidModPage($url) {
        if ( $url->toString() == "http://yacoby.silgrad.com/MW/Mods/index.htm" )
            return false;
        return (preg_match("%http://yacoby\\.silgrad\\.com/MW/Mods/\\w*\\.htm%", $url->toString()) == 1 );
    }

    protected  function doIsValidPage($url) {
        return false;
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }


    /**
     * @assert () == "MW"
     */
    function getGame() {
        return "MW";
    }
    function getName() {
        $r = $this->_html->find(".modTitle", 0);
        if ( isset($r->plaintext) )
            return $r->plaintext;
        return null;
    }
    function getAuthor() {
        return "Yacoby";
    }
    function getDescription() {
        $d = "";
        foreach ( $this->_html->find(".content p") as $p )
            $d .= $p->innertext;
        return self::getDescriptionText($d);;
    }

}

