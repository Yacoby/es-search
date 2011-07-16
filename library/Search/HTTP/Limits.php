<?php

/**
 * Facade(?) for Search_Table_ModSites, but only exposes the limit related data
 *
 * Hangover from an older version impelemted to maintain compatibility. Worth
 * considering if this should be removed all together
 */
class Search_HTTP_Limits {

    /**
     * @var Search_Table_ModSites
     */
    private $_sites;

    /**
     * @param Search_Table_ModSites|null $website The class for handling the
     *                                            site data
     */
    function __construct(Search_Table_ByteLimitedSources $website = null) {
        $this->_sites = $website ? $website : new Search_Table_ByteLimitedSources();
    }

    /**
     * Updates limits, tries to use as few queries as possible
     * @deprecated
     */
    public function updateAllLimits() {
        assert(false);
    }


    /**
     * @param Search_Url $url
     * @return bool
     */
    public function hasLimits(Search_Url $url) {
        assert($url->isValid());
        return $this->_sites->hasSite($url->getHost());
    }

    /**
     *
     * @param Search_Url $url
     * @return array
     */
    /*
    public function getLimits(Search_Url $url) {
        assert($url->isValid());

        $site = $this->_sites->getByHost($url->getHost());
        return $site->ByteLimit;
    }
     */

    /**
     * Checks if there is any bytes left to request a page.
     * This doesn't check if the page will go over that limit (as we don't know
     * how big it is) so this will always lead to it slightly overreaching the
     * limit all the time.
     *
     * @param Search_Url $url The url of the page.
     * @return bool
     */
    public function canGetPage(Search_Url $url) {
        assert($url->isValid());

        $site = $this->_sites->getByHost($url->getHost());
        return $site->BytesUsed < $site->ByteLimit;
    }


    /**
     * Updates the database bytes used
     *
     * @param Search_Url $url The url of the page
     * @param int $size The size in bytes of the page.
     */
    public function addRequesedPage(Search_Url $url, $size) {
        assert($url->isValid());
        assert((int)$size == $size);

        $site = $this->_sites->findOneByHost($url->getHost());
        $site->bytes_used += (int)$size;
        $site->save();
    }

}
