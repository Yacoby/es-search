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

class Search_Table_ModLocation extends Zend_Db_Table_Abstract {
    protected $_name    = 'ModLocation';
    protected $_primary = array('ModID', 'URL');

    protected $_referenceMap    = array(
            'Search_Table_Mods' => array(
                            'columns'           => array('ModID'),
                            'refTableClass'     => 'Search_Table_Mods',
                            'refColumns'        => array('ModID'),
                            'onDelete'          => self::CASCADE,
            ),
    );


    /**
     * Gets a mod id by a given url.
     *
     * @param string $game
     * @param URL $url
     * @return int or -1 if the mod id doesn't exist
     */
    public function getIdByUrl(URL $url) {
        $select = $this->select()
                ->from($this, 'ModID')
                ->where('URL=?', $url->toString());
        //->where('Game=?', $game);
        $r = $this->fetchRow($select);

        return $r ? $r->ModID : -1;
    }


    public function getLocations($mid) {
        $select = $this->select()
                ->where('ModID=?', array((int)$mid));
        return $this->fetchAll($select);
    }

    public function getLocationCount($mid) {
        $select = $this->select()
                ->from($this, 'COUNT(*) AS Num')
                ->where('ModID=?', (int)$mid);
        
        return $this->fetchRow($select)->Num;
    }
    public function getLocation($mid, URL $url) {
        $select = $this->select()
                ->where('ModID=?', (int)$mid)
                ->where('URL=?', $url->toString());
        return $this->fetchRow($select);
    }


    public function addLocation($mid, URL $url,  $cat, $ver, $desc) {
        $data = array(
                'ModID' => (int)$mid,
                'URL'   => $url->toString(),
                'Version' => $ver,
                'Description' => $desc,
                'Category' => $cat,
        );
        $this->insert($data);
    }

    public function updateLocation($mid, URL $url, $cat, $ver, $desc) {
        assert($this->getLocation($mid, $url) != null);

        $select = $this->select()
                ->where('ModID=?', (int)$mid)
                ->where('URL=?', $url->toString());

        $where = implode(' ', $select->getPart(Zend_Db_Select::WHERE));

        $data = array(
                'Version' => $ver,
                'Description' => $desc,
                'Category' => $cat,
        );
        $this->update($data, $where);
    }

    public function removeLocation($mid, URL $url) {
        $select = $this->select()
                ->where('ModID=?', (int)$mid)
                ->where('URL=?', $url->toString());

        $where = implode(' ', $select->getPart(Zend_Db_Select::WHERE));
        $this->delete($where);

    }
}