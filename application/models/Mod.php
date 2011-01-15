<?php
/* l-b
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
 * l-b */

class ModLocation{
    /**
     * @var array
     */
    private $_data;
    
    public function __construct(array $data){
        $this->_data = $data;
    }
    public function getUrl(){
        assert(array_key_exists('url', $this->_data));
        return new Search_Url($this->_data['url']);
    }
    public function getCategory(){
        return '?';
        assert(array_key_exists('category', $this->_data));
        return $this->_data['category'] ? $this->_data['category'] : 'unknown';
    }
    public function getDescription(){
        assert(array_key_exists('description', $this->_data));
        return $this->_data['description'];
    }
    public function getVersion(){
        assert(array_key_exists('version', $this->_data));
        return $this->_data['version'] ? $this->_data['version'] : 'unknown';
    }
    public function getHost(){
        assert(array_key_exists('url', $this->_data));
        return $this->getUrl()->getHost();
    }
}

class Default_Model_Mod {
    /**
     *
     * @var Zend_Db_Rowset_Abstract
     */
    private $_mod;
    /**
     * An array of ModLocation based on the id of $_mod
     *
     * @var array
     */
    private $_location = array();

    public function __construct($mid) {
        if ( !is_numeric($mid) ){
            throw new Exception("Invlalid mod");
        }

        $this->_mod = Doctrine_Query::create()
                            ->select('m.*, l.*, g.*')
                            ->addSelect('CONCAT(s.base_url, s.mod_url_prefix, l.mod_url_suffix) as url')
                            ->from('Modification m, m.Locations l, l.Site s, m.Games g')
                            ->where('m.id = ?', $mid)
                            ->orderBy('l.int_version DESC')
                            ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

        if ( !$this->_mod ){
            throw new Exception('Mod was not found');
        }

        foreach ( $this->_mod['Locations'] as $location ){
            $this->_location[] = new ModLocation($location);
        }
        var_dump($this->_mod);
    }

    public function getName() {
        return $this->_mod['name'];
    }
    public function getAuthor() {
        return $this->_mod['author'];
    }
    public function getGame() {
        //return $this->_mod['game'];
    }

    /**
     * Gets the game as an expanded string, for example OB expands to Oblivion
     *
     * @return string
     */
    public function getGameString(){
        $a = array(
            'MW' => 'Morrowind',
            'OB' => 'Oblivion',
            'UN' => 'Unknown',
        );
        return $a[$this->getGame()];
    }

        public function getLocation($index){
        return $this->_location[$index];
    }
    
    public function getLocations(){
        return $this->_location;
    }

}

?>
