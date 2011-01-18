<?php

class Default_Model_Recent{

    /**
     * Gets a list of the $num most recent searches
     *
     * @param int $num
     * @return array
     */
    public function getRecentHistory($num = 5){
        $results = Doctrine_Query::create()
                        ->select('h.game_id, h.general, h.name, h.author, h.description')
                        ->from('SearchHistory h')
                        ->leftJoin('h.HistoryBanned b')
                        ->where('b.ip IS NULL')
                        ->limit((int)$num)
                        ->orderBy('h.id DESC')
                        ->fetchArray();
        return $results;
    }
}