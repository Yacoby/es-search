<?php 

class Search_HTTP_ClientTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_HTTP_Client
     */
    protected $_client;
    protected $_rawClient, $_limits, $_jar;

    protected function setUp() {
        $this->_rawClient = $this->getMock('Zend_Http_Client');
        $this->_limits    = $this->getMock('Search_HTTP_Limits', array(),
                                           array(), '', false);
        $this->_jar       = $this->getMock('Search_Table_CookieJar',
                                           array(), array(), '', false);

    }


    public function testCanGetWebpage() {
        //debug call, it must return true
        $this->_limits->expects($this->any())
                ->method('hasLimits')
                ->will($this->returnValue(true));

        $url = new Search_Url('http://example.com');
        $this->_limits->expects($this->once())
                ->method('canGetPage')
                ->with($this->equalTo($url))
                ->will($this->returnValue(true));

        $client = new Search_HTTP_Client(
                $this->_rawClient,
                $this->_limits,
                $this->_jar
        );

        $this->assertTrue($client->canGetWebpage($url));
    }


    public function testGetWebpage() {
        $url = new Search_Url('http://www.bbc.co.uk/');
        /*
        $this->_limits->expects($this->once())
                ->method('canGetPage')
                ->with($this->equalTo($url))
                ->will($this->returnValue(true));
         */

        $this->_jar->expects($this->once())
                ->method('getCookies')
                ->will($this->returnValue(array()));


        $client = new Search_HTTP_Client(
                null,
                $this->_limits,
                $this->_jar
        );

        $result = $client->request($url)->withCache(false)->exec();
        $this->assertEquals(200, $result->getStatus());

        $index = stripos($result->getBody(), "BBC");
        $this->assertTrue($index!==false);
    }


}
?>
