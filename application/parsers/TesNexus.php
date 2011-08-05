<?php 
/**
 * Parses the update page using regex as SimpleHTML uses up far to much memory
 * doing it.
 */
final class TesNexusPage extends Search_Parser_Site_Page {

    public function __construct($response) {
        parent::__construct($response);
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

    public function isLoggedIn() {
        $html = $this->getResponse()->html();
        $links = $html->xpath('//*[@id="menu"]//li//a//span/text()');

        foreach ( $links as $text ) {
            if ( trim($text) == 'LOGOUT' ) {
                return true;
            }
        }
        return false;
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
        $html = $this->getResponse()->html();
        $elems = $html->xpath('//*[@id="topbar"]');
        return count($elems) > 0;
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
        $text = $this->getResponse()->text();
        return stripos($text,
                       "<script>window.location='/includes/error.php?pop=0&report=0&error=file_exist") === 0;
    }

    function getGame() {
        $html = $this->getResponse()->html();
        $find = $html->xpath("//div[@id='left_side']//h3//a/text()");
        if ( count($find) === 0 ) {
            return null;
        }
        $find = $find[0];

        switch(trim($find)) {
            case 'Morrowind':   return 'MW';
            case 'Oblivion':    return 'OB';
        }
        return null;
    }

    function getCategory() {
        $html = $this->getResponse()->html();
        $cat = (string)$html->xpathOne('(//div[@id="left_side"]//h3//a)[2]/text()');
        $cat = str_replace("\n", ' ', $cat);
        return $cat;
    }

    function getName() {
        $html = $this->getResponse()->html();
        return (string)$html->xpathOne('//div[@id="left_side"]//h2/text()');
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
        $str = $client->request($newURL)->exec()->simpleHtmlDom();
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
        $html = $this->getResponse()->simpleHtmlDom();
        foreach ( $html->find(".info_box .info") as $fi ) {
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
