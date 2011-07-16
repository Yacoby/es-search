<?php

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

        //I can't do this through Doctrine, as it doesn't hydrate correctly,
        //as the urls are not grouped with the mods.

        $sql = 'SELECT
                    m.id, m.name, m.author, l.description, l.version,
                    c.name as category,
                    CONCAT(s.url_prefix, l.url_suffix) as url
                FROM modification m
                INNER JOIN location l   ON l.modification_id = m.id
                INNER JOIN mod_source s ON l.mod_source_id   = s.id
                INNER JOIN category c   ON l.category_id     = c.id
                WHERE (m.id = ?)
                ORDER BY l.int_version ASC';

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
        //we know that $result[0] must be set, as $result is not empty
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
