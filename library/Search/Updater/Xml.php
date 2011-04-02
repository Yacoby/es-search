<?php

/**
 * Class for dealing updates from XML files
 */
class Search_Updater_Xml implements Search_Updater_Interface{

    public function update() {
        list($file, $source) = $this->findXmlToUpdate();
        if ( $file === null ){
            return array();
        }
        $fileContent = $this->getXmlFile();

        $mods = $this->getXmlFileMods($fileContent);

        $modsOnDisk = $this->getStoredMods($source);

        //do something like this.
        $deleting = array_diff($modsOnDisk, $mods);
        $adding   = array_diff($mods, $modsOnDisk);

    }

    private function isLocalFile($fname){
        return stripos($fname, 'http') !== 0;
    }
    /**
     * Checks the database for an xml file that needs to be updated
     */
    private function findXmlToUpdate(){
    }

    private function getXmlFile(){
        return $this->isLocalFile($fname) ? $this->getLocalXmlFile() : $this->getRemoteXmlFile();
    }
    private function getRemoteXmlFile(){
    }
    private function getLocalXmlFile(){
    }

    private function getXmlFileMods($file){

    }
    private function getStoredMods(){

    }

}