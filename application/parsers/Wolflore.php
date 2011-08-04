<?php

final class WolflorePage extends Search_Parser_Site_Page {

	protected function doIsValidModPage($url) {
        $rx = '/http://www\.wolflore\.net/viewtopic\.php\?f=\d+&t=\d+/';
		return preg_match($rx, $url) == 1;
	}

	protected  function doIsValidPage($url) {
		return false;
	}

    public function login($client){
        //get the cookies for the login page
        $loginPage = 'http://www.wolflore.net/ucp.php?mode=login';
        $client->request(new Search_Url($loginPage))
               ->method('GET')
               ->cacheOutput(false)
               ->exec();

        //send the request for the login page
        $client->request( new Search_Url($loginPage) )
               ->addPostParameter('redirect', './ucp.php?mode=login')
               ->addPostParameter('autologin', 'on')
               ->addPostParameter('username', 'ES_Search')
               ->addPostParameter('password', 'SearchBot1')
               ->addPostParameter('submit', 'Login')
               ->setHeader('Referer', $loginPage)
               ->method('POST')
               ->cacheOutput(false)
               ->exec();
    }

    public function getLoginStateFromHTML(){
        $xp = '//div[@class="content"]/h2/text()';
        $content = $this->getResponse()->html()->xpathOne($xp);

        $notLoggedIn = 'The board requires you to be registered and logged in to view this forum.' 
        return (string)$content != $notLoggedIn;
    }

	public function getGame() {
		return "MW";
	}

    private function getRawName(){
        $html = $this->getResponse()->html();
        return trim((string) $this->xpathOne('//h2/a/text()'));
    }

	public function getName() {
        if ( !$this->hasDownloads() ){
            return null;
        }

        $name = $this->getRawName();
        $patterns = array(
            '/^\[WIPz?\]/i',
            '/^\[RELz?\]/i',
            '/^\w+\'s/i',
            '/by [\w\s\d]+$/i',
        );
        return trim(preg_replace($patterns, '', $name));
	}

    public function hasDownloads(){
        //the count must exceed 0
        $xp = '(//div[@class="postbody"])[1]//div[@class="inline-attachment" or @class="attachbox"]';
        return count($this->getResponse()->xpath($xp)) > 0;
    }

	public function getAuthor() {
        $xp = '(//li[@class="icon-home"])[1]/a[last()]/text()';
        $forumName = $this->getResponse()->html()->xpathOne($xp);

        if ( preg_match('([\d\w\s]+)\'s Mods', $forumName, $matches) ){
            return $matches[1];
        }

        $name = $this->getRawName();
        for ( array('/^(\w+)\'s/i', '/by ([\w\s\d]+)$/i') as $re ){
            if ( preg_match($re, $name, $matches) ){
                return $matches[1];
            }
        }

		return "Unknown";
	}

	public function getDescription() {
        $xp = '(//div[@class="postbody"])[1]/div[@class="content"]/text()';
        return trim((string) $this->xpathOne($xp));
	}

}
