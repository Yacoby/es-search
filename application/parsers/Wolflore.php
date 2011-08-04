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

        $notLoggedIn = 'The board requires you to be registered and logged in to view this forum.';
        return (string)$content != $notLoggedIn;
    }

    /**
     * Inspects the breadcrumb looking for game information
     */
	public function getGame() {
        //this doesn't work as expected for some reason?
        $xp = '(//li[@class="icon-home"])[1]/a/text()';
        $crumbs = $this->getResponse()->html()->xpath($xp);
        foreach ( $crumbs as $crumb ){
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
        return trim((string) $this->xpathOne('//h2/a/text()'));
    }

    /**
     * Checks that the thread we are looking at has downloads. If it doesn't
     * we should ignore it
     */
    public function hasDownloads(){
        //the count must exceed 0
        $xp = '(//div[@class="postbody"])[1]//div[@class="inline-attachment" or @class="attachbox"]';
        return count($this->getResponse()->html()->xpath($xp)) > 0;
    }

    /**
     * Gets the raw name of the thread and tries to strip out some common things
     */
	public function getName() {
        if ( !$this->hasDownloads() ){
            return null;
        }

        $name = $this->getRawName();
        $patterns = array(
            '/^\[WIPz?\]/i',
            '/^\[RELz?\]/i',
            '/by [\w\s\d]+$/i',
        );
        return trim(preg_replace($patterns, '', $name));
	}


    /**
     * This attempts to guess the author, we can't use the author of the post
     * as in many cases mods are posted by a random user
     */
	public function getAuthor() {
        $xp = '(//li[@class="icon-home"])[1]/a[last()]/text()';
        $forumName = $this->getResponse()->html()->xpathOne($xp);

        if ( preg_match('([\d\w\s]+)\'s Mods', $forumName, $matches) ){
            return $matches[1];
        }

        $name = $this->getRawName();
        foreach ( array('/by ([\w\s\d]+)$/i') as $re ){
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

