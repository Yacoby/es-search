<?php /* l-b
 * This file is part of ES Search.
 * 
 * Copyright (c) 2009 Jacob Essex
 * 
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */ ?>

<?php

//require 'Tables/Mods.php';
//require 'Tables/ModLocation.php';

class ModLocation{
    private $_data;
    public function __construct($data){
        $this->_data = $data;
    }
    public function getURL(){
        return new URL($this->_data['URL']);
    }
    public function getCategory(){
        return $this->_data['Category'] ? $this->_data['Category'] : 'Unknown';
    }
    public function getDescription(){
        return $this->_data['Description'];
    }
    public function getVersion(){
        return $this->_data['Version'] ? $this->_data['Version'] : 'Unknown';
    }
    public function getHost(){
        return $this->getURL()->getHost();
    }
}

class Default_Model_Mod {
    private $_mod;
    private $_location = array();
    public function __construct($mid) {
        if ( !is_numeric($mid) ){
            throw new Exception("Invlalid mod");
        }

        $mt = new Search_Table_Mods();
        $this->_mod = $mt->getMod($mid);

        if ( !$this->_mod ){
            throw new Exception('Mod was not found');
        }

        $locations = $this->_mod->findDependentRowset('Search_Table_ModLocation');
        foreach ($locations as $l){
            $this->_location[] = new ModLocation($l);
        }
    }

    public function getName() {
        return $this->_mod['Name'];
    }
    public function getAuthor() {
        return $this->_mod['Author'];
    }
    public function getGame() {
        return $this->_mod['Game'];
    }

    public function getGameString(){
        $a = array(
            'MW' => 'Morrowind',
            'OB' => 'Oblivion',
            'UN' => 'Unknown',
        );
        return $a[$this->getGame()];
    }
    
    public function getLocations(){
        return $this->_location;
    }

}

?>
