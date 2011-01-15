<?php
/**
 * Cascades deleted locations upwards, so that if there are no locations
 * left, for a mod, the mod is removed
 */
class Search_Listener_Location_CascadeUpwards extends Doctrine_Record_Listener{
    public function postDelete(Doctrine_Event $e){
        $l = $e->getInvoker();
        $count = Doctrine_Query::create()
                            ->select('COUNT(modification_id) as c')
                            ->from('Location')
                            ->where('modificaton_id = ?', $l->modification_id)
                            ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        if ( $count == 0 ){
            Doctrine_Query::create()
                            ->delete()
                            ->from('Modification')
                            ->where('id=?', $l->modification_id)
                            ->execute();
        }
    }
}