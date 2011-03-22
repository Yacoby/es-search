<?php

/**
 * Class for dealing updates from XML files
 */
class Search_Updater_Xml implements Search_Updater_Interface{

    public function update() {
        $file = $this->findXmlToUpdate();
        if ( $file === null ){
            return array();
        }
        $fileContent = $this->getXmlFile();

        $mods = $this->getXmlFileMods($fileContent);

        $modsOnDisk = $this->getStoredMods();




    }

    private function isLocalFile($fname){
        return stripos($fname, 'http') !== 0;
    }
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