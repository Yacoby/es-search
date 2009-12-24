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


/**
 * @todo Class isn't unit testable
 */
class Search_Updater {
    /**
     *
     * @var Search_Table_VisitedPage
     */
    private $_vp;

    /**
     *
     * @var Search_Table_Website
     */
    private $_ws;

    /**
     *
     * @var Search_Data_UnifiedModDatabase
     */
    private $_modDb;


    public function __construct(
            Search_Data_UnifiedModDatabase $mdb = null,
            Search_Table_VisitedPage $vp = null,
            Search_Table_Website $ws = null) {

        if ( !$vp ) {
            $vp = new Search_Table_VisitedPage();
        }
        $this->_vp = $vp;

        if ( !$ws ) {
            $ws = new Search_Table_Website();
        }
        $this->_ws = $ws;

        if ( !$mdb) {
            $mdb = new Search_Data_UnifiedModDatabase(
                    new Search_Data_DB_MySQL(),
                    new Search_Data_DB_Lucene()
            );
        }
        $this->_modDb = $mdb;
    }

    /**
     * Trys to update an update page, if it finds to update, it retures true
     *
     * @return bool If it managed to find an update to an update page
     */
    public function attemptUpdatePage() {
        $host = $this->getUpdateSite();
        if ( $host == null ) {
            return false;
        }

        echo "Updating $host<br />\n";

        $this->setUpdateSite($host);

        //update update page
        $site = Search_Parser_Factory::getInstance()->getSiteByHost($host);
        $updateInfo = $site->getUpdatePage();

        if ( $updateInfo == null ) {
            return false;
        }


        //ensure update frequency is now set correctly
        $this->setUpdateSite($host, $updateInfo['UpdateF']);

        foreach ( $updateInfo['URL'] as $url ) {
            $page = $site->getPage($url);
            $this->addPageData($page);
        }
        return true;
    }

    /**
     * FOr unit testing
     *
     * @return bool
     */
    public function hasUpdateUpdate() {
        return $this->getUpdateSite() != null;
    }

    /**
     * Gets a site to update
     * @return string Hostname
     */
    private function getUpdateSite() {
        $row = $this->_ws->getSiteNeedingUpdate();
        return $row ? $row->HostName : null;
    }

    /**
     * Sets a site as having been updated
     *
     * @param string $host
     * @param int $days
     */
    private function setUpdateSite($host, $days = 1) {
        if ( !is_numeric($days)) {
            throw new Exception('$days must be numeric');
        }
        $this->_ws->setSiteUpdated($host, $days);
    }


    /**
     * Updates a page
     *
     * @return bool if an update has occured
     */
    public function generalUpdate() {
        //  select a page needing updating
        $page = $this->_vp->getPageNeedingVisit();
        if ( $page == null ) {
            return false;
        }
        $url = new URL($page->URL);

        echo "Updating URL: $url<br />\n";

        //flag it as updated before we have a (large) chance for errors to occur
        $this->_vp->setPageVisited($url);

        $page = Search_Parser_Factory::getInstance()
                ->getSiteByURL($url)
                ->getPage($url);

        $this->addPageData($page);
        return true;
    }

    public function addPageData(Search_Parser_Page $page) {
        if ( $page->isValidModPage() ) {
            //  update mod(s)
            foreach ( $page->mods() as $mod ) {
                echo "Updating Mod ", $mod['Name'], "<br />\n";
                $this->modUpdate($mod, $page->getURL());
            }
        }
        //update all links
        foreach ( $page->links() as $link ) {
            echo "Adding Link $link<br />\n";
            $this->updateOrAddLink($link, $page->isValidModPage($link));
        }


    }

    /**
     * @todo array_merge plz?
     */
    private function modUpdate($mod, URL $url) {
        $notReq = array(
                'Version',
                'Category',
                'Description'
        );
        foreach ( $notReq as $nr ) {
            if ( !isset($mod[$nr]) ) {
                $mod[$nr] = "";
            }
        };

        if ( !isset($mod['URL']) ) {
            $mod['URL'] = $url;
        }

        //update details
        $this->_modDb->addMod($mod);
    }

    public function updateOrAddLink(URL $url, $modPage = false) {
        //If it doesn't exist
        if ( !$this->_vp->hasPage($url) ) {
            $this->_vp->addPage($url);//  add
        }else { //else if it doesn't need updating
            $pageDetails = $this->_vp->getPage($url);
            if ( $pageDetails->NeedRevisit == 0 ) {
                $updateDays = $modPage ? UPDATE_MOD_PAGE : UPDATE_NORMAL_PAGE;
                $this->_vp->setNeedVisit(new URL($pageDetails->URL), $updateDays);
            }
        }
    }

}
