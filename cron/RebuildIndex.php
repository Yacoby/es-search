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
/*
 * This rebuilds the search index from the database
 *
 * I really would suggest something like Java is used for this. It is
 * really really slow, and there are limits to the number of files open in
 * php. Java would allow a much larger amount of segments to be open, hence
 * speeding it up.
 *
 * On my (addmittedly slow) machine, it takes upwards of 5 hours to index
 * 20,000 mods
*/

require '../AppLoader.php';
createApplication(realpath(dirname(__FILE__).'/Bootstrap/Bootstrap.php'));

ini_set('memory_limit', '256M');
set_time_limit(0);

$lucene = new Search_Data_DB_Lucene();
$lucene->setRebuildMode();

$tbl = new Search_Table_Mods();
$loc = new Search_Table_ModLocation();


$s = $loc->select('*');
$results = $loc->fetchAll($s);

echo "Creating location data<br />\n";
$locs = array();
foreach ( $results as $result ) {
    if ( !isset($locs[$result->ModID]) ) {
        $locs[$result->ModID] = array();
    }
    $locs[$result->ModID][] = $result->Description;
}
echo "Created location data<br />\n";


$s = $tbl->select('*')->order('ModID');
$results = $tbl->fetchAll($s);

$i = 0;
foreach ( $results as $result ) {
    echo "{$result->ModID}<br />\n";
    if ( $i++ % 100 == 0 ){
        flush();
    }

    $array = $result->toArray();
    $array['Description'] = implode(' ', $locs[$result->ModID]);

    $lucene->addMod($result->Game, $result->ModID, $array);

    unset($locs[$result->ModID]);

}

echo "++DONE++";
