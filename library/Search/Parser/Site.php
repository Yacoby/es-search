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
 * Superclass for a site, all sites should inherit from this class, else they
 * will not get picked up by the factory and will not implement all the required
 * functionality
 *
 * All sites implmenetations should go in the library/Search/Site directory
 */
class Search_Parser_Site extends Search_Parser_Source_Abstract {

    public function getLimitBytes() {
        return $this->getOption('limitBytes');
    }
    /**
     * This function returns the host this site works with. This is used
     * in the Search_Parser_Factory class to register the classes correctly.
     *
     * @return string The host that this site supports or null if it doesn't
     */
    public function getHost() {
        return $this->getOption('host');
    }
    /**
     * The domain of the site. This is usally constructed by just prefixing
     * the host by http://
     *
     * @return string
     */
    public function getDomain() {
        if ( $this->getOption('domain') == null ) {
            $host = $this->getHost();
            if ( $host == null ) {
                throw new Exception('Host was null so couldn\'t compute domain');
            }
            return "http://{$host}";
        }
        return $this->getOption('host');
    }

    /**
     * This should be overridden by inheriting classes to return the GET prefix
     * common to all mods. The return value should be constant. This doesn't
     * refer to the pages that the mods are parsed from but the pages that the
     * user is directed to (The actual mod location)
     *
     * @return string
     */
    public function getModUrlPrefix() {
        return $this->getOption('modUrlPrefix');
    }

    /**
     * This returns the frequency that the update pages are parsed in days. There
     * is no guaretee that it will be parsed exactly in this time periord, but it
     * should be very close. (Minutes rather than hours)
     *
     * @return int|float
     */
    public function getUpdateFrequency() {
        return $this->getOption('updateFrequency');
    }
    /**
     * Gets the pages that should be checked every now and again that lists the
     * updated mods. This is guarenteed to be parsed about once every UpdateFrequency
     * so doesn't have to refer to an update page as such. This shouldn't be overwridden
     * but the values returned in this should be defined in _getUpdateDetails()
     *
     * @return array
     */
    public function getUpdatePages() {
        return $this->convertUrlSuffixes($this->getOption('updateUrl'));
    }
    /**
     * Gets the pages that should be used as a seed for finding the mods that
     * have already been added so won't be found in the update pages.
     *
     * These could be parsed more than once, and the current implementation re-adds
     * them every month or so.
     *
     * @return array
     */
    public function getInitialPages() {
        return $this->convertUrlSuffixes($this->getOption('initialPages'));
    }
    /**
     * Takes an array of url suffixes, merges them with the domain, wraps them
     * in a Search_Url and returns the new array
     *
     * @param array $suffixes
     * @return array
     */
    private function convertUrlSuffixes(array $suffixes) {
        $urls = array();
        foreach ( $suffixes as $urlSuffix ) {
            $urls[] = new Search_Url($this->getDomain().$urlSuffix);
        }
        return $urls;
    }


    protected function needsLogin(Search_Parser_Page $p) {
        return $this->getOption('loginRequired');
    }

    /**
     * If this returns true, then login is called and the current page is re
     * downloaded
     *
     * @param Search_Parser_Page $p
     * @return bool
     */
    public function isLoggedIn(Search_Parser_Page $p) {
        return $p->isLoggedIn();
    }

    /**
     * Called before the webpage is requested
     * @param Search_HTTP_Client $ig
     */
    public function login(Search_HTTP_Client $ig) {
    }


}