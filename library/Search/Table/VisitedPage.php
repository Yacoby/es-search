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

/**
 * Manages visited pages.
 *
 * For a page to show up, the host has to exist in the websites table
 */
class Search_Table_VisitedPage extends Zend_Db_Table_Abstract {
    protected $_name = 'VisitedPage';

    /**
     * Gets the page needing a visit the most ugrently
     *
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function getPageNeedingVisit() {
        $cols = array('HostName', 'URL', 'LastVisited', 'NeedRevisit');
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($this->_name, $cols)
                ->joinInner('Website', 'VisitedPage.HostName = Website.HostName')
                ->where('NeedRevisit > 0')
                ->where('BytesUsed < ByteLimit')
                ->where('NeedRevisit < '.time())
                ->where('Enabled = 1')
                ->order('NeedRevisit ASC')
                ->limit(1);
        return $this->fetchRow($select);
    }

    /**
     * gets the number of pages needing a vistit
     *
     * @todo check the integrity check or document. Looks bad?
     *
     * @return int
     */
    public function getNumPagesNeedingVisit() {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($this->_name, 'COUNT(VisitedPage.HostName) as RowCount')
                ->joinInner('Website', 'VisitedPage.HostName = Website.HostName',array())
                ->where('NeedRevisit > 0')
                ->where('BytesUsed < ByteLimit')
                ->where('NeedRevisit < '.time())
                ->where('Enabled = 1')
                ->order('NeedRevisit ASC');
        return $this->fetchRow($select)->RowCount;
    }

    /**
     * Sets a page as having been visited, so it will no longer be retrived by
     * the get pages needing visit functions.
     *
     * @param URL $url
     */
    public function setPageVisited(URL $url) {
        assert($url->isValid());
        $data = array(
                'LastVisited' => time(),
                'NeedRevisit' => 0
        );
        $where = $this->getAdapter()->quoteInto("URL=?", $url->toString());
        $this->update($data, $where);
    }


    public function hasPage(URL $url) {
        assert($url->isValid());
        return ($this->getPage($url) != null);
    }

    /**
     * Gets the page with the given Url or null if it doesn't exist
     *
     * @param URL $url
     * @return Zend_Db_Table_Row_Abstract|null
     */
    public function getPage(URL $url) {
        assert($url->isValid());
        $select = $this->select()
                ->where('URL=?', $url->toString());
        return $this->fetchRow($select);
    }

    /**
     * Adds a page to the index. the page shouldn't exist
     *
     * @param URL $url
     * @param int $days The number of days wait untill this should be parsed
     */
    public function addPage(URL $url, $days = 0) {
        assert($url->isValid());
        assert(!$this->hasPage($url));

        $data = array(
                "HostName"      => $url->getHost(),
                "URL"           => $url->toString(),
                "LastVisited"   => 0,
                "NeedRevisit"   => time()+(((int)$days)*60*60*24)
        );
        $this->insert($data);
    }

    /**
     * Sets a page as needing a visit. the optional days paramater is how long
     * it should be before the visit takes place
     *
     * @param URL $url
     * @param int $days
     */
    public function setNeedVisit(URL $url, $days = 30) {
        assert($url->isValid());
        assert($this->hasPage($url));

        $newUpdateTime = time() + (((int)$days)*60*60*24);
        $data = array(
                'NeedRevisit' => $newUpdateTime
        );
        $where = $this->getAdapter()->quoteInto("URL=?", $url->toString());
        $this->update($data, $where);
    }

}
