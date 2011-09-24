<?php

class Search_Table_Mods extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Modification',$conn);
    }

    private function stripText($text){
        //strips non breaking spaces and replaces them with spaces
        $nbs = html_entity_decode('&#00A0', ENT_COMPAT, 'UTF-8');
        $text->replace($nbs, "\x20");

        //we don't need (or want) links, etc. Also deals with html comments
        $text->stripTags($text);

        $text->trim();

        return $text;
    }

    private function getModId($name, $author, $url){
        $match = $this->findMatch( $name,
                                   $author,
                                   $url);
        return $match ? $match->id : null;
    }
    
    private function getGameIdFromShortName($game){
        $gameId = Doctrine_Query::create()
                    ->select('g.id')
                    ->from('Game g')
                    ->where('g.short_name = ?', $game)
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        //Convert to better expcetion class
        if ( $gameId === false ){
            throw new Exception('No valid game found for ' . $game
                               . ' Have you checked that the database is'
                               . ' properly populdated');
        }
        return $gameId;
    }

    private function getOrCreateCategoryId($categoryName){
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
    public function addOrUpdateModFromArray(Search_Table_ModSources $sources,
                                            array $modDetails,
                                            $sourceId){
        
        //$this->getConnection()->beginTransaction();
                                                
        foreach ( array('Name', 'Author', 'Category', 'Description') as $key ){
            $modDetails[$key] = $this->stripText($modDetails[$key])->getBytes();
        }

        $modId = $this->getModId($modDetails['Name'],
                                 $modDetails['Author'],
                                 $modDetails['Url']);


        $modDetails['Game']     = strtolower($modDetails['Game']);
        $modDetails['Category'] = strtolower($modDetails['Category']);

        $gameId = $this->getGameIdFromShortName($modDetails['Game']);

        $mod = $modId ? $this->findOneById($modId) : $this->create();
        $mod->name           = $modDetails['Name'];
        $mod->author         = $modDetails['Author'];
        $mod->game_id        = $gameId;
        $mod->replace();

        $url    = $modDetails['Url'];
        $source = $sources->findOneById($sourceId);
        if ( $source === false ){
            throw new Exception('There was no soucre for the given id');
        }
        
        
        $locations = new Search_Table_Locations();
        $location  = $locations->create();
        
        $modUrlSuffix = substr((string)$url, strlen($source->url_prefix));
        $location->url_suffix   = $modUrlSuffix;
        
        $location->version          = $modDetails['Version'];
        $location->description      = $modDetails['Description'];

        $categoryId = $this->getOrCreateCategoryId($modDetails['Category']);
        $location->category_id      = $categoryId;

        $location->modification_id  = $mod->id;
        $location->mod_source_id    = $source->id;
        $location->replace();

        //$this->getConnection()->commit();
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
                            ->innerJoin('l.ModSource s')
                            ->where('CONCAT(s.url_prefix,
                                            l.url_suffix) = ?', (string)$url)
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
