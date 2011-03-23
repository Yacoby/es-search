<?php
/**
 * This handles updating the changed mods. It is passed a class that complies
 * with an interface that can be used to update a site, xml, or other source
 */
class Search_UpdateWorker{
    /**
     * @var Search_Table_Mods
     */
    private $_mods;
    /**
     * @var Search_Table_Locations
     */
    private $_locations;

    private $_sources;

    public function  __construct(
            Search_Table_Mods $mods        = null,
            Search_Table_Locations $locs   = null,
            Search_Table_ModSources $ms    = null
    ) {
        $this->_mods      = $mods  ? $mods  : new Search_Table_Mods();
        $this->_locations = $locs  ? $locs  : new Search_Table_Locations();
        $this->_sources   = $ms    ? $ms    : new Search_Table_ModSources();
    }

    /**
     * This runs the updater task on a object, and syncs the results with the
     * database
     *
     * @param Search_Updater_Interface $updater 
     */
    public function runUpdateTask(Search_Updater_Interface $updater){
        $result = $updater->update();

        if (array_key_exists('NewUpdated', $result)){
            foreach ( $result['NewUpdated'] as $mod ){
                $this->addOrUpdateMod($mod);
            }
        }
        if (array_key_exists('Deleted', $result)){
            foreach ( $result['Deleted'] as $url ){
                $this->removeLocation($url);
            }
        }
    }

    private function addOrUpdateMod(array $modArray) {
        //merge mod with default values
        $defualts = array(
                'Version'     => '',
                'Category'    => '',
                'Description' => '',
        );
        $modArray = array_merge($defualts, $modArray);

        //there is a transaction in this function, so we don't need one here
        $this->_mods->addOrUpdateModFromArray($this->_sources, $modArray);

        Search_Logger::info("Added Mod: {$modArray['Name']}");
    }
    private function removeLocation(Search_Url $url){
            $this->_locations->deleteByUrl($url);
    }
}