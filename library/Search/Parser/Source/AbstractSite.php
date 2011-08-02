<?php

class Search_Parser_Source_AbstractSite extends Search_Parser_Source_Abstract{
    
    protected function getHtml(Search_Parser_HttpClient $i,
                               Search_Url $url,
                               $cache = true) {
        $result = $i->request($url)
                    ->method('GET')
                    ->cacheOutput($cache)
                    ->exec();
        if ( $result->getStatus() != 200 ) {
            throw new Exception("Site status must be 200 and wasn't when requesting {$url}");
        }
        return new Search_Parser_SimpleHtmlDom($result->getBody());
    }

    public function getPage(Search_Url $url, $client = null) {
        $cls = $this->getPageClass();
        assert(class_exists($cls));

        $i = $client ? $client : new Search_Parser_HttpClient();

        $dom = $this->getHtml($i, $url);
        $obj = new $cls($url, $dom);

        if ( $obj->isModNotFoundPage($client) ) {
            throw new Search_Parser_Exception_ModRemoved('The mod was not found');
        }else if ( !$obj->isValidPageBody($obj) ) {
            throw new Search_Parser_Exception_InvalidPage(
            "The mod page at {$url} was found to be invalid"
            );
        }

        if ( $this->hasOption('loginRequired') &&
                $this->getOption('loginRequired') &&
                !$this->isLoggedIn($obj)
        ) {
            $this->login($i);
            $dom = $this->getHost($i, $url, false);
            $obj = new $cls($url, $dom);

            if ( !$this->isLoggedIn($obj) ) {
                throw new Search_Parser_Exception_Login(
                "Failed to log in when requesting {$url}"
                );
            }
        }
        $obj->parsePage($i);
        return $obj;
    }


}

