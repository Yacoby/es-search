<?php

require_once "PageHelper.php";

class EsFilefrontTest extends PageTest {
    function __construct(){
        parent::__construct(
            'EsFilefront',
            new Search_Url('http://elderscrolls.filefront.com/file/Wizard_Hats_Eyeglasses;63064')
        );
    }
    function testInstance(){
        $this->helpTestInstance(
                new Search_Url('http://elderscrolls.filefront.com/file/Wizard_Hats_Eyeglasses;63064')
        );
    }

    function testMod1(){
        $mod = array(
            'Name'    => 'Battle Tattoos',
            'Author'  => 'Kevin P. Cook',
            'Version' => '1.0',
            'Game'    => 'OB',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Battle_Tattoos;73175'),
            1,
            $mod
        );
    }
    function testMod2(){
        $mod = array(
            'Name'    => 'Cat Companions',
            'Author'  => 'Emma',
            'Game'    => 'MW'
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Cat_Companions;52824'),
            1,
            $mod
        );
    }

    function testMod3(){
        $mod = array(
            'Name'     => 'East Side Estate',
            'Author'   => 'Unknown / Anonymous',
            'Game'     => 'OB',
            'Version'  => '1.0',
            'Category' => 'House Mods',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/East_Side_Estate;72658'),
            1,
            $mod
        );


    }
}