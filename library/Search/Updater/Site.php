<?php

/*Find new page on update page
    if mod page
        if has mod
            flag as needing a visit if not already needing update. Months time
        else
            flag visit now
    else
        if doesn't know about page
            visit soon
        else
            visit months time


Find new page on another page
    if mod page
        flag as needing a visit if not already needing update
    else
        if doesn't know about page
            visit soon
        else
            visit months time
*/


/**
 * The time (days) till a mod page is reparsed if a link is enountered and
 * it alreay exists in the database
 *
 * @todo move to config file maybe?
 */
define('UPDATE_MOD_PAGE', 15);

/**
 * The time (days) till a normal page is reparsed if a link is enountered and
 * it alreay exists in the database
 */
define('UPDATE_NORMAL_PAGE', 60);

/**
 * Class for updating mods from sites
 */
class Search_Updater_Site extends Search_Observable implements Search_Updater_Interface {
    /**
     * @var Search_Table_Sites
     */
    private $_sites;

    /**
     * @var Search_Table_SitePages 
     */
    private $_pages;

    /**
     * @var Search_Parser_Factory
     */
    private $_factory;

    public function __construct(
            Search_Parser_Factory $fac,
            Search_Table_ByteLimitedSources $ws = null,
            Search_Table_Pages $pages           = null
            ) {
        $this->_factory = $fac;
        $this->_sites   = $ws    ? $ws    : new Search_Table_ByteLimitedSources();
        $this->_pages   = $pages ? $pages : new Search_Table_Pages();

        //always incrase the limits
        Search_Parser_HttpClient::alwaysAttach(new Search_Observer_Limits());
    }

    public function update(){
        //this will reutnrn false if there is no site to update, otherwise
        //it will contain some(?) data that may or many not contain mods
        $pageData = $this->attemptUpdatePage($this->_factory);
        if ( $pageData !== false ){
            return $pageData;
        }
        //updates a page
        return $this->generalUpdate($this->_factory);
    }

    /**
     * Gets a site to update, that it is possible to update (e.g. not over any
     * byte limits) and the source Id for it
     * 
     */
    private function getSiteToUpdate() {
        $row = $this->_sites->findOneByUpdateRequired();
        return $row !== false ? array($row->host, $row->mod_source_id) 
                              : array(null,null);
    }

    /**
     * Trys to update an update page, if it finds to update, if it doesn't it
     * returns false
     */
    private function attemptUpdatePage(Search_Parser_Factory $parserFactory) {
        list($host, $sourceId) = $this->getSiteToUpdate();
        if ( $host === null ) {
            return false;
        }
        Search_Logger::info("Updating Host {$host}");

        //set updated. This is so that we don't get stuck on errors
        $this->setSiteUpdated($host);

        //get update page
        $site  = $parserFactory->getSiteByHost($host);
        $pages = $site->getUpdatePages();

        if ( $pages === null ) {
            print 'No pages';
            return false;
        }

        //ensure the next time to update is now set with the correct frequency
        $this->setSiteUpdated($host, $site->getUpdateFrequency());

        $data = array('Source' => $sourceId);
        foreach ( $pages as $url ) {
            $page = $site->getPage($url);
            $data = array_merge_recursive($data, $this->processPageData($page));
        }
        return $data;
    }

    /**
     * For unit testing
     *
     * @return bool
     */
    public function hasUpdateUpdate() {
        list($a, $b) = $this->getSiteToUpdate();
        return $a !== null;
    }


    /**
     * Sets a site as having been updated
     *
     * @param string $host
     * @param float|int $days The number of days to wait till another update.
     *                        Default, 1
     */
    private function setSiteUpdated($host, $days = 1) {
        if ( !is_numeric($days)) {
            throw new Exception('$days must be numeric');
        }
        $site = $this->_sites->findOneByHost($host);
        $site->next_update = time() + 60*60*24*$days;
        $site->save();
    }


    /**
     * Updates a page that needs updating
     *
     * @return bool if an update has occured
     */
    public function generalUpdate(Search_Parser_Factory $factory) {
        //  select a page needing updating
        $page = $this->_pages->findOneByUpdateRequired();

        if ( $page === false ) {
            return array();
        }
        $url = new Search_Url($page->ByteLimitedSource->base_url . $page->url_suffix);

        Search_Logger::info("Updating Page: {$url}");

        //flag it as updated before we have a (large) chance for errors to occur
        //$this->_vp->setPageVisited($url);
        $page->revisit      = 0;
        $page->last_visited = time();
        $page->save();

        try{
            $page = $factory->getSiteByHost($url->getHost())
                            ->getPage($url);
            /*
             * TODO We are using an exception when it isn't exceptional. Not good
             */
        }catch(Search_Parser_Exception_ModRemoved $e){
            return array('Deleted' => array($url));
        }

        return $this->processPageData($page);
    }

    public function processPageData(Search_Parser_Site_Page $page) {
        $site = $this->_sites->findOneByHost($page->getUrl()->getHost());

        //update all links
        foreach ( $page->links() as $link ) {
            $this->addOrUpdateLink($site, $link, $page->isValidModPage($link));
        }

        //this is a bit of a hacky fix to ensure that every mod has a url
        $mods = $page->mods();
        foreach ( $mods as &$mod ){
            if ( !array_key_exists('Url', $mod) ){
                $mod['Url'] = $page->getURL();
            }
        }

        if ( $page->isValidModPage() ) {
            return array('Source' => $site->mod_source_id, 'NewUpdated' => $mods);
        }
        return array('Source' => $site->mod_source_id);
    }


    public function addOrUpdateLink($site, Search_Url $url, $modPage = false) {
        $this->_pages->getConnection()->beginTransaction();
        
        $page = $this->_pages->findOneByUrl($url);
         //If it doesn't exist
        if ( $page === false ) {
            $page = $this->_pages->createByUrl($site, $url);
            $page->byte_limited_source_id = $site->id;
            $page->revisit = time();
            $page->save();
        }else { //else if it doesn't need updating
            if ( $page->revisit == 0 ){
                $updateDays    = $modPage ? UPDATE_MOD_PAGE : UPDATE_NORMAL_PAGE;
                $page->revisit = time() + $updateDays*60*60*24;
                $page->save();
            }
        }

        $this->_pages->getConnection()->commit();
    }

}
