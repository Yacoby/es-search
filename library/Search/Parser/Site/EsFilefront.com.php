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
class EsFilefront extends Search_Parser_Site {

    protected $_details = array(
        'host'            => 'elderscrolls.filefront.com',
        'domain'          => null,
        'modUrlPrefix'    => '/file/',
        'initialPages'    => array(
            '/files/The_Elder_Scrolls_III_Morrowind/Modifications;7095',
            '/files/The_Elder_Scrolls_IV_Oblivion/Modifications;7128',
        ),
        'updateUrl'       => array(),
        'updateFrequency' => 31,
        'loginRequired'   => false,
        'limitBytes'      => 1048578,
    );

}

class EsFilefront_page extends Search_Parser_Page {
    protected function doIsValidModPage($url) {
        return (preg_match("%http://elderscrolls\\.filefront\\.com/file/.*;\\d+$%", $url->toString()) == 1 );
    }

    protected function doIsValidPage($url) {
        $startMW = 'http://elderscrolls\\.filefront\\.com/files/The_Elder_Scrolls_III_Morrowind/Modifications/.*/;\\d+';
        $startOB = 'http://elderscrolls\\.filefront\\.com/files/The_Elder_Scrolls_IV_Oblivion/Modifications/.*;\\d+';
        $end     = '\\?start=\\d+&sort=name&name_direction=asc&limit=\\d+&descriptions_in=0&summary_in=0&show_screenshot_in=0#files';

        $pages = array(
            $startOB,
            $startOB.$end,
            $startMW,
            $startMW.$end,
        );

        return $this->isAnyMatch($pages, $url);
    }



    function getGame() {
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = html_entity_decode($b->plaintext);
            if ( preg_match('%Downloads > Modifications%', $txt) == 1 ||
                 preg_match('%Downloads > .*:$%', $txt) == 1 ) {
                if ( stripos($txt, 'Morrowind') !== false ){
                    return 'MW';
                }else if ( stripos($txt, 'Oblivion') !== false ){
                    return 'OB';
                }
            }
        }
        return null;
    }

    function getCategory() {
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = html_entity_decode($b->plaintext);
            if ( preg_match('%Downloads > Modifications > ([0-9a-zA-Z ]*):$%', $txt, $regs) == 1 ||
                 preg_match('%Downloads > (.*):$%', $txt, $regs) == 1 ) {
                return trim($regs[1]);
            }
        }
        return '';
    }

    function getVersion() {
        $regex = '%^[\x20-\x7e]* \(([\.0-9\(\)a-zA-Z]+)\) - File Description$%U';
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = $b->plaintext;
            if ( preg_match($regex, $txt, $regs) == 1 ) {
                return ltrim(
                        str_replace(array('(', ')'), '', $regs[1]),
                        'vV'
                );
            }
        }
        return '';
    }

    function getName() {
        $regex = '%^([\x20-\x7e]*)' //the mod name
               . '(\([\.0-9\(\)a-zA-Z]+\))?' //maybe a version string in brackets
               . '[ ]*'
               . '- File Description$%U';
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = $b->plaintext;
            if ( preg_match($regex, $txt, $regs) == 1 ) {
                return trim($regs[1]);
            }
        }
        return null;
    }

    function getDescription() {
        foreach ( $this->_html->find("tr td") as $t  ) {
            $txt = trim($t->plaintext);

            $i1 = stripos($txt, "Description:");
            $i2 = stripos($txt, "E-Mail to Friend");
            $l = strlen("Description:");
            if ( $i1 !== false && $i2 !== false ) {
                $txt = substr($txt, $i1+$l, $i2 - $l);
                $i2 = stripos($txt, "E-Mail to Friend");
                return substr($txt, 0, $i2);
            }
        }
        return null;
    }

    function getAuthor() {
        foreach ( $this->_html->find("b a") as $b  ) {
            $txt = $b->plaintext;
            $url = $b->href;
            if ( preg_match("%/developer/[0-9a-zA-Z _]*;\\d+%", $url) == 1 ) {
                return $txt;
            }
        }
        return null;
    }

}

