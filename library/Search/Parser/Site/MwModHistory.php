<?php
/* l-b
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
 */
final class modding_history extends Search_Parser_Site {

    public static function getHost() {
        return 'modhistory.fliggerty.com';
    }
	public static function getModUrlPrefix(){
		return '/index.php?dlid=';
	}
    /**
     * Maximum Usage Per day
     */
    public function getLimitBytes() {
        return 1048578*10;
    }

    /**
     * Gets the page used to update the mod.
     */
    protected function _getUpdateDetails() {
        return array(
                'Urls'		=> array(),
                'UpdateFrequency'	=> 31
        );
    }

    protected function _getInitialPages() {
        return array(
                '/index.php?cid=5'
        );
    }

}

final class modding_history_page extends Search_Parser_Page {

    protected function doIsValidModPage($url) {
        return preg_match(
                '%^http://modhistory\.fliggerty\.com/index\.php\?dlid=\d+$%',
                $url->toString()
                )  == 1;
    }

    protected function doIsValidPage($url) {
        $pages = array(
                'http://modhistory\.fliggerty\.com/index\.php\?cid=\d+',
                'http://modhistory\.fliggerty\.com/index\.php\?cid=\d+&sortvalue=date&order=ASC&limit=\d+'
        );
        return $this->isAnyMatch($pages, $url);
    }

    /**
     *
     * @return string will always return 'MW'
     */
    public function getGame() {
        return "MW";
    }


    /**
     * This function tries to generate a better name from the input by stripping
     * off common things that mess up. TESSource style IDs for example.
     *
     *
     * @return string|null
     */
    public function getName() {
        $str = $this->_html->find("div[id=banner] div[id=catname] a", 0)->plaintext;

        if ( $str === null ) {
            return null;
        }

        //cope with tesnexus style file names
        //ID3002-2-24-Unholy+Temple+Armor-20040215 => Unholy+Temple+Armor
        //ID2514-2-26-White+Bonemold+Armor.-20031129 => White+Bonemold+Armor.
        $str = preg_replace('/ID[\d]+-[\d]+-[\d]+-(.*)-[\d]+/', '$1', $str);

        //remove _ and + from between words
        //117_Better_Bodies_Texture_Replacer_Muscular => 117 Better Bodies Texture Replacer Muscular
        $str = preg_replace('/([\w\d])[_|\+]([\w\d])/', '$1 $2', $str);


        //remove leading numbers but not if there is a letter in the number and only if there
        //is a space after
        //117 Better Bodies Texture Replacer Muscular =>  Better Bodies Texture Replacer Muscular
        $str = preg_replace('/^[\d]+ /', '', $str);

        //remove trailing numbers if it starts with '  0' (note the space)
        //nudbretf_0601 => nudbretf_
        //Clothed Muscles v1.1 => Clothed Muscles v1.1

        $str = preg_replace('/ 0([\d])*$/', '', $str);

        //add spaces before capitals, but only if there is one capital
        //BB_LeatherAndChain => BB_Leather And Chain
        $str = preg_replace('/(?<!\ )[A-Z][a-z]/', ' $0', $str);

        return trim($str);
    }

    /**
     *
     * @return string|null
     */
    public function getAuthor() {
        $mr = $this->_html->find('.mainrow');
        foreach ( $mr as $r ) {
            if ( trim($r->find('td',0)->plaintext) == "Author:" ) {
                return $r->find('td',1)->plaintext;
            }
        }
        return null;
    }

    /**
     *
     *
     * Note, this will never return null, it will just return ''
     *
     * @return string
     */
    public function getDescription() {
        $mr = $this->_html->find('.mainrow');
        foreach ( $mr as $r ) {
            if ( count($r->children()) == 1) {
                return $r->children(0)->plaintext;
            }
        }
        return '';
    }

    /**
     *
     *
     * Note, this will never return null, it will return '' as it is optional
     *
     * @return string
     */
    public function getCategory() {
        $mr = $this->_html->find('.topbg strong',0);
        if ( $mr == null )
            return '';

        $n = count($mr->children());
        return $mr->children($n-2)->plaintext;
    }


}

