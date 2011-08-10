<?php

class Zend_View_Helper_Stats{
    public function stats(){
        $rows = Doctrine_Query::create()
                    ->select('id, game_id, COUNT(id) as count')
                    ->from('Modification')
                    ->groupBy('game_id, id')
                    ->execute();

        $games = array(3 => 0, 4 => 0, 5 => 0);
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
