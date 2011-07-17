<?php

/**
 * Class for dealing with Scheduled classes
 */
class Search_Updater_Scheduled implements Search_Updater_Interface{

    protected $_factory;

    protected $_sources;

    protected $_scheduled;

    public function __construct(
            Search_Parser_Factory $factory,
            Search_Table_ScheduledSources $ss = null
    ) {
        $this->_factory   = $factory;
        $this->_scheduled = $ss ? $ss : new Search_Table_ScheduledSources();
    }

    public function update() {
        list($name, $source) = $this->getNameToUpdate();
        if ( $name === null ){
            return array();
        }
        $this->setUpdated($name);

        $parser = $this->_factory
                       ->getScheduledByName($name);

        $parser->parse();

        return array(
            'Source'     => $source,
            'NewUpdated' => $parser->mods(),
        );
    }

    /**
     * Checks the database for an parser that needs to be updated
     * @return an array containing the name of the class and the mod source id
     *          if there are no updates, it returns array(null, null)
     */
     protected function getNameToUpdate(){
         $row = $this->_scheduled->findOneByUpdateRequired();
         return $row !== false ? array($row->name, $row->mod_source_id)
                               : array(null, null);
     }

     protected function setUpdated($name){
         $row = $this->_scheduled->findOneByName($name);
         $row->last_run_time = time();
         $row->save();
     }
}
