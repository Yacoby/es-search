<?php

/**
 * This class is responsible for converting a string version to a numeric one
 * when a mod is saved.
 * 
 * This is obviously attached to the Location record, as that holds a mod version
 */
class Search_Listener_Location_Version extends Doctrine_Record_Listener{
    /**
     * Sets the int version, dependant on the string version
     * @param Doctrine_Event $e
     */
    public function preSave(Doctrine_Event $e){

        $loc = $e->getInvoker();
        if ( $loc->isModified() ){
            $loc->int_version = Search_Version::fromString($e->Version);
        }
    }

}