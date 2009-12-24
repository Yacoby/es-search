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
//Search_Parser_Factory::getInstance()->register("www.tesnexus.com", "tesnexus_com");

/**
 */
final class tesnexus_com extends Search_Parser_Site {

    public static function getHost() {
        return 'www.tesnexus.com';
    }


    public function getPage(URL $url) {

        if ( !$this->isUpdatePage($url) ) {
            //echo "Mod Page: $url\n";
            return parent::getPage($url);
        }else{
            //echo "Not Page: $url\n";
        }

        $cls = get_class($this)."_page";
        assert(class_exists($cls));

        $i = new Search_HTTP_Client();
        $result = $i->getWebpage($url);

        if ( $result->getStatus() != 200 ) {
            throw new Exception("Site status must be 200");
        }

        $body = $result->getBody();
        $obj = new $cls($url, $body);

        return $obj;
    }

    protected function needsLogin(Search_Parser_Page $p){
        return $p->isValidModPage();
    }

    protected function login(Search_HTTP_Client $ig) {

        //$ig->disableCache();

        $ig->getWebpage(
                new URL('http://www.tesnexus.com/modules/login/index.php?redirect=/'),
                'GET',
                false
        );

        //redirect=%2Findex.php&user=ES_Search&pass=SearchBot&submit=Login
        $ig->addPostParameter('redirect', '%2F');
        $ig->addPostParameter('user', 'ES_Search');
        $ig->addPostParameter('pass', 'SearchBot');
        $ig->addPostParameter('submit', 'Login');

        //Referer: http://www.tesnexus.com/modules/login/index.php?redirect=/index.php
        $ig->setHeader(
                'Referer',
                'http://www.tesnexus.com/modules/login/index.php?redirect=/'
        );

        $req = $ig->getWebpage(
                new URL('http://www.tesnexus.com/modules/login/do_login.php'),
                'POST',
                false
        );


    }

    /**
     * Maximum Usage Per day
     */
    public function getLimitBytes() {
        return 1048578*25;
    }

    /**
     * Gets the page used to update the mod.
     */
    public function getUpdatePage() {
        return array(
                "URL" => array(new URL("http://www.tesnexus.com/downloads/recent.php") ),
                "UpdateF" => 12
        );
    }

    public function isUpdatePage(URL $url) {
        $up = $this->getUpdatePage();
        foreach ($up['URL'] as $u) {
            if ( $u == $url ) {
                return true;
            }
        }
        return false;
    }

    public function getInitialPages() {
        return array(
                "http://www.tesnexus.com/downloads/categories.php"
        );
    }


}

/**
 * Parses the update page using regex as SimpleHTML uses up far to much memory
 * doing it.
 */
final class tesnexus_com_page extends Search_Parser_Page {

    protected function getLoginStateFromHTML(){
        $links = $this->_html->find('#menu li a span');

        assert(count($links));

        foreach ( $links as $text ){
            if ( trim($text->innertext) == 'LOGOUT' ) {
                return true;
            }
        }
        return false;
    }

    public function __construct(URL $url, $html) {
        if ( $html instanceof Search_Parser_Dom ) {
            parent::__construct($url, $html);
        }else {
            $this->_url = $url;
            $this->parseUpdateLinks($html);
        }
    }

    protected function parseUpdateLinks($html) {
        preg_match_all ('/<a[^>]+href="([^"]+)"[^"]*>/is',$html, $matches);
        $matches = $matches[1];

        foreach ($matches as $m) {
            $url = new URL($m, $this->_url);

            if ( $this->isValidModPage($url)) {
                $this->_links[] = new URL($m, $this->_url);
            }

        }

    }

    protected function doIsValidModPage($url) {
        return (preg_match("%http://www\\.tesnexus\\.com/downloads/file\\.php\\?id=\\d+%", $url->toString()) == 1 );
    }

    protected function doIsValidPage($url) {
        $pages = array(
                "http://www\\.tesnexus\\.com/downloads/cat\\.php\\?id=\\d+",
                "http://www\\.tesnexus\\.com/downloads/cat\\.php\\?id=\\d+&page=\\d+&orderby=name&order=ASC"
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doParseModPage() {
        return $this->useModParseHelper();
    }

    function getGame() {
        $find = $this->_html->find("div[id=left_side] h3 a");
        if ( count($find) === 0 ) {
            return null;
        }
        $find = $find[0];

        switch(trim($find->plaintext)){
            case 'Morrowind':   return 'MW';
            case 'Oblivion':    return 'OB';
        }
        return null;
    }

    function getCategory() {
        return $this->_html->find("div[id=left_side] h3 a",1)->plaintext;
    }


    function getName() {
        return $this->_html->find("div[id=left_side] h2", 0)->plaintext;
    }

    function getAuthor() {
        $a = $this->getFileInfo("Author");
        if ( $a === null ) {
            return $this->getFileInfo("Uploader");
        }
        return $a;
    }

    function getDescription() {
        //work out modid
        $html = preg_match("%.*tesnexus\\.com/downloads/file\\.php\\?id=([0-9]+)%i", $this->_url->toString(), $regs);
        $id = $regs[1];

        if ( !is_numeric($id) ) {
            throw new Exception("ID is not numeric");
        }

        //get the url of the descrition
        $newURL = new URL("http://www.tesnexus.com/downloads/file/description.php?id=".$id);

        //get description
        $http = new Search_HTTP_Client();
        $str = $http->getWebpage($newURL)->getBody();
        $id = strripos($str, "</h3>");

        $str = substr($str, $id + strlen("</h3>") );
        $html = new simple_html_dom();
        $html->load($str);

        $text = self::getDescriptionText($html->innertext);

        $html->clear();
        unset($html);

        return $text;
    }

    function getVersion() {
        return $this->getFileInfo("Version");
    }

    /**********************************************************************
    * Misc Function
    **********************************************************************/
    function getFileInfo($name) {
        foreach ( $this->_html->find(".files_info") as $fi ) {
            if ( trim($fi->find("div", 0)->plaintext) == $name ) {
                $v = substr(trim(($fi->plaintext)), strlen($name));
                $v = trim(html_entity_decode($v));
                $v =  $this->_stripNonAscii($v);
                return trim($v);
            }
        }
        return null;
    }

}
