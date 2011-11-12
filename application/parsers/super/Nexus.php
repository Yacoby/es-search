<?php 
class NexusPage extends Search_Parser_Site_Page {

    private $_nexusUrl;
    private $_nexusRegexUrl;
    private $_nexusHost;

    public function __construct($response, Search_Url $url) {
        $this->_nexusUrl = (string)$url;
        $this->_nexusRegexUrl = str_replace('.', '\.', (string)$url);
        $this->_nexusHost = $url->getHost();
        parent::__construct($response);
    }

    public function login(Search_Parser_HttpClient $ig) {
        //get the cookies for the login page
        $ig->request(new Search_Url($this->_nexusUrl . '/modules/login/index.php?redirect=/'))
                ->method('GET')
                ->cacheOutput(false)
                ->exec();

        //send the request for the login page
        $ig->request( new Search_Url($this->_nexusUrl . '/modules/login/do_login.php') )
            //redirect=%2Findex.php&user=ES_Search&pass=SearchBot&submit=Login
           ->addPostParameter('redirect', '%2F')
           ->addPostParameter('user', 'ES_Search')
           //@TODO move password to config
           ->addPostParameter('pass', 'SearchBot')
           ->addPostParameter('submit', 'Login')
           //Referer: http://www.tesnexus.com/modules/login/index.php?redirect=/index.php
           ->setHeader(
                'Referer',
                $this->_nexusUrl . '/modules/login/index.php?redirect=/'
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
        $links = $html->xpath('//div[@class="user"]//a/text()');

        foreach ( $links as $text ) {
            $text = $text->toString()->getAscii();
            if ( trim($text) == 'Log out' ) {
                return true;
            }
        }
        return false;
    }


    protected function parseUpdateLinks($html) {
        preg_match_all ('/<a[^>]+href="([^"]+)"[^"]*>/is',$html, $matches);
        $matches = $matches[1];

        foreach ($matches as $m) {
            $url = new Search_Url($m, $this->_nexusUrl);

            if ( $this->isValidModPage($url)) {
                $this->addLink(new Search_Url($m, $this->_nexusUrl));
            }

        }

    }

    public function isValidPageBody(){
        $html = $this->getResponse()->html();
        $elem = $html->xpathOne('//div[@class="header"]/h1/text()');
        $text = $elem->toString()->getAscii();
        return $text != 'Site Error';
    }

    protected function doIsValidModPage($url) {
        return (preg_match('%' . $this->_nexusRegexUrl . '/downloads/file\.php\?id=\d+%', $url->toString()) == 1 );
    }

    protected function doIsValidPage($url) {
        $pages = array(
                $this->_nexusRegexUrl . '/downloads/cat\.php\?id=\d+',
                $this->_nexusRegexUrl . '/downloads/cat\.php\?id=\d+&page=\d+&orderby=name&order=ASC',
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
        $find = $html->xpath('//div[@class="header"]//div[@class="right"]/a/text()');
        if ( count($find) === 0 ) {
            return null;
        }
        $find = $find[0]->toString()->getAscii();

        switch(trim($find)) {
            case 'Morrowind':   return 'MW';
            case 'Oblivion':    return 'OB';
            case 'Skyrim':      return 'SK';
        }
        return null;
    }

    function getCategory() {
        $html = $this->getResponse()->html();
        $find = $html->xpath('//div[@class="header"]//div[@class="right"]/a/text()');
        if ( count($find) === 0 ) {
            return null;
        }
        return $find[count($find)-1]->normalisedString();
    }

    function getName() {
        $html = $this->getResponse()->html();
        $str = $html->xpathOne('//div[@class="header"]/h1/text()')->toString();
        $str->trim();
        return $str;
    }

    function getAuthor() {
        $html = $this->getResponse()->html();
        $str = $html->xpathOne('//div[@class="header"]/h1/span/strong/text()');
        if ( $str ){
            $str = $str->toString();
            $str->trim();
            $str = $str->replace("\n", " "); //why this is needed IDK
            return $str;
        }else{
            $str = $html->xpathOne('//li[@class="uploader"]/a/text()');
            if ( $str ){
                $str = $str->toString();
                $str->trim();
                return $str;
            }
        }
        return new Search_Unicode('Unknown');
    }

    function getDescription($client) {
        //work out modid
        $html = preg_match('%.*' . $this->_nexusHost . '/downloads/file\.php\?id=([0-9]+)%i', $this->_url->toString(), $regs);
        $id = $regs[1];

        if ( !is_numeric($id) ) {
            throw new Exception("ID is not numeric");
        }

        //get the url of the descrition
        $newURL = new Search_Url($this->_nexusUrl . '/downloads/file/description.php?id=' . $id);

        //get description
        $html = $client->request($newURL)->exec()->html();
        return $html->xpathOne('//div[@class="bb-content"]/text()')->toString();
    }

    function getVersion() {
        $html = $this->getResponse()->html();
        $version = $html->xpathOne('//*[@class="file-version"]/strong/text()');
        return $version->toString()->getAscii();
    }

}
