<?php
//Search_Parser_Factory::getInstance()->register("elderscrolls.filefront.com", "elderscrolls_filefront_com");

/**
 */
class elderscrolls_filefront_com extends Search_Parser_Site {

    public static function getHost(){
        return null;//DON'T USE CLASS
    //    return 'elderscrolls.filefront.com';
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
        "URL" => array(),
        "UpdateF" => 31
        );
    }

    public function getInitialPages() {
        return array(
        "http://elderscrolls.filefront.com/files/The_Elder_Scrolls_III_Morrowind/Modifications;7095",
        "http://elderscrolls.filefront.com/files/The_Elder_Scrolls_IV_Oblivion/Modifications;7128"
        );
    }
}

class elderscrolls_filefront_com_page extends Search_Parser_Page {
    protected function doIsValidModPage($url) {
        return (preg_match("%http://elderscrolls\\.filefront\\.com/file/.*;\\d+%", $url->toString()) == 1 );
    }

    protected function doIsValidPage($url) {
        $startMW = 'http://elderscrolls\\.filefront\\.com/files/The_Elder_Scrolls_III_Morrowind/Modifications/.*/;\\d+';
        $startOB = 'http://elderscrolls\\.filefront\\.com/files/The_Elder_Scrolls_IV_Oblivion/Modifications/.*;\\d+';
        $end = '\\?start=\\d+&sort=name&name_direction=asc&limit=\\d+&descriptions_in=0&summary_in=0&show_screenshot_in=0#files';

        $pages = array(
            $startOB,
            $startOB.$end,
            $startMW,
            $startMW.$end,
        );

        return $this->isAnyMatch($pages, $url);
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }


    function getGame() {
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = html_entity_decode($b->plaintext);
            if ( preg_match("%Downloads > Modifications%", $txt) == 1 ) {
                if ( stripos($txt, "Morrowind") !== false )
                    return "MW";
                if ( stripos($txt, "Oblivion") !== false )
                    return "OB";
            }
        }
        return null;
    }

    function getCategory() {
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = html_entity_decode($b->plaintext);
            if ( preg_match("%Downloads > Modifications > ([0-9a-zA-Z ]*)%", $txt, $regs) == 1 ) {
                return $regs[1];
            }
        }
        return "";
    }

    function getName() {
        foreach ( $this->_html->find("b") as $b  ) {
            $txt = $b->plaintext;
            if ( preg_match("%([0-9a-zA-Z ]*) - File Description%", $txt, $regs) == 1 ) {
                return $regs[1];
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
            if ( preg_match("%/developer/[0-9a-zA-Z ]*;\\d+%", $url) == 1 ) {
                return $txt;
            }
        }
        return null;
    }

}

