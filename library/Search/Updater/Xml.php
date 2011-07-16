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

        $modsOnDisk = $this->getStoredModUrls($source);

        //do something like this.
        $deleting   = array_diff($modsOnDisk, $this->getModUrls($mods));
        $addingUrls = array_diff($this->getModUrls($mods), $modsOnDisk);

        $adding = array();
        foreach ( $mods as $mod ){
            if ( in_array($mod['Url'], $addingUrls) ){
                $adding[] = $mod;
            }
        }

        return array(
            'Source'     => $source,
            'Deleted'    => $deleting,
            'NewUpdated' => $adding,
        );
    }

    private function getModUrls($mods){
        $urls = array();
        foreach ( $mods as $mod ){
            $urls[] = $mod['Url'];
        }
        return $urls;
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
    private function getStoredModUrls(){

    }

}