<?php

final class PlanetElderScrollsPage extends Search_Parser_Site_Page {

    private $_html;
    public function __construct($response){
        parent::__construct($response);
        $this->_html = $response->simpleHtmlDom();
    }

    public function isLoggedIn(){
        return $this->_html->find('input[name=age_gate]') == false;
    }

    /**
     * @todo
     * This does at least one request more than it should as 
     * due to the method logging in uses, the result is thrown away
     * and the SAME page is then got again
     */
    public function login(Search_Parser_HttpClient $client){
        $url = $this->getResponse()->url();

        $client->request($url)
               ->method('POST')
               ->addPostParameter('age_gate', '1')
               ->addPostParameter('Profile_BirthdayMonth', '1')
               ->addPostParameter('Profile_BirthdayDay', '1')
               ->addPostParameter('Profile_BirthdayDay', '1')
               ->addPostParameter('Profile_BirthdayYear', '1985')
               ->exec();
    }
    
    private $_urlSections = '(Oblivion(Mods|Utilities)|Mods|Utilities)';

    protected function doIsValidModPage($url) {
        
        $re = 'http://planetelderscrolls\.gamespy\.com/View\.php\?view='
            . $this->_urlSections
            . '\.Detail&id=\d+';
        
        return $this->isAnyMatch(array($re), $url);
    }

    protected function doIsValidPage($url) {
        
        $reCat = 'http://planetelderscrolls\.gamespy\.com/View\.php'
               . '\?category_show_all=1&view='
               . $this->_urlSections
               . '\.List&Data_page=\d+';
        $reList = 'http://planetelderscrolls\.gamespy\.com/View\.php'
                . '\?view='
                . $this->_urlSections
                . '\.List';
        return $this->isAnyMatch(array($reCat, $reList), $url);
    }


    public function preAddLink(Search_Url $url) {
        return new Search_Url(
                preg_replace('%&persist_search=[0-9a-zA-Z]+%', '', $url->toString(), 1)
        );
    }

    public function  isModNotFoundPage($client) {
        foreach ( $this->_html->find('h1') as $e ) {
            if ( $e->plaintext == 'This entry either does not exist or is not yet available.' ) {
                return true;
            }
        }
        return false;
    }

    /**********************************************************************
    * Functions for parsing mod pages
    **********************************************************************/
    public function getGame() {
        $find = $this->_html->find("#article_box_body center h1");
        if ( count($find) == 0 ) {
            return null;
        }
        $find = $find[0];

        $gs = $find->plaintext;
        if ( stripos($gs, "Morrowind") !== false ) {
            return "MW";
        }
        if ( stripos($gs, "Oblivion") !== false ) {
            return "OB";
        }

        //we have to do another check here as
        //ob lists:
        //Oblivion Utilities
        //but mw only lists
        //utilites

        if ( stripos($gs, "UTILITIES") !== false ) {
            return "MW";
        }

        return null;
    }

    public function getName() {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            $val = $tr->find("td a");
            if ( count($val) == 0 ) continue;

            $val = trim(htmlspecialchars_decode($val[0]->plaintext));
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));

            if ( substr($key, 0, 4) == "Name" )
                return $val;
        }
        return null;
    }
    public function getAuthor() {
        return $this->findInTable("Author");
    }
    public function getCategory() {
        return $this->findInTable("Category");
    }
    public function getVersion() {
        $v = $this->findInTable("Version");
        return $v === null ? '' : $v;
    }
    public function getDescription() {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            if ( count($tr->children()) != 1 ) {
                continue;
            }
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));
            if ( substr($key, 0, strlen("Description")) == "Description" ) {
                return self::getDescriptionText(
                        trim(htmlspecialchars_decode($tr->next_sibling()->children(0)->innertext)
                        )
                );
            }
        }
        return null;
    }

    protected function findInTable($string) {
        foreach ( $this->_html->find("TABLE[cellpadding=6] tr td table[cellspacing=1] tr") as $tr ) {
            if ( count($tr->children()) < 2 ) {
                continue;
            }

            $val = trim(htmlspecialchars_decode($tr->children(1)->plaintext));
            $key = trim(htmlspecialchars_decode($tr->children(0)->plaintext));

            if ( substr($key, 0, strlen($string)) == $string ) {
                return $val;
            }
        }
        return null;
    }

}
