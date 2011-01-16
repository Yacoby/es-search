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
        assert(array_key_exists('category', $this->_data));
        return $this->_data['category'] ? ucwords($this->_data['category']) : 'Unknown';
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

        $sql = 'SELECT
                    m.id, m.name, m.author, l.description, l.version,
                    c.name as category,
                    CONCAT(s.base_url, s.mod_url_prefix, l.mod_url_suffix) as url
                FROM modification m
                LEFT JOIN location l  ON l.modification_id = m.id
                LEFT JOIN site s      ON l.site_id         = s.id
                LEFT JOIN category c  ON l.category_id     = c.id
                WHERE (m.id = ?)
                ORDER BY l.int_version DESC';

        $dbh =  Doctrine_Manager::getInstance()
                                ->getCurrentConnection()
                                ->getDbh();

        $stmt = $dbh->prepare($sql);
        $stmt->execute(array((int)$mid));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty ($result)){
            throw new Exception('Mod was not found');
        }

        foreach ( $result as $row ){
            $location = array(
                'url'         => $row['url'],
                'category'    => $row['category'],
                'description' => $row['description'],
                'version'     => $row['version'],
            );
            $this->_location[] = new ModLocation($location);
        }
        $this->_mod = array(
            'name'   => $result[0]['name'],
            'author' => $result[0]['author'],
        );
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
