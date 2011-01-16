<?php
/**
 * This is responsible for updating the search database when the locations
 * change. This is probably a really inefficant way of doing things as it
 * requires far more SQL queries.... but it probably doesn't matter
 */
class Search_Listener_Location_Lucene extends Doctrine_Record_Listener{

    /**
     * Gets a record containing the mod and game information about a mod
     * with the id $id.
     *
     * @param int $id  The mod id
     * @return Doctrine_Record
     */
    private function getRowFromModId($id){
        return Doctrine_Query::create()
                                ->select('m.*, g.*')
                                ->from('Modification m')
                                ->leftJoin('m.Games g')
                                ->where('m.modification_id = ?', $id)
                                ->fetchOne();
    }
    /**
     * Gets a record containing mod, game and location information for the
     * modification matcing the given location $l
     *
     * @param Doctrine_Record $l
     * @return Doctrine_Record
     */
    private function getRowFromLocation($l){
        return Doctrine_Query::create()
                                ->select('m.*, l.*, g.*')
                                ->from('Modification m, m.Locations l, m.Games g')
                                ->where('l.modification_id = ?', $l->modification_id)
                                ->fetchOne();

    }

    public function postSave(Doctrine_Event $e){
        Search_Lucene_Db::staticAddOrUpdateMod(
                $this->getRowFromLocation($e->getInvoker())
        );
    }

    public function postDelete(Doctrine_Event $e){
        $l = $e->getInvoker();

        $count = Doctrine_Query::create()
                            ->select('COUNT(modification_id) as c')
                            ->from('Location')
                            ->where('modificaton_id = ?', $l->modification_id)
                            ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        if ( $count == 0 ){
            Search_Lucene_Db::staticRemoveMod(
                    $this->getRowFromModId($l->modification_id)
            );
        }else{
            Search_Lucene_Db::staticAddOrUpdateMod($this->getRowFromLocation($l));
        }
    }
}
