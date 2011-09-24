<?php

/**
 * Wolflore. Quite  a lot of adult mods and in many cases unique mods
 */
class WolflorePage extends Search_Parser_Site_Page {

	protected function doIsValidModPage($url) {
        $rx = '~^http://www\.wolflore\.net/viewtopic\.php\?f=\d+&t=\d+$~';
		return preg_match($rx, $url) == 1;
	}

	protected  function doIsValidPage($url) {
        $rx = '~^http://www\.wolflore\.net/viewforum\.php\?f=\d+&start=\d+$~';
		return preg_match($rx, $url) == 1;
	}

    public function login(Search_Parser_HttpClient $client){
        //get the cookies for the login page
        $loginPage = 'http://www.wolflore.net/ucp.php?mode=login';
        $result = $client->request(new Search_Url($loginPage))
                         ->method('GET')
                         ->cacheOutput(false)
                         ->exec();

        $sid = $result->html()->xpathOne('//input[@name="sid"]/@value');

        //send the request for the login page
        $client->request( new Search_Url($loginPage) )
               ->addPostParameter('redirect', './ucp.php?mode=login')
               ->addPostParameter('autologin', 'on')
               ->addPostParameter('username', 'ES_Search')
               ->addPostParameter('password', 'SearchBot1')
               ->addPostParameter('submit', 'Login')
               ->addPostParameter('login', 'Login')
               ->addPostParameter('sid', $sid->toString())
               ->setHeader('Referer', $loginPage)
               ->method('POST')
               ->cacheOutput(false)
               ->exec();
    }

    public function isLoggedIn(){
        $xp = '//title/text()';
        $content = $this->getResponse()->html()->xpathOne($xp)->toString();
        $notLoggedIn = 'Wolflore ? Login';
        return $content->getAscii() != $notLoggedIn;
    }

    /**
     * Inspects the breadcrumb looking for game information
     */
	public function getGame() {
        //this doesn't work as expected for some reason?
        $xp = '(//li[@class="icon-home"])[1]/a/text()';
        $crumbs = $this->getResponse()->html()->xpath($xp);
        foreach ( $crumbs as $crumb ){
            $crumb = $crumb->toString()->getAscii();
            if ( stripos($crumb, 'morrowind') !== false ){
                return 'MW';
            }elseif ( stripos($crumb, 'oblivion') !== false ){
                return 'OB';
            }
        }
        return null;
	}

    private function getRawName(){
        $html = $this->getResponse()->html();
        return trim($html->xpathOne('//h2/a')->normalisedString()->getAscii());
    }

    /**
     * Checks that the thread we are looking at has downloads. If it doesn't
     * we should ignore it
     */
    public function hasDownloads(){
        //the count must exceed 0
        $xp = '(//div[@class="postbody"])[1]//dl[@class="inline-attachment" or @class="attachbox"]';
        
        if ( count($this->getResponse()->html()->xpath($xp)) > 0 ){
            return true;
        }

        //now we look to see if there are any wolflore downloads.
        //this is desinged to also pickup things from wolflore.fliggerty.com
        $xp = '(//div[@class="postbody"])[1]//a[contains(@href, "wolflore.")]';

        return count($this->getResponse()->html()->xpath($xp)) > 0;

    }

    /**
     * Gets the raw name of the thread and tries to strip out some common things
     */
	public function getName() {
        $name = $this->getRawName();
        $patterns = array(
            '/^\[WIPz?\]/i',
            '/^\[RELz?\]/i',
            '/by [\w\s\d]+$/i',
        );
        return new Search_Unicode(trim(preg_replace($patterns, '', $name)));
	}

    protected function doParseModPage($client) {
        if ( $this->hasDownloads() ){
            return $this->useModParseHelper($client);
        }
        return array();
    }


    /**
     * This attempts to guess the author, we can't use the author of the post
     * as in many cases mods are posted by a random user
     */
	public function getAuthor() {
        $xp = '(//li[@class="icon-home"])[1]/a/text()';
        $crumbs = $this->getResponse()->html()->xpath($xp);

        foreach ( $crumbs as $crumb ){
            $crumb = $crumb->toString()->getAscii();
            if ( preg_match('/^([\d\w\s]+)\'s/i', trim($crumb), $matches) ){
                return new Search_Unicode($matches[1]);
            }
        }

        $name = $this->getRawName();
        foreach ( array('/by ([\w\s\d]+)$/i') as $re ){
            if ( preg_match($re, $name, $matches) ){
                return new Search_Unicode($matches[1]);
            }
        }

		return new Search_Unicode("Unknown");
	}

	public function getDescription() {
        $xp = '(//div[@class="postbody"])[1]/div[@class="content"]/text()';
        return $this->getResponse()->html()->xpathOne($xp)->toString();
	}
}

