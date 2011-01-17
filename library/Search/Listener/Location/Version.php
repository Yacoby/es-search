<?php

/**
 * This class is responsible for ensuring that the mods have the correct version
 * ordering
 *
 * This is obviously attached to the Location record, as that holds a mod version
 */
class Search_Listener_Location_Version extends Doctrine_Record_Listener{
    public static function versionCmp($a, $b){
        return version_compare($b['version'], $a['version']);
    }
    /**
     * Sets the default value pre insert. This may not be needed
     *
     * @param Doctrine_Event $e
     */
    public function preSave(Doctrine_Event $e){
        $loc = $e->getInvoker();
        $loc->int_version = 0;
    }

    /**
     * Compares the version of the locations for a mod, and orders it so that
     * the highest mod version has a int_version of 0, with lower versions having
     * higher int_versions
     *
     * @param Doctrine_Event $e
     */
    public function postSave(Doctrine_Event $e){
        $loc = $e->getInvoker();

        $rows = Doctrine_Query::create()
                    ->select('mod_url_suffix, version, int_version')
                    ->from('Location')
                    ->where('modification_id = ?', $loc->modification_id)
                    ->fetchArray();

        //TODO. This sort isn't stable, which would result in more queries than
        //required. Maybe this should be insertion sort.
        usort($rows, array('Search_Listener_Location_Version', 'versionCmp'));
        for ( $i = 0; $i < count($rows); $i++ ){
            $row = $rows[$i];
            if ( $row['int_version'] != $i ){
                Doctrine_Query::create()
                            ->update('Location')
                            ->set('int_version', $i)
                            ->where('modification_id = ?', $loc->modification_id)
                            ->andWhere('mod_url_suffix = ?', $row['mod_url_suffix'])
                            ->execute();
            }
        }
    }

}