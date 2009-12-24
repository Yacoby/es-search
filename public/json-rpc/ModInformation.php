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
 * l-b */

require '../../AppLoader.php';
createApplication(dirname(__FILE__).'/Bootstrap/Bootstrap.php');

/**
 * Exposes mod information details via a safe interface. Used for the json-rpc
 * server
 */
class ModInformation {
    /**
     * Gets all the mod details about a mod based on the mod id
     *
     * @param int $mid the mod id to get the details of
     * @return array
     */
    public function getModDetails($mid) {
        $mm = new Default_Model_Mod((int)$mid);

        $details = array(
                'Name'      => $mm->getName(),
                'Author'    => $mm->getAuthor(),
                'Game'      => $mm->getGameString(),
                'Location'  => array()
        );

        foreach ($mm->getLocations() as $l) {
            $details['Location'][] = array(
                    'URL'            => $l->getURL()->toString(),
                    'Host'           => $l->getHost(),
                    'Version'        => $l->getVersion(),
                    'Category'       => $l->getCategory(),
                    'Description'    => $l->getDescription()
            );
        }

        return $details;
    }
}


$server = new Zend_Json_Server();
$server->setClass('ModInformation');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $server->setTarget('/ModInformation.php')
            ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

    $smd = $server->getServiceMap();

    header('Content-Type: application/json');
    echo $smd;
    return;
}else {
    $server->handle();
}