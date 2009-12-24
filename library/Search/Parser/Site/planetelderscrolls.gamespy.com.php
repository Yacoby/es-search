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
//Search_Parser_Factory::getInstance()->register("planetelderscrolls.gamespy.com", "planetelderscrolls_com");

/**
 */
final class planetelderscrolls_com extends Search_Parser_Site {
    public static function getHost() {
        return 'planetelderscrolls.gamespy.com';
    }

    public function getLimitBytes() {
        return 1048578*50;
    }

    /**
     * Gets the page used to update the mod.
     */
    public function getUpdatePage() {
        $up = array(
                new URL("http://planetelderscrolls.gamespy.com/View.php?view=LatestAdditions&clusters=utilities&days=30&show_days_back=1"),
                new URL("http://planetelderscrolls.gamespy.com/View.php?view=RecentUpdates&clusters=utilities&days=30&show_days_back=1"),

                new URL("http://planetelderscrolls.gamespy.com/View.php?view=LatestAdditions&clusters=oblivionutilities&days=30&show_days_back=1"),
                new URL("http://planetelderscrolls.gamespy.com/View.php?view=RecentUpdates&clusters=oblivionutilities&days=30&show_days_back=1"),

                new URL("http://planetelderscrolls.gamespy.com/View.php?view=RecentUpdates&clusters=mods&days=30&show_days_back=1"),
                new URL("http://planetelderscrolls.gamespy.com/View.php?view=LatestAdditions&clusters=mods&days=300&show_days_back=1"),

                new URL("http://planetelderscrolls.gamespy.com/View.php?view=LatestAdditions&clusters=oblivionmods&days=30&show_days_back=1"),
                new URL("http://planetelderscrolls.gamespy.com/View.php?view=RecentUpdates&clusters=oblivionmods&days=30&show_days_back=1")
        );

        return array(
                "URL" => $up,
                "UpdateF" => 0.5
        );
    }

    public function getInitialPages() {
        return array(
                "http://planetelderscrolls.gamespy.com/View.php?view=OblivionMods.List",
                "http://planetelderscrolls.gamespy.com/View.php?view=Mods.List",
                "http://planetelderscrolls.gamespy.com/View.php?view=OblivionUtilities.List",
                "http://planetelderscrolls.gamespy.com/View.php?view=Utilities.List"
        );
    }


}

final class planetelderscrolls_com_page extends Search_Parser_Page {

    protected function doIsValidModPage($url) {
        $pages = array(
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=Mods\\.Detail&id=\\d+", //mw
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=OblivionMods\\.Detail&id=\\d+",//ob
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=OblivionUtilities\\.Detail&id=\\d+", //ob util
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=Utilities\\.Detail&id=\\d+" //mwutil
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        $pages = array(
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?category_show_all=1&view=OblivionMods\\.List&Data_page=\\d+",
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=OblivionMods\\.List",

                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?category_show_all=1&view=Mods\\.List&Data_page=\\d+",
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=Mods\\.List",

                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?category_show_all=1&view=OblivionUtilities\\.List&Data_page=\\d+",
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=OblivionUtilities\\.List",


                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?category_show_all=1&view=Utilities\\.List&Data_page=\\d+",
                "http://planetelderscrolls\\.gamespy\\.com/View\\.php\\?view=Utilities\\.List"

        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }

    public function stripFromLinks(URL $url) {
        return new URL(
                preg_replace('%&persist_search=[0-9a-zA-Z]+%', '', $url->toString(), 1)
        );
    }

    /**********************************************************************
    * Functions for parsing mod pages
    **********************************************************************/
    function getGame() {
        $find = $this->_html->find(".datatable_page center h1");
        if ( count($find) == 0 ) {
            return null;
        }
        $find = $find[0];

        $gs = $find->plaintext;
        if ( stripos($gs, "Morrowind") !== false ) return "MW";
        if ( stripos($gs, "Oblivion") !== false ) return "OB";

        //ob lists:
        //Oblivion Utilities
        //but mw only lists
        //utilites

        if ( stripos($gs, "UTILITIES") !== false ) return "MW";

        return null;
    }

    function getName() {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            $val = $tr->find("td a");
            if ( count($val) == 0 ) continue;

            $val = trim(htmlspecialchars_decode($val[0]->plaintext));
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));

            if ( substr($key, 0, 4) == "Name" )
                return $val;
        }
        return null;
    }
    function getAuthor() {
        return $this->findInTable("Author");
    }
    function getCategory() {
        return $this->findInTable("Category");
    }
    function getVersion() {
        $v = $this->findInTable("Version");
        return ( $v == null ? ' ' : $v );
    }
    function getDescription() {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            if ( count($tr->children()) != 1 ) {
                continue;
            }
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));
            if ( substr($key, 0, strlen("Description")) == "Description" ) {
                return self::getDescriptionText(
                    trim(
                        htmlspecialchars_decode(
                            $tr->next_sibling()->children(0)->innertext
                        )
                    )
                );
            }
        }
        return null;
    }

    function findInTable($string) {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            if ( count($tr->children()) < 2 ) {
                continue;
            }

            $val = trim(htmlspecialchars_decode($tr->children(1)->plaintext));
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));

            if ( substr($key, 0, strlen($string)) == $string )
                return $val;
        }
        return null;
    }

}
