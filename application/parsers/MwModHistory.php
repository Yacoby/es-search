<?php

final class ModdingHistoryPage extends Search_Parser_Site_Page {

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
        $str = preg_replace('/([\w\d\-\(\)])[_|\+]([\w\d\-\(\)])/', '$1 $2', $str);


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
        $str = preg_replace('/([a-z])([A-Z][a-z])/', '$1 $2', $str);

        return trim($str);
    }

    /**
     * This will return unkonwn on a blank author. This is so that
     * we index more mods, as it seems that Unkown is the default for other
     * mods on the site if there is no author
     *
     * @return string
     */
    public function getAuthor() {
        $mr = $this->_html->find('.mainrow');
        foreach ( $mr as $r ) {
            if ( trim($r->find('td',0)->plaintext) == "Author:" ) {
                $a = trim($r->find('td',1)->plaintext);
                return $a == '' ? 'Unknown' : $a;
            }
        }
        return 'Unknown';
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
        if ( $mr == null ){
            return '';
        }

        $n = count($mr->children());
        return $mr->children($n-2)->plaintext;
    }


}

