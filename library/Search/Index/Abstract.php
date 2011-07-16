<?php

interface Search_Index_Abstract{
    
    public function  __construct($game);
    
    /**
     *
     * @param string $query The user entered query
     * @param int $offset the 
     * @param int $limit
     * @return Search_Index_SearchResults
     */
    public function searchSimple($query, $offset, $limit);

    /**
     *
     * @param string $name
     * @param string $author
     * @param string $description
     * @param int $offset
     * @param int $limit
     * @return Search_Index_SearchResults
     */
    public function searchAdvanced($name, $author, $description, $offset, $limit);
}