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
final class elricm_com extends Search_Parser_Site {

    public static function getHost() {
        return 'www.elricm.com';
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
        $up = array(
                new URL("http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=4"),
                new URL("http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=8"),
                new URL("http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=7"),
        );

        return array(
                "URL" => $up,
                "UpdateF" => 31
        );
    }



    public function getInitialPages() {
        return array(
                "http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=4",
                "http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=8",
                "http://www.elricm.com/nuke/html/modules.php?op=modload&name=Downloads&file=index&req=viewdownload&cid=7",
        );
    }


}

final class elricm_com_page extends Search_Parser_Page {

    protected function doIsValidModPage($url) {
        $pages = array(
                "http://www\\.elricm\\.com/nuke/html/modules\\.php\\?op=modload&name=Downloads&file=index&req=viewsdownload&sid=\\d+",
                "http://www\\.elricm\\.com/nuke/html/modules\\.php\\?op=modload&name=Downloads&file=index&req=viewsdownload&sid=\\d+&min=\\d+&orderby=titleA&show=10"
        );
        return $this->isAnyMatch($pages, $url);

    }

    protected function doIsValidPage($url) {
        return $this->doIsValidModPage($url);
    }

    protected function doParseModPage() {
        $html = $this->_html;

        $hdsec = $html->find("div[style=text-align:center] span.pn-title",0);
        if ( $hdsec == null ) {
            return; //failed to find correct section
        }

        preg_match("%Main / (.*) / (.*)%", $hdsec->plaintext, $regs);
        $cat = $regs[2];

        $game;
        if ( stripos($regs[1], "Morrowind") !== false ) {
            $game = "MW";
        }else if ( stripos($regs[1], "Oblivion") !== false ) {
            $game = "OB";
        }else if ( stripos($regs[1], "Affiliates") !== false ) {
            $game = "UN";
            $cat = ""; //not a cat
        }else {
            return ; //failed
        }

        $modSection = $html->find(".module", 0)->children(1)->find("span[class=pn-normal]", 0);

        foreach ( $modSection->find(".pn-title") as $elem ) {
            $mod = array();
            $mod['Name'] = $elem->plaintext;

            $mod['Game'] = $game;
            $mod['Category'] = $cat;

            while ( $elem = $elem->next_sibling() ) {

                if ( $elem->tag != "span" && $elem->tag != "a" ) {
                    continue;
                }
                $text = trim($elem->plaintext);

                if ( preg_match("%^Description: (.*)%", $text, $regs) ) {
                    $mod['Description'] = self::getDescriptionText($regs[1]);
                }elseif ( preg_match("%^Author: (.*)%", $text, $regs) ) {
                    $mod['Author'] = $regs[1];
                }elseif ( preg_match("%^File Version: ([0-9\\.]+) | File size: .*%", $text, $regs) ) {
                    if ( count($regs) >= 2 ) {
                        $mod['Version'] = $regs[1];
                    }
                }elseif ( $text == "Details") {
                    $mod['URL'] = new URL(html_entity_decode($elem->href), $this->_url);
                    break;
                }
            }
            $this->_mods[] = $mod;
        }

    }

}
