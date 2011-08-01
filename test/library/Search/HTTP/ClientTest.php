<?php 

class Search_HTTP_ClientTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_HTTP_Client
     */
    protected $_client;
    protected $_rawClient, $_jar;

    protected function setUp() {
        $this->_rawClient = $this->getMock('Zend_Http_Client');
        $this->_jar       = $this->getMock('Search_Table_CookieJar',
                                           array(), array(), '', false);

    }

    public function testGetWebpage() {
        $url = new Search_Url('http://www.bbc.co.uk/');

        $this->_jar->expects($this->once())
                ->method('getCookies')
                ->will($this->returnValue(array()));


        $client = new Search_HTTP_Client(
                null,
                $this->_jar
        );

        $result = $client->request($url)->withCache(false)->exec();
        $this->assertEquals(200, $result->getStatus());

        $index = stripos($result->getBody(), "BBC");
        $this->assertTrue($index!==false);
    }


}
?>
