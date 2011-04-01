<?php

class Search_Sync {
    /**
     * @var Search_Parser_Factory
     */
    private $_factory;
    /**
     * @var Search_Table_Sites
     */
    private $_sites;

    /**
     * @var Search_Table_Pages
     */
    private $_pages;

    /**
     * @var Search_Table_ModSources
     */
    private $_sources;
    
    public function __construct(
            Search_Parser_Factory $factory,
            Search_Table_Sites $sites        = null,
            Search_Table_Pages $pages        = null,
            Search_Table_ModSources $sources = null
    ) {
        assert ( $factory );
        $this->_factory = $factory;
        $this->_sites   = $sites   ? $sites   : new Search_Table_Sites();
        $this->_pages   = $pages   ? $pages   : new Search_Table_Pages();
        $this->_sources = $sources ? $sources : new Search_Table_ModSources();
    }

    /**
     * Helper function that runs all functions in the correct order
     */
    public function syncAll() {
        $this->ensureSitesCreated();
        $this->ensureModSourcesCreated();
        $this->updateByteLimits();
        $this->copyInitalPages();
    }

    /**
     * Checks all the sites that exist as files are on the database
     */
    public function ensureSitesCreated() {
        foreach ( $this->_factory->getSites() as $host => $site) {
            $dbSite = $this->_sites->findOneByHost($host);
            if ( $dbSite === false ){
                $dbSite = $this->_sites->create();
                $dbSite->bytes_last_updated = time();
            }

            $dbSite->host           = $host;
            $dbSite->base_url       = $site->getDomain();
            $dbSite->mod_url_prefix = $site->getModUrlPrefix();

            $dbSite->save();
        }
    }

    /**
     * Ensure that for every site there is a mod source
     */
	public function ensureModSourcesCreated(){
        foreach ( $this->_factory->getSites() as $host => $site){
            //if not created, create Mod Sources for each site
            $dbSite = $this->_sites->findOneByHost($host);

            //check if we need to sync
            $source = null;
            if ( $dbSite->mod_source_id === null ){
                $source = $this->_sources->create();
                $source->save();
                //have to save the source before we can create a ref to it on the site
                //TODO FIX

                $dbSite->mod_source_id = $source->id;
                $dbSite->save();
            }else{
                $source = $this->_sources->findOneById($dbSite->mod_source_id);
                if ( $source === false ){
                    throw new Exception('Could not find Sites source');
                }
            }
            //ensure that the prefix is correct
            $source->mod_url_prefix = $dbSite->base_url . $dbSite->mod_url_prefix;
            $source->save();
        }


	}

    public function updateByteLimits() {
        foreach ( $this->_factory->getSites() as $host => $site) {
            $dbSite = $this->_sites->findOneByHost($host);
            $dbSite->byte_limit = $site->getLimitBytes();
            $dbSite->save();

        }
    }
    private function toUrlSuffix($site, Search_Url $url){
        $i = stripos((string)$url, $site->base_url);
        if ( $i === false ){
            return (string)$url;
        }
        return substr((string)$url, $i + strlen($site->base_url));
    }

    public function copyInitalPages() {
        foreach ( $this->_factory->getSites() as $host => $site ) {
            $dbSite = $this->_sites->findOneByHost($host);

            $pages = $site->getInitialPages();
            foreach ($pages as $urlPath){
                $page = $this->_pages->findOneByUrl($urlPath);
                if ( $page === false ){
                    $page               = $this->_pages->create();
                    $page->site_id      = $dbSite->id;
                    $page->url_suffix   = $this->toUrlSuffix($dbSite, $urlPath);
                    $page->last_visited = time();
                    $page->revisit      = time();
                    $page->save();
                } else if ( $page->last_visited < strtotime('+3 months') ) {
                    $page->revisit = time();
                    $page->save();
                }

            }

        }

    }

}
