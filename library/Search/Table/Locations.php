<?php
class Search_Table_Locations extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Location',$conn);
    }

    public function deleteByUrl($url){
        //this doesn't do the delete through dql, so that the listner code is
        //called.
        $loc = Doctrine_Query::create()
                        ->select()
                        ->from('Location l')
                        ->innerJoin('l.ModSource s')
                        ->where('CONCAT(s.url_prefix, l.url_suffix) = ?', (string)$url)
                        ->fetchOne();

        if ( $loc !== false ){
            $loc->delete();
        }
    }
}