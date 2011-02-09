<?php
/* l-b
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
 * l-b */



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


class Search_Updater extends Search_Observable {
    /**
     *
     * @var Search_Table_Sites
     */
    private $_sites;

    /**
     *
     * @var Search_Table_Mods
     */
    private $_mods;

    /**
     *
     * @var Search_Table_Locations
     */
    private $_locations;

    /**
     *
     * @var Search_Table_Pages 
     */
    private $_pages;


    public function __construct(
            Search_Table_Sites $ws         = null,
            Search_Table_Pages $pages      = null,
            Search_Table_Mods $mods        = null,
            Search_Table_Locations $locs   = null
            ) {
        $this->_sites     = $ws    ? $ws    : new Search_Table_Sites();
        $this->_pages     = $pages ? $pages : new Search_Table_Pages();
        $this->_mods      = $mods  ? $mods  : new Search_Table_Mods();
        $this->_locations = $locs  ? $locs  : new Search_Table_Locations();
    }

    /**
     * Gets a site to update, that it is possible to update (e.g. not over any
     * byte limits)
     * 
     * @return string|null The hostname, or null if no site needs to be updated
     */
    private function getUpdateSite() {
        $row = $this->_sites->findOneByUpdateRequired();
        return $row !== false ? $row->host : null;
    }

    /**
     * Trys to update an update page, if it finds to update, it retures true
     *
     * @return bool True if it managed to find a page to update and update it
     */
    public function attemptUpdatePage(Search_Parser_Factory $parserFactory) {
        $host = $this->getUpdateSite();
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
            return false;
        }

        //ensure the next time to update is now set with the correct frequency
        $this->setSiteUpdated($host, $site->getUpdateFrequency());

        foreach ( $pages as $url ) {
            $page = $site->getPage($url);
            $this->addPageData($page);
        }
        return true;
    }

    /**
     * For unit testing
     *
     * @return bool
     */
    public function hasUpdateUpdate() {
        return $this->getUpdateSite() !== null;
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
            return false;
        }
        $url = new Search_Url($page->Site->base_url . $page->url_suffix);

        Search_Logger::info("Updating Page: {$url}");

        //flag it as updated before we have a (large) chance for errors to occur
        //$this->_vp->setPageVisited($url);
        $page->revisit      = 0;
        $page->last_visited = time();
        $page->save();

        try{
            $page = $factory->getSiteByURL($url)
                            ->getPage($url);
            /*
             * TODO We are using an exception when it isn't exceptional. Not good
             */
        }catch(Search_Parser_Exception_ModRemoved $e){
            $this->_locations->deleteByUrl($url);
            return true; //Really? It was a success I suppose
        }

        $this->addPageData($page);

        return true;
    }

    public function addPageData(Search_Parser_Page $page) {
        if ( $page->isValidModPage() ) {
            //  update mod(s)
            foreach ( $page->mods() as $mod ) {
                $this->addOrUpdateMod($mod, $page->getURL());
            }
        }
        //update all links
        foreach ( $page->links() as $link ) {
            $this->addOrUpdateLink($link, $page->isValidModPage($link));
        }
    }

    /**
     *
     * @param array $modArray
     * @param Search_Url $url
     */
    private function addOrUpdateMod(array $modArray, Search_Url $url) {
        Search_Logger::info("Found Mod: {$modArray['Name']}");

        //merge mod with default values
        $defualts = array(
                'Version'     => '',
                'Category'    => '',
                'Description' => '',
                'Url'         => $url
        );
        $modArray = array_merge($defualts, $modArray);
        
        //there is a transaction in this function, so we don't need one here
        $this->_mods->addOrUpdateModFromArray($this->_sites, $modArray);

        Search_Logger::info("Added Mod: {$modArray['Name']}");
          
    }


    public function addOrUpdateLink(Search_Url $url, $modPage = false) {
        //I don't like this being here. It adds a large query overhead compared
        //to if it was taken out of the function. However, consider
        //the fact that a link from some site may link to another site. It is a
        //corner case, but it *may* happen.
        //
        //plus, this is only called in the updating, so it isn't a huge huge issue
        //I don't think...
        $site = $this->_sites->findOneByHost($url->getHost());

        $this->_pages->getConnection()->beginTransaction();
        
        $page = $this->_pages->findOneByUrl($url);
         //If it doesn't exist
        if ( $page === false ) {
            $page = $this->_pages->createByUrl($site, $url);
            $page->site_id = $site->id;
            $page->revisit = time();
            $page->save();
        }else { //else if it doesn't need updating
            if ( $page->revisit == 0 ){
                $updateDays = $modPage ? UPDATE_MOD_PAGE : UPDATE_NORMAL_PAGE;
                $page->revisit = time() + $updateDays*60*60*24;
                $page->save();
            }
        }

        $this->_pages->getConnection()->commit();
    }

}
