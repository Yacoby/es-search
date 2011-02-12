<?php

class Zend_View_Helper_Stats{
    public function stats(){
        $rows = Doctrine_Query::create()
                    ->select('game_id, COUNT(*) as count')
                    ->from('GameMods')
                    ->groupBy('game_id')
                    ->execute();

        $games = array();
        foreach ( $rows as $row ){
            $games[$row->game_id] = $row->count;
        }

        $modCount = Doctrine_Query::create()
                    ->select('COUNT(*) as count')
                    ->from('Modification')
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        $newest = Doctrine_Query::create()
                    ->select()
                    ->from('Modification')
                    ->orderBy('id DESC')
                    ->limit(1)
                    ->fetchOne(array(),Doctrine_Core::HYDRATE_ARRAY);

        return array(
            'Games'     => $games,
            'ModCount'  => $modCount,
            'Newest'    => $newest,
        );
    }

}