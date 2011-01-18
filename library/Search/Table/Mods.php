<?php

class Search_Table_Mods extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Modification',$conn);
    }

    private function stripText($text){
       if ( mb_check_encoding($text) == "UTF-8" ) {
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            //$text = iconv("UTF-8", "ISO-8859-1//IGNORE", $text);
        }else {
            $text = html_entity_decode($text, ENT_QUOTES);
        }

        //strips non breaking spaces and replaces them with spaces
        $text = str_replace("\xa0", "\x20", $text);

        //we don't need (or want) links, etc. Also deals with html comments
        $text = strip_tags($text);

        $text = trim($text);

        return $text;
    }

    private function getModId($name, $author, $url){
        $match = $this->findMatch( $name,
                                   $author,
                                   $url);
        return $match ? $match->id : null;
    }
    private function getGameId($game){
        $gameId = Doctrine_Query::create()
                    ->select('g.id')
                    ->from('Game g')
                    ->where('g.short_name = ?', $game)
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        //Convert to better expcetion class
        if ( $gameId === false ){
            throw new Exception('No Valid Game');
        }
        return $gameId;
    }

    private function getCategoryId($categoryName){
        $categories = new Search_Table_Categories();
        $category   = $categories->createQuery('c')
                                  ->select('c.id')
                                  ->where('c.name = ?', $categoryName)
                                  ->fetchOne(array(),
                                             Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        if ( $category === false ){
            $category = $categories->create();
            $category->name = $categoryName;
            $category->save();
            return $category->id;
        }
        return $category;
    }

    /**
     * @todo this is horrible :(
     *
     * @param Search_Table_Sites $sites
     * @param array $modDetails
     */
    public function addOrUpdateModFromArray(Search_Table_Sites $sites, array $modDetails){
        $this->getConnection()->beginTransaction();

        $modId = $this->getModId($modDetails['Name'],
                                 $modDetails['Author'],
                                 $modDetails['Url']);

        foreach ( array('Name', 'Author', 'Category', 'Description') as $key ){
            $modDetails[$key] = $this->stripText($modDetails[$key]);
        }

        $modDetails['Game']     = strtolower($modDetails['Game']);
        $modDetails['Category'] = strtolower($modDetails['Category']);

        $gameId = $this->getGameId($modDetails['Game']);

        $mod = $modId ? $this->findOneById($modId) : $this->create();
        $mod->name           = $modDetails['Name'];
        $mod->author         = $modDetails['Author'];
        $mod->replace();

        $gm = new GameMods();
        $gm->game_id         = $gameId;
        $gm->modification_id = $mod->id;
        $gm->replace();

        $url  = $modDetails['Url'];
        $site = $sites->findOneByHost($url->getHost());

        $modUrlSuffix = substr((string)$url,
                               strlen($site->base_url . $site->mod_url_prefix));

        
        $locations = new Search_Table_Locations();
        $location = $locations->create();


        $categoryId = $this->getCategoryId($modDetails['Category']);

        $location->mod_url_suffix   = $modUrlSuffix;
        $location->version          = $modDetails['Version'];
        $location->description      = $modDetails['Description'];

        $location->category_id      = $categoryId;

        $location->modification_id  = $mod->id;
        $location->site_id          = $site->id;

        $location->replace();

        $this->getConnection()->commit();

    }

    /**
     * Trys to find the mod. There must be a match on either the Url or the
     * author and name.
     *
     * The Url takes precidence over the name
     *
     * @param string $name
     * @param string $author
     * @param Search_Url $url
     * @return Doctrine_Record|null Null is returned if there is no mod found
     */
    public function findMatch($name, $author, Search_Url $url){
        //first try and find a url match. This is done first so that if the
        //mod title changes, we don't run into issues with two mods, same
        //location, diffrent names

        //The url is stored in two seperate locations. Hence we need
        //to join them.
        $record = Doctrine_Query::create()
                            ->select('m.*')
                            ->from('Modification m')
                            ->innerJoin('m.Locations l')
                            ->innerJoin('l.Site s')
                            ->where('CONCAT(s.base_url,
                                            s.mod_url_prefix,
                                            l.mod_url_suffix) = ?', (string)$url)
                            ->fetchOne();

        if ( $record !== false ){
            return $record;
        }

        //then run a author and name check.
        //This is intentionally case insensitive
        $record = $this->createQuery()
                        ->select()
                        ->where('name LIKE ?', $name)
                        ->andWhere('author LIKE ?', $author)
                        ->fetchOne();

        return $record !== false ? $record : null;
    }
}