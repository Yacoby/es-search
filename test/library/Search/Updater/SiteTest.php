<?php

class Search_Updater_Parser extends Search_Parser_Site{
    public function scrape(){
        return new Search_Updater_ExamplePage();
    }
}

class Search_Updater_ExamplePage extends Search_Parser_Site_Page{
    
    public function mods(){
        return array('name' => 'a name',
                     'author' => 'some author',
                     'game' => 'MW');

    }
}

class Search_Updater_SiteTest extends PHPUnit_Framework_TestCase {
    private $_sites;
    private $_pages;
    private $_sources;
    protected $_updater;

    protected function setUp() {

        $string = <<<'INI'
[site]
    
[yacoby.silgrad.com:site]
    implementation                = true
    option:initialPages[]  = "/MW/Mods/"
    option:modUrlPrefix    = '/MW/Mods/'

    option:updateFrequency = 1
    option:updateFrequency = 31
    option:limitBytes      = 100100
    
    option:host = yacoby.silgrad.com
    option:pageClass             = "Search_Updater_ExamplePage"

    class = "Search_Updater_Parser"
INI;
        
        $ini = new Search_Parser_Ini($string);

        $factory = new Search_Parser_Factory();
        $factory->setIni($ini);
        
        $this->_sites    = new Search_Table_ByteLimitedSources();
        $this->_pages    = new Search_Table_Pages();
        $this->_sources  = new Search_Table_ModSources();

        $sync            = new Search_Sync_Site($factory,
                                                $this->_sources,
                                                $this->_sites,
                                                $this->_pages);
        $sync->syncAll();

        $this->_updater = new Search_Updater_Site(
                $factory,
                $this->_sites,
                $this->_pages
        );
    }    

    public function tearDown() {
        $this->_sources->createQuery()->delete()->execute();
        $this->_sites->createQuery()->delete()->execute();
        $this->_pages->createQuery()->delete()->execute();
    }

    public function testUpdate1() {
        $this->assertEquals(1, $this->_sites->count());
        $site = $this->_sites->findOneByHost('yacoby.silgrad.com');
        $site->next_update = time() - 1;
        $site->byte_limit  = 1;
        $site->bytes_used  = 0;
        $site->save();

        $this->_updater->update();

        $site = $this->_sites->findOneByHost('yacoby.silgrad.com');
        $this->assertGreaterThan(time(), (int)$site->next_update);
    }

    public function testUpdate2() {
        $this->assertEquals(1, $this->_sites->count());
        $site = $this->_sites->findOneByHost('yacoby.silgrad.com');
        $site->next_update = strtotime('+1 day');
        $site->save();

        $this->_updater->update();

        list($page) = $this->_pages->findAll();

        $this->assertLessThanOrEqual(time(), (int)$page->last_visited);
        $this->assertGreaterThan(strtotime('-1 day'), (int)$page->last_visited);
    }
    
    public function testAddMod(){
        $this->assertEquals(1, $this->_sites->count());
        $site = $this->_sites->findOneByHost('yacoby.silgrad.com');
        $site->next_update = strtotime('+1 day');
        $site->save();

        $this->_updater->update();
    }

}
