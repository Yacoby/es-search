<?php
class Search_Table_Pages extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Page',$conn);
    }

    /**
     *
     * There is no need for a findByUrl function, as there should never
     * be more than result
     *
     * @param string|Search_Url $url
     */
    public function findOneByUrl($url){
        return Doctrine_Query::create()
                        ->select()
                        ->from('Page p, p.Site s')
                        ->where('CONCAT(s.base_url, p.url_suffix)=?', (string)$url)
                        ->fetchOne();
    }

    /**
     *
     * @todo maybe some sort of ordering would be very good
     * @return Doctrine_Record
     */
    public function findOneByUpdateRequired(){
        return Doctrine_Query::create()
                ->select('p.*, s.*')
                ->from('Page p, p.Site s')
                ->where('s.bytes_used < s.byte_limit')
                ->andWhere('p.revisit < ?', time())
                ->andWhere('p.revisit != 0')
                ->andWhere('s.enabled = 1')
//              ->orderBy('p.revisit DESC') //TODO for testing only
                ->fetchOne();
    }
}