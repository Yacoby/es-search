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

class LuceneModDatabaseTest extends PHPUnit_Framework_TestCase {

    public function setup() {
        resetLucene();
    }

    public function testAddMod1() {
        $db = new Search_Data_DB_Lucene();

        $mod = array(
            'Name' => 'TheAmazing',
            'Author' => 'Yacoby',
            'Description' => 'Yacobyies mod',
        );

        $this->assertEquals(0, $db->getModCount('MW'));
        $db->addMod('MW', 0, $mod, 'iso-8859-1');
        $this->assertEquals(1, $db->getModCount('MW'));
    }

    static function getDBWithBasicData() {
        $db = new Search_Data_DB_Lucene();

        $mod = array(
            'Name' => 'The Amazing',
            'Author' => 'Yacoby',
            'Description' => 'Mod',
        );
        $db->addMod('MW', 0, $mod, 'iso-8859-1');
        return $db;
    }

    public function testSearch() {
        $db = self::getDBWithBasicData();
        $results = $db->search('MW', 'Yacoby', 0, 10);

        $this->assertEquals(1, count($results->results()));
        $this->assertEquals(0, $results->getResult(0)->ModID);
        $this->assertEquals('The Amazing', $results->getResult(0)->Name);
    }

    public function testSearchAdvanced() {
        $db = self::getDBWithBasicData();
        $results = $db->searchAdvanced('MW', null, 'Amazing', null, 0, 10);
        $this->assertEquals(0, count($results->results()));

        $results = $db->searchAdvanced('MW', 'Amazing', null, null, 0, 10);
        $this->assertEquals(1, count($results->results()));

        $results = $db->searchAdvanced('MW', 'Yacoby', null, null, 0, 10);
        $this->assertEquals(0, count($results->results()));

        $results = $db->searchAdvanced('MW', 'Mod', null, null, 0, 10);
        $this->assertEquals(0, count($results->results()));

        $results = $db->searchAdvanced('MW', null, null, 'Mod', 0, 10);
        $this->assertEquals(1, count($results->results()));
    }

    public function testRemoveMod() {
        $db = self::getDBWithBasicData();

        $this->assertEquals(1, $db->getModCount('MW'));
        $db->removeMod('MW', 0);
        unset($db);

        $db = new Search_Data_DB_Lucene();
        $this->assertEquals(0, $db->getModCount('MW'));

        $results = $db->search('MW', 'Yacoby', 0, 10);
        $this->assertEquals(0, count($results->results()));

    }




}