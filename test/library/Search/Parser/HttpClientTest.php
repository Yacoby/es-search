<?php 

class Search_Parser_HttpClientTest extends PHPUnit_Framework_TestCase {
    /**
     * @var    Search_Parser_HttpClient
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

        $this->_jar->expects($this->any())
                ->method('getCookies')
                ->will($this->returnValue(array()));

        $client = new Search_Parser_HttpClient(
                null,
                $this->_jar
        );

        $result = $client->request($url)->withCache(false)->exec();
        $this->assertEquals(200, $result->httpStatus());

        $index = stripos($result->text(), "BBC");
        $this->assertTrue($index!==false);
    }


}
?>
