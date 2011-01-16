<?php
/**
 * This doesn't test enough edge cases
 */
class Search_VersionTest extends PHPUnit_Framework_TestCase {

    public function testNothing(){
        $this->assertEquals(0, Search_Version::fromString(''));
    }


    public function testAll(){
        $this->assertLessThan(
                Search_Version::fromString('0020'),
                Search_Version::fromString('0019')
        );

        $this->assertLessThan(
                Search_Version::fromString('1.53'),
                Search_Version::fromString('1.52beta3')
        );

       $this->assertLessThan(
                Search_Version::fromString('1.3'),
                Search_Version::fromString('1beta3')
        );
    }

    public function testAllMore(){
         $this->assertLessThan(
                Search_Version::fromString('1.5beta3'),
                Search_Version::fromString('1.0.3')
        );

        $this->assertLessThan(
                Search_Version::fromString('1.5beta3'),
                Search_Version::fromString('1.0.0.20')
        );

        $this->assertLessThan(
                Search_Version::fromString('1beta2'),
                Search_Version::fromString('1beta1')
        );

        //This fails.
         $this->assertLessThan(
                Search_Version::fromString('1beta1'),
                Search_Version::fromString('1.0.5')
        );
    }

    public function testResultsNumericfromString(){
        $this->assertLessThan(
                Search_Version::fromString('1.1'),
                Search_Version::fromString('1')
        );

        $this->assertLessThan(
                Search_Version::fromString('2.5'),
                Search_Version::fromString('1.5')
        );


         $this->assertLessThan(
                Search_Version::fromString('2.5a'),
                Search_Version::fromString('1.5a')
        );
    }
    
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
    
    
    public function testSpaceEqulivence() {
        $this->assertEquals(
                Search_Version::fromString('1 a'),
                Search_Version::fromString('1a')
        );
        $this->assertEquals(
                Search_Version::fromString('1 1 a'),
                Search_Version::fromString('11a')
        );
    }

    public function testNameEqulivence(){
        $this->assertEquals(
                Search_Version::fromString('1 beta'),
                Search_Version::fromString('1 b')
        );

        $this->assertEquals(
                Search_Version::fromString('1 alpha'),
                Search_Version::fromString('1 a')
        );
    }
}