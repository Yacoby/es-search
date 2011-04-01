<?php
class EsFilefrontTest extends PageTest {

    public function __construct() {
        parent::__construct('elderscrolls.filefront.com');
    }

    public function testMods() {
        $details = array(
            array(
                'Url'     => 'http://elderscrolls.filefront.com/file/Battle_Tattoos;73175',
                'Name'    => 'Battle Tattoos',
                'Author'  => 'Kevin P. Cook',
                'Version' => '1.0',
                'Game'    => 'OB',
            ),
            array(
                'Url'     => 'http://elderscrolls.filefront.com/file/Cat_Companions;52824',
                'Name'    => 'Cat Companions',
                'Author'  => 'Emma',
                'Game'    => 'MW'
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/East_Side_Estate;72658',
                'Name'     => 'East Side Estate',
                'Author'   => 'Unknown / Anonymous',
                'Game'     => 'OB',
                'Version'  => '1.0',
                'Category' => 'House Mods',
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/Cosmic_SkyCycling;98980',
                'Name'     => 'Cosmic SkyCycling',
                'Author'   => 'Samroski/Sycamore',
                'Game'     => 'OB',
                'Version'  => '4.0.0.1969',
                'Category' => 'Sound and Textures',
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/Croatian_Medieval_Armor_set;116637',
                'Name'     => 'Croatian Medieval Armor set',
                'Author'   => 'CRO White Wolf',
                'Game'     => 'OB',
                'Version'  => '1.1beta',
                'Category' => 'Weapon and Armor',
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/Oblivion_Itemizer_v300;106519',
                'Name'     => 'Oblivion Itemizer v3.00',
                'Author'   => 'spike1000',
                'Game'     => 'OB',
                'Version'  => '3.00',
                'Category' => 'Utilities',
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/Oblivion_Itemizer_v301;107015',
                'Name'     => 'Oblivion Itemizer v3.01',
                'Author'   => 'spike1000',
                'Game'     => 'OB',
                'Version'  => '3.01',
                'Category' => 'Utilities',
            ),
            array(
                'Url'      => 'http://elderscrolls.filefront.com/file/Exile_Spirits_of_the_Underworld_Mod;52669',
                'Name'     => 'Exile: Spirits of the Underworld Mod',
                'Author'   => 'Unknown / Anonymous',
                'Game'     => 'MW',
                'Category' => 'Modifications',
            ),
        );
        $this->helpTestMods($details);
    }

}