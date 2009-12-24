<?php /* l-b
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
 * l-b */ ?>

<?php

class Search_Table_Mods extends Zend_Db_Table_Abstract {
    protected $_name = 'Mods';
    protected $_primary = 'ModID';

    protected $_dependentTables = array('Search_Table_ModLocation');

    /**
     * Get next unused ID
     *
     * @return int
     */
    public function getNextID() {
        $select = $this->select()
            ->from($this->_name,'ModID')
            ->limit(1)
            ->order('ModID DESC');
        $row = $this->fetchRow($select);
        return $row ? ( $row->ModID + 1) : 0;
    }
    
    /**
     * Gets the id of an exact match or -1 if there is no match
     *
     * @param string $name
     * @param string $author
     * @param string $game
     * @return int
     */
    public function getID($game, $name, $author) {
        $select = $this->select('ModID')
            ->where('Name=?', $name)
            ->where('Author=?', $author)
            ->where('Game=?', $game)
            ->limit(1);
        $row = $this->fetchRow($select);
        return  $row ? $row->ModID : -1;
    }


    /**
     * Gets a mod with the given ID
     *
     * @return Zend_Db_Rowset_Abstract
     */
    public function getMod($mid) {
        $mid = (int) $mid;
        $rows = $this->find($mid);
        assert($rows->count() <= 1 );
        return $rows->count() ? $rows->current() : null;
    }

    /**
     * Adds a mod to the database
     *
     * @return void
     */
    public function addMod($mid, $game, $name, $author) {
        $mid = (int) $mid;
        $mod = array(
            'ModID'     => $mid,
            'Name'      => $name,
            'Author'    => $author,
            'Game'      => $game,
        );
        $this->insert($mod);
    }

    /**
     * Updates a mod that exists in the database
     *
     * @param <type> $mid
     * @param <type> $game
     * @param <type> $name
     * @param <type> $author
     */
    public function updateMod($mid, $game, $name, $author) {
        $mid = (int)$mid;
        $mod = array(
            'Name'      => $name,
            'Author'    => $author,
            'Game'      => $game,
        );
        $where = $this->getAdapter()->quoteInto('ModID=?', $mid);
        $this->update($mod, $where);
    }

    /**
     * @return void
     */
    public function removeMod($mid) {
        $mid = (int) $mid;
        $where = $this->getAdapter()->quoteInto('ModID=?', $mid);
        $this->delete($where);
    }

    public function count() {
        $select = $this->select()
            ->from($this,'COUNT(*) AS num');
        return $this->fetchRow($select)->num;
    }

    public function getMods(array $mids) {
        if ( count($mids) == 0) {
            throw new Exception('Must be at least some IDs');
        }
        foreach ( $mids as $i ) {
            if ( !is_numeric($i) ) {
                throw new Exception('All values must be numeric');
            }
        }
        $queryStr = implode(',', $mids);

        $select = $this->select()->where("ModID IN ($queryStr)");
        return $this->fetchAll($select);
    }
}
