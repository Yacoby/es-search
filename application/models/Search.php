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


class Default_Model_Search {
    public static function sortCallback($a, $b){
        return $a['score'] < $b['score'];
    }

    private $_result;

    public function __construct(array $vals, $lowerBound, $count) {
        $this->_db = new Search_Lucene_Db((int)$vals['game']);
        $searchResults = $this->search($vals, $lowerBound, $count);

        $modIds = array();
        $idScoreMap = array();
        foreach( $searchResults->results() as $result ){
            $modIds[] = $result->mod_id;
            $idScoreMap[$result->mod_id] = $result->score;
        }

        if ( $searchResults->count() == 0  ){
            $this->_result = array();
        }else{

            $mods = Doctrine_Query::create()
                        ->select('m.*, l.*')
                        ->addSelect('CONCAT(s.base_url, s.mod_url_prefix, l.mod_url_suffix) as Url')
                        //->addSelect('COUNT(l.version)')
                        ->from('Modification m')
                        ->leftJoin('m.Locations l')
                        ->leftJoin('l.Site s')
                        ->whereIn('m.id', $modIds)
                        ->andWhere('l.int_version IN
                            (SELECT MAX(int_version)
                                    FROM Location WHERE m.id = modification_id)')
                        ->fetchArray();

            foreach ( $mods as &$mod ){
                $mod['score'] = $idScoreMap[$mod['id']];
            }

            uasort($mods, array($this, 'sortCallback'));

            $this->_result = $mods;
        }
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
