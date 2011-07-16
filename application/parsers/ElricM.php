<?php

final class ElricMPage extends Search_Parser_Site_Page {

    protected function doIsValidModPage($url) {
        $re = 'http://www\.elricm\.com/nuke/html/modules\.php'
            . '\?op=modload&name=Downloads&file=index&req=viewsdownload&sid=\d+'
            . '(&min=\d+&orderby=titleA&show=10)?';
        return $this->isAnyMatch(array($re), $url);
    }

    protected function doIsValidPage($url) {
        return $this->doIsValidModPage($url);
    }

    protected function doParseModPage($client) {
        $html = $this->_html;

        $hdsec = $html->find('div[style=text-align:center] span.pn-title',0);
        if ( $hdsec == null ) {
            return; //failed to find correct section
        }

        preg_match('%Main / (.*) / (.*)%', $hdsec->plaintext, $regs);
        $cat = $regs[2];

        $game;
        if ( stripos($regs[1], 'Morrowind') !== false ) {
            $game = 'MW';
        }else if ( stripos($regs[1], 'Oblivion') !== false ) {
            $game = 'OB';
        }else if ( stripos($regs[1], 'Affiliates') !== false ) {
            return;
            //$game = 'UN';
            //$cat = ''; //not a cat
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
                }else if ( preg_match("%^Author: (.*)%", $text, $regs) ) {
                    $mod['Author'] = $regs[1];
                }else if ( preg_match("%^File Version: ([0-9\\.]+) | File size: .*%", $text, $regs) ) {
                    if ( count($regs) >= 2 ) {
                        $mod['Version'] = $regs[1];
                    }
                }else if ( $text == "Details") {
                    $mod['Url'] = new Search_Url(html_entity_decode($elem->href), $this->_url);
                    break;
                }
            }
            $this->addMod($mod);
        }

    }

}
