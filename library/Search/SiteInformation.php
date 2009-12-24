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

/**
 * One unix time month. Not september, april, june, november or feb ;)
 */
define('MONTH', 60*60*24*31);

/**
 * This is a class called by a cronjob about once a day. It syncronises the
 * files and the database to ensure any changes or new files take effect
 */
final class Search_SiteInformation {

    /**
     * @var Search_Table_Website
     */
    private $_website;
    /**
     * @var Search_Table_VisitedPage
     */
    private $_visitedPage;
    /**
     * An array of site hosts
     * @var array
     */
    private $_sites = array();

    public function  __construct(
            Search_Table_Website $ws = null,
            Search_Table_VisitedPage $vp = null
    ) {
        if ( !$ws ) {
            $ws = new Search_Table_Website();
        }
        $this->_website = $ws;

        if ( !$vp ) {
            $vp = new Search_Table_VisitedPage();
        }
        $this->_visitedPage = $vp;


        $pf = Search_Parser_Factory::getInstance();
        $siteData = $pf->_sites();

        $sites = array();
        foreach ( $siteData as $k => $v ) {
            $this->_sites[$k] = $pf->getSiteByHost($k);
        }
    }

    /**
     * Checks all the sites that are stored as files exist on the db
     */
    public function ensureParsersCreated() {
        foreach ( $this->_sites as $host => $site ) {
            if ( !$this->_website->hasSite($host) ) {
                $this->_website->addSite($host);
            }
        }
    }


    /**
     * Copies the byte limits from the files to the db
     */
    public function ensureByteLimitsCorrect() {
        foreach ( $this->_sites as $host => $site ) {
            $this->_website->setByteLimit($host, $site->getLimitBytes());
        }
    }

    /**
     * copy all visited page data accross and ensures that if they were
     * visited 3 months ago, they are flagged as needing a revist
     */
    public function copyInitialPages() {
        foreach ( $this->_sites as $host => $site ) {
            $pages = $site->getInitialPages();

            foreach ($pages as $p) {
                $url = new URL($p);

                if ( !$this->_visitedPage->hasPage(new URL($url)) ) {
                    $this->_visitedPage->addPage($url);
                }else {
                    $pageDetails = $this->_visitedPage->getPage($url);
                    if ( $pageDetails->LastVisited < time()-(MONTH*3) ) {
                        $this->_visitedPage->setNeedVisit($url);
                    }
                }

            }
        }
    }

}