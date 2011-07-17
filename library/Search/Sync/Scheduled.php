<?php

class Search_Sync_Scheduled{

    private $_sources, $_scheduled, $_factory;

    public function __construct(
            Search_Parser_Factory $factory,
            Search_Table_ModSources $sources  = null,
            Search_Table_ScheduledSources $ss = null
    ){
        assert($factory);

        $this->_factory   = $factory;
        $this->_sources   = $sources ? $sources : new Search_Table_ModSources();
        $this->_scheduled = $ss      ? $ss      : new Search_Table_ScheduledSources();
    }

    public function syncAll(){
        $this->ensureScheduledCreated();
        $this->ensureModSourcesCreated();
    }

    public function ensureScheduledCreated(){
        foreach ( $this->_factory->getNamesByBaseType('scheduled') as $name ){
            $parser    = $this->_factory->getScheduledByName($name);
            $scheduled = $this->_scheduled->findOneByName($name);
            if ( $scheduled === false ){
                $scheduled = $this->_scheduled->create();
            }

            $scheduled->name        = $name;
            $scheduled->hours_delta = $parser->getOption('hoursDelta');
            $scheduled->save();
        }
    }

    public function ensureModSourcesCreated(){
        foreach ( $this->_factory->getNamesByBaseType('scheduled') as $name ){
            $parser = $this->_factory->getScheduledByName($name);

            //if not created, create Mod Sources for each site
            $scheduled = $this->_scheduled->findOneByName($name);

            if ( $scheduled === false ){
                continue;
            }

            //check if we need to sync
            $source = null;
            if ( $scheduled->mod_source_id === null ){
                $source = $this->_sources->create();
                $source->save();
                //have to save the source before we can create a ref 
                //to it on the site
                //TODO FIX

                $scheduled->mod_source_id = $source->id;
                $scheduled->save();
            }else{
                $source = $this->_sources->findOneById($scheduled->mod_source_id);
                if ( $source === false ){
                    throw new Exception('Could not find Sites source');
                }
            }
            $source->url_prefix = $parser->getOption('urlPrefix');

            //ensure that the prefix is correct
            $source->save();
        }
    }

}
