<?php
interface Search_Updater_Interface{
    /**
     * Function should return an array containing a array under the index
     * 'NewUpdated' of mod details that are new or have changed and an array
     * under the index 'Deleted' of mods locations that should be removed.
     *
     * If a mod is to be added or deleted, the array should also contain an index
     * Source which is the numeric id of the source from which the mod comes
     */
    public function update();
}