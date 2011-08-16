<?php
class Search_Table_Pages extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Page',$conn);
    }

    private function toUrlSuffix($site, Search_Url $url){
        $i = stripos((string)$url, $site->base_url);
        if ( $i === false ){
            return (string)$url;
        }
        return substr((string)$url, $i + strlen($site->base_url));
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
                        ->from('Page p, p.ByteLimitedSource s')
                        ->where('CONCAT(s.base_url, p.url_suffix)=?', (string)$url)
                        ->limit(1)
                        ->fetchOne();
    }

    public function createByUrl($site, Search_Url $url){
        $page = $this->create();
        $page->url_suffix = $this->toUrlSuffix($site, $url);
        return $page;
    }

    public function findOneByUpdateRequired(){
        return Doctrine_Query::create()
                ->select('p.*, s.*')
                ->from("Page p, p.ByteLimitedSource s, s.ModSource m")
                ->where('s.bytes_used < s.byte_limit')
                ->andWhere('p.revisit < ?', time())
                ->andWhere('p.revisit != 0')
                ->andWhere('m.scrape = ?', true)
                ->orderBy('p.revisit ASC')
                ->limit(1)
                ->fetchOne();
    }
}
