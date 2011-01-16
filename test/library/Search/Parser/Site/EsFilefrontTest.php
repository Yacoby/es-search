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

    function testMod4(){
        $mod = array(
            'Name'     => 'Cosmic SkyCycling',
            'Author'   => 'Samroski/Sycamore',
            'Game'     => 'OB',
            'Version'  => '4.0.0.1969',
            'Category' => 'Sound and Textures',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Cosmic_SkyCycling;98980'),
            1,
            $mod
        );
    }
    function testMod5(){
        $mod = array(
            'Name'     => 'Croatian Medieval Armor set',
            'Author'   => 'CRO White Wolf',
            'Game'     => 'OB',
            'Version'  => '1.1beta',
            'Category' => 'Weapon and Armor',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Croatian_Medieval_Armor_set;116637'),
            1,
            $mod
        );
    }
    function testMod6(){
        $mod = array(
            'Name'     => 'Grey Fox Fans',
            'Author'   => 'Unknown / Anonymous',
            'Game'     => 'OB',
            'Version'  => '1.0',
            'Category' => 'Miscellaneous',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Grey_Fox_Fans;80945'),
            1,
            $mod
        );
    }
    function testMod7(){
        $mod = array(
            'Name'     => 'Oblivion Itemizer v3.00',
            'Author'   => 'spike1000',
            'Game'     => 'OB',
            'Version'  => '3.00',
            'Category' => 'Utilities',
        );
        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Oblivion_Itemizer_v300;106519'),
            1,
            $mod
        );

        $mod['Version'] = '3.01';
        $mod['Name']    = 'Oblivion Itemizer v3.01';

        $this->helpTestModPage(
            new Search_Url('http://elderscrolls.filefront.com/file/Oblivion_Itemizer_v301;107015'),
            1,
            $mod
        );
    }
}