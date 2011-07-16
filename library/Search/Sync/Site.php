<?php

/**
 * Syncs site details from the configuration files and php files to the database
 * 
 * @todo there are still some issues in the source that need fixing
 */
class Search_Sync_Site {
    /**
     * @var Search_Parser_Factory
     */
    private $_factory;
    /**
     * @var Search_Table_Sites
     */
    private $_sites;

    /**
     * @var Search_Table_SitePages
     */
    private $_pages;

    /**
     * @var Search_Table_ModSources
     */
    private $_sources;
    
    /**
     * Constructor with optional parameters for dependcy injection. $factory
     * must not be null.
     *
     * @param Search_Parser_Factory $factory Must not be null
     * @param Search_Table_ModSources $sources
     * @param Search_Table_Sites $sites
     * @param Search_Table_SitePages $pages 
     */
    public function __construct(
            Search_Parser_Factory $factory,
            Search_Table_ModSources $sources       = null,
            Search_Table_ByteLimitedSources $sites = null,
            Search_Table_Pages $pages              = null
    ) {
        assert ( $factory );
        $this->_factory = $factory;
        $this->_sites   = $sites   ? $sites   : new Search_Table_ByteLimitedSources();
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
        foreach ( $this->_factory->getHostsByBaseType('site') as $host ) {
            $site   = $this->_factory->getSiteByHost($host);
            $dbSite = $this->_sites->findOneByHost($host);
            if ( $dbSite === false ){
                $dbSite = $this->_sites->create();
                $dbSite->bytes_last_updated = time();
            }

            $dbSite->host       = $host;
            //TODO Change this so it works with site and forum
            $dbSite->base_url   = $site->getDomain(); 
            $dbSite->url_prefix = $site->getModUrlPrefix(); //and this
            
            $dbSite->save();
        }
    }

    /**
     * Ensure that for every site there is a mod source
     */
	public function ensureModSourcesCreated(){
        foreach ( $this->_factory->getHostsByBaseType() as $host){
            $site   = $this->_factory->getSiteByHost($host);

            //if not created, create Mod Sources for each site
            $dbSite = $this->_sites->findOneByHost($host);

            //this may happen if it wasn't created due to a un supported base
            //type
            if ( $dbSite === false ){
                continue;
            }

            //check if we need to sync
            $source = null;
            if ( $dbSite->mod_source_id === null ){
                $source = $this->_sources->create();
                $source->save();
                //have to save the source before we can create a ref 
                //to it on the site
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
            $source->url_prefix = $dbSite->base_url . $dbSite->url_prefix;
            $source->save();
        }
	}

    /**
     * @todo this really shouldn't be here
     */
    public function updateByteLimits() {
        foreach ( $this->_factory->getHostsByBaseType('site') as $host){
            $site   = $this->_factory->getSiteByHost($host);
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
        //we have to cast to a string as it may return false in some cases
        return (string)substr((string)$url, $i + strlen($site->base_url));
    }

    /**
     * copies across the initial pages as listed in the site configuration
     */
    public function copyInitalPages() {
        foreach ( $this->_factory->getHostsByBaseType('site') as $host ) {
            $site   = $this->_factory->getSiteByHost($host);
            $dbSite = $this->_sites->findOneByHost($host);

            $pages = $site->getInitialPages();
            foreach ($pages as $urlPath){
                $page = $this->_pages->findOneByUrl($urlPath);
                if ( $page === false ){
                    $page               = $this->_pages->create();
                    $page->byte_limited_source_id = $dbSite->id;
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
