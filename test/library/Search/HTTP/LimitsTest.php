<?php

/**
 * This isn't tested much as it is a basic wrapper arround Search_Table_ModSites
 * and therefore doesn't contain much code
 */
class Search_HTTP_LimitsTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_Table_Sites
     */
    private $_sites;
    /**
     *
     * @var Search_HTTP_Limits
     */
    private $_limits;


    protected function setUp() {
        $this->_sites = $this->getMock(
                'Search_Table_ByteLimitedSources',
                array(),
                array(),
                '',
                false);

        $this->_limits = new Search_HTTP_Limits($this->_sites);
    }


    /**
     * If BytesUsed is over ByteLimit, then we shouldn't be able to get a page
     */
    public function testCanGetPage1() {
        $this->_sites->expects($this->once())
                ->method('__call')
                ->will($this->returnValue((object)array('BytesUsed' => 20, 'ByteLimit' => 10)));

        $this->_limits = new Search_HTTP_Limits($this->_sites);

        $this->assertFalse(
                $this->_limits->canGetPage(new Search_Url('http://example.com'))
        );
    }
    /**
     * If BytesUsed is under ByteLimit, then we should be able to get a page
     */
    public function testCanGetPage2() {
        $this->_sites->expects($this->once())
                ->method('__call')
                ->will($this->returnValue((object)array('BytesUsed' => 10, 'ByteLimit' => 20)));

        $this->assertTrue(
                $this->_limits->canGetPage(new Search_Url('http://example.com'))
        );
    }

    /**
     * If BytesUsed is the same as ByteLimit, then we shouldn't be able to get a page
     */
    public function testCanGetPage3() {
        $this->_sites->expects($this->once())
                ->method('__call')
                ->will($this->returnValue((object)array('BytesUsed' => 10, 'ByteLimit' => 10)));

        $this->assertFalse(
                $this->_limits->canGetPage(new Search_Url('http://example.com'))
        );
    }

}


?>

