<?php

class Default_Model_Search {
    public static function scoreCmp($a, $b){
        return $a['score'] < $b['score'];
    }

    private $_result;
    private $_count;

    public function __construct(array $vals, $lowerBound, $count) {
        $this->_db = new Search_Index_Sphinx((int)$vals['game']);
        $searchResults = $this->search($vals, $lowerBound, $count);

        $this->_count = $searchResults->count();

        $modIds = array();
        $idScoreMap = array();
        foreach( $searchResults->results() as $result ){
            $modIds[] = $result['mod_id'];
            $idScoreMap[$result['mod_id']] = $result['score'];
        }

        if ( $searchResults->count() == 0  ){
            $this->_result = array();
        }else{
            $mods = Doctrine_Query::create()
                        ->select('m.*, l.*')
                        ->addSelect('CONCAT(s.url_prefix, l.url_suffix) as url')
                        ->from('Modification m')
                        ->innerJoin('m.Locations l')
                        ->innerJoin('l.ModSource s')
                        ->whereIn('m.id', $modIds)
                        ->andWhere('l.int_version = 0')
                        ->groupBy('m.id')
                        ->fetchArray();

            foreach ( $mods as &$mod ){
                $mod['score'] = $idScoreMap[$mod['id']];
            }

            usort($mods, array($this, 'scoreCmp'));

            $this->_result = $mods;
        }
    }

    public function count(){
        return $this->_count;
    }

    /**
     *
     * @return array
     */
    public function getResults() {
        return $this->_result;
    }

    private function search($vals, $lowerBound, $count) {
        if ( array_key_exists('general', $vals) ) {
            return $this->_db->searchSimple(
                    $vals['general'],
                    $lowerBound,
                    $count
            );
        }

        return $this->_db->searchAdvanced(
                $vals['name'],
                $vals['author'],
                $vals['description'],
                $lowerBound,
                $count
        );
    }

}

?>
