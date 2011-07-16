<?php

class Search_Table_ModsTest extends PHPUnit_Framework_TestCase {
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

    private $_sources;
    
    public function setUp() {
        $this->_mods      = new Search_Table_Mods();
        $this->_locations = new Search_Table_Locations();
        $this->_sources   = new Search_Table_ModSources();
    }

    //fails as no connection
    public function tearDown() {
        //order is important due to db constraints
        //ok. Maybe not now? Test it.
        $this->_locations->createQuery()->delete()->execute();
        $this->_mods->createQuery()->delete()->execute();
        $this->_sources->createQuery()->delete()->execute();
    }

    public function testAddOrUpdateModFromArray() {

    }

    public function testFindOneBy() {
        $new = $this->_mods->create();
        $new->name   = 'yacoby';
        $new->author = 'someone';
        $new->save();

        $this->assertNotEquals(false, $this->_mods->findOneByName(array('yacoby')));
    }

    public function testFindMatch1() {

        $mod1 = $this->_mods->create();
        $mod1->name   = 'mod1';
        $mod1->author = 'yacoby';
        $mod1->save();

        $sor1 = $this->_sources->create();
        $sor1->url_prefix = 'http://example.com?id=';
        $sor1->save();

        $loc1 = $this->_locations->create();
        $loc1->modification_id = $mod1->id;
        $loc1->mod_source_id   = $sor1->id;
        $loc1->category_id     = 1;
        $loc1->url_suffix      = 'mod1';
        $loc1->save();

        //this should match as the url is the same as the mods url
        $this->assertNotEquals(
                null,
                $this->_mods->findMatch(
                    'mod1',
                    'no_match',
                    new Search_Url('http://example.com?id=mod1')
                )
        );

        //this should match as the mods details are the same
        $this->assertNotEquals(
                null,
                $this->_mods->findMatch(
                    'mod1',
                    'yacoby',
                    new Search_Url('http://example.com?id=no_match')
                )
        );

        //this shouldn't match, as there are no simalarities
        $this->assertEquals(
                null,
                $this->_mods->findMatch(
                    'mod1',
                    'no_match',
                    new Search_Url('http://example.com?id=no_match')
                )
        );


    }
}
