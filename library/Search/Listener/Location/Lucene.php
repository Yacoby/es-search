<?php
/**
 * This is responsible for updating the search database when the locations
 * change. This is probably a really inefficant way of doing things as it
 * requires far more SQL queries.... but it probably doesn't matter
 */
class Search_Listener_Location_Lucene extends Doctrine_Record_Listener{

    private function getRowFromLocation($l){
        return Doctrine_Query::create()
                                ->select('m.*, l.*, g.*')
                                ->from('Modification m, m.Locations l, m.Games g')
                //TODO Maybe just add an id in for locations
                                ->where('l.modification_id = ?', $l->modification_id)
                                ->andWhere('l.mod_url_suffix = ?', $l->mod_url_suffix)
                                ->fetchOne();

    }

    public function postSave(Doctrine_Event $e){
        //echo 'Adding to DB';
        /*
         * Fails on new locations
        if ( !$e->getInvoker()->isModified() ){
          
            return;
        }
         *
         */
        Search_Lucene_Db::staticAddMod($this->getRowFromLocation($e->getInvoker()));
    }

    public function postDelete(Doctrine_Event $e){
        Search_Lucene_Db::staticRemoveMod($this->getRowFromLocation($e->getInvoker()));
    }
}
