<?php
/**
 * This doesn't test enough edge cases
 */
class Search_VersionTest extends PHPUnit_Framework_TestCase {

    public function testAll(){
        $this->assertLessThan(
                Search_Version::fromString('0020'),
                Search_Version::fromString('0019')
        );

        $this->assertLessThan(
                Search_Version::fromString('1.53'),
                Search_Version::fromString('1.52beta3')
        );

       //clearly, this should be less. In the current implementation, it fails
       $this->assertLessThan(
                Search_Version::fromString('1.3'),
                Search_Version::fromString('1beta3')
        );
    }

    public function testResultsNumefromStringric(){
        $this->assertLessThan(
                Search_Version::fromString('1.1'),
                Search_Version::fromString('1')
        );

        $this->assertLessThan(
                Search_Version::fromString('2.5'),
                Search_Version::fromString('1.5')
        );


         $this->assertLessThan(
                Search_Version::fromString('2.5rc'),
                Search_Version::fromString('1.5rc')
        );
    }
    /*
    public function testResultsStrings(){
        $this->assertLessThan(
                Search_Version::fromString('1'),
                Search_Version::fromString('1a') 
        );

        $this->assertLessThan(
                Search_Version::fromString('1b'),
                Search_Version::fromString('1a')
        );
        
        $this->assertLessThan(
                Search_Version::fromString('1rc'),
                Search_Version::fromString('1b')
        );
    }
     */
    
    public function testSpaceEqulivence() {
        $this->assertEquals(
                Search_Version::fromString('1 rc'),
                Search_Version::fromString('1rc')
        );
        $this->assertEquals(
                Search_Version::fromString('1 1 rc'),
                Search_Version::fromString('11rc')
        );
    }

    public function testOrderEqulivence(){
        $this->assertEquals(
                Search_Version::fromString('1 rc'),
                Search_Version::fromString('rc 1')
        );
    }

    public function testNameEqulivence(){
        $this->assertEquals(
                Search_Version::fromString('1 rc'),
                Search_Version::fromString('1 r')
        );
        $this->assertEquals(
                Search_Version::fromString('1 beta'),
                Search_Version::fromString('1 b')
        );

        $this->assertEquals(
                Search_Version::fromString('1 alpha'),
                Search_Version::fromString('1 a')
        );

         $this->assertEquals(
                Search_Version::fromString('1 dev'),
                Search_Version::fromString('1 d')
        );
    }

    public function testPrecidence(){
        $this->assertEquals(
                Search_Version::fromString('1 rc'),
                Search_Version::fromString('alpha 1 rc')
        );
    }
}