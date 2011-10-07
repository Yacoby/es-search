<?php

class Zend_View_Helper_Stats{
    public function stats(){
        //I didn't manage to get this working using Doctrine correctly, so
        //I gave up and used raw sql
        $sql = 'SELECT
                    game_id, COUNT(*) as count
                FROM modification
                GROUP BY game_id';

        $dbh =  Doctrine_Manager::getInstance()
                                ->getCurrentConnection()
                                ->getDbh();

        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $games = array(3 => 0, 4 => 0, 5 => 0);
        foreach ( $rows as $row ){
            $games[$row['game_id']] = $row['count'];
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
