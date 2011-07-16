<?php

class Search_Sync_SiteTest extends PHPUnit_Framework_TestCase {
        private $_sites;
        private $_pages;
        private $_sources;
        /**
         *
         * @var Search_Sync_Site
         */
        private $_sync;

    public function setUp() {

        $string = <<<'INI'
[site]
    
[example.com:site]
    implementation                = true
    option:source:initialPages[]  = "/something.php"
    option:source:modUrlPrefix    = '/prefix'
    
    page:class             = "NOT_DEFINED"
INI;

        $ini = new Search_Parser_Ini($string);

        $this->_factory   = new Search_Parser_Factory();
        $this->_factory->setIni($ini);
        
        $this->assertEquals(1, count($this->_factory->getHosts()));
        
        $this->_sites     = new Search_Table_ByteLimitedSources();
        $this->_pages     = new Search_Table_Pages();
        $this->_sources   = new Search_Table_ModSources();

        $this->_sync = new Search_Sync_Site($this->_factory,
                                            $this->_sources,
                                            $this->_sites,
                                            $this->_pages);
    }

    public function tearDown() {
        $this->_sources->createQuery()->delete()->execute();
        $this->_sites->createQuery()->delete()->execute();
        $this->_pages->createQuery()->delete()->execute();
    }

    public function testEnsureSitesCreated(){
        $this->_sync->ensureSitesCreated();

        $result = $this->_sites->findOneByHost('example.com');
        $this->assertNotEquals(false, $result);
    }

    public function testEnsureModSourcesCreated(){
        $this->_sync->ensureSitesCreated();
        $this->_sync->ensureModSourcesCreated();
        
        $host = $this->_sites->findOneByHost('example.com'); 
        $result = $this->_sources->findOneById($host->mod_source_id);
        $this->assertNotEquals(false, $result);
        $this->assertEquals('http://example.com/prefix', $result->url_prefix);
    }

    public function copyInitalPages() {
        $this->_sync->ensureSitesCreated();
        $this->_sync->copyInitalPages();
        
        $host = $this->_sites->findOneByHost('example.com');
        
        $result = $this->_pages->findOneBySiteId($host->id);
        $this->assertNotEquals(false, $result);
        $this->assertEquals('/something.php', $result->url_suffix);
    }
}
