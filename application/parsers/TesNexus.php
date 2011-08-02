<?php

/**
 */
final class TesNexus extends Search_Parser_Site {

    /**
     * Gets an page, but if it is an update page doesn't get a Search_Parser_SimpleHtmlDom
     * object but ensures the pageclass uses regex to parse the page
     *
     * @param Search_Url $url
     * @return string|Search_Parser_SimpleHtmlDom
     */
    public function getPage(Search_Url $url, $client = null) {
        if ( !$this->isUpdatePage($url) ) {
            //if not logged in then the page will be redirected to /adult
            //this simply logs in and tries again
            try{
                return parent::getPage($url, $client);
            }catch(Search_HTTP_Exception_Redirect $e){
                if ( $e->to() == '/adult.php' ){
                    $client = $client ? $client : new Search_Parser_HttpClient();
                    $this->login($client);
                    return parent::getPage($url, $client);
                }
                throw $e;
            }
        }

        $cls = $this->getOption('pageClass');
        assert(class_exists($cls));

        $i      = $client ? $client : new Search_Parser_HttpClient();
        $result = $i->request($url)
                    ->method('GET')
                    ->exec();

        if ( $result->getStatus() != 200 ) {
            throw new Exception("Site status must be 200");
        }

        $body = $result->getBody();
        $obj = new $cls($url, $body);

        return $obj;
    }

    protected function needsLogin(Search_Parser_Page $p) {
        return $p->isValidModPage();
    }

    public function login(Search_Parser_HttpClient $ig) {
        //get the cookies for the login page
        $ig->request(new Search_Url('http://www.tesnexus.com/modules/login/index.php?redirect=/'))
                ->method('GET')
                ->cacheOutput(false)
                ->exec();

        //send the request for the login page
        $ig->request( new Search_Url('http://www.tesnexus.com/modules/login/do_login.php') )
            //redirect=%2Findex.php&user=ES_Search&pass=SearchBot&submit=Login
           ->addPostParameter('redirect', '%2F')
           ->addPostParameter('user', 'ES_Search')
           //@TODO move password to config
           ->addPostParameter('pass', 'SearchBot')
           ->addPostParameter('submit', 'Login')
           //Referer: http://www.tesnexus.com/modules/login/index.php?redirect=/index.php
           ->setHeader(
                'Referer',
                'http://www.tesnexus.com/modules/login/index.php?redirect=/'
           )
          ->method('POST')
          ->cacheOutput(false)
          ->exec();
    }

    public function isUpdatePage(Search_Url $url) {
        $urls = $this->getOption('updateUrl');
        foreach ($urls as $u) {
            if ( 'http://'.$this->getHost().$u == $url->toString() ) {
                return true;
            }
        }
        return false;
    }

}

/**
 * Parses the update page using regex as SimpleHTML uses up far to much memory
 * doing it.
 */
final class TesNexusPage extends Search_Parser_Site_Page {

    protected function getLoginStateFromHTML() {
        $links = $this->_html->find('#menu li a span');

        foreach ( $links as $text ) {
            if ( trim($text->innertext) == 'LOGOUT' ) {
                return true;
            }
        }
        return false;
    }

    public function __construct($url, $html) {
        if ( $html === null || $html instanceof Search_Parser_SimpleHtmlDom ) {
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
            $url = new Search_Url($m, $this->_url);

            if ( $this->isValidModPage($url)) {
                $this->addLink(new Search_Url($m, $this->_url));
            }

        }

    }

    public function isValidPageBody(){
        return count($this->_html->find('#topbar')) > 0;
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

    public function  isModNotFoundPage($client) {
        return stripos((string)$this->_html,
                       "<script>window.location='/includes/error.php?pop=0&report=0&error=file_exist") === 0;
    }

    function getGame() {
        $find = $this->_html->find("div[id=left_side] h3 a");
        if ( count($find) === 0 ) {
            return null;
        }
        $find = $find[0];

        switch(trim($find->plaintext)) {
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
        foreach ( array('Author', 'Uploader') as $key) {
            $value = $this->getFileInfo($key);
            if ( $value !== null && trim($value) != '' ) {
                return $value;
            }
        }
        return 'Unknown';
    }

    function getDescription($client) {
        //work out modid
        $html = preg_match("%.*tesnexus\\.com/downloads/file\\.php\\?id=([0-9]+)%i", $this->_url->toString(), $regs);
        $id = $regs[1];

        if ( !is_numeric($id) ) {
            throw new Exception("ID is not numeric");
        }

        //get the url of the descrition
        $newURL = new Search_Url("http://www.tesnexus.com/downloads/file/description.php?id=".$id);

        //get description
        $str = $client->request($newURL)->exec()->getBody();
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
        foreach ( $this->_html->find(".info_box .info") as $fi ) {
            if ( trim($fi->find(".stattitle", 0)->plaintext) == $name ) {
                $v = $fi->find(".stats", 0)->plaintext;
                $v = trim(html_entity_decode($v));
                $v =  $this->_stripNonAscii($v);
                return trim($v);
            }
        }
        return null;
    }

}
