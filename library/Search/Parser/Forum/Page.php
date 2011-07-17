<?php

abstract class Search_Parser_Forum_Page extends Search_Parser_Location_AbstractPage{

    public function parsePage($client){
        $this->isThread($this->_url) ? $this->parseThread() : $this->parseIndex();
    }

    protected function parseThread(){
        $this->addMod(array(
            'Name'        => html_entity_decode($this->getName()),
            'Author'      => html_entity_decode($this->getAuthor()),
            'Description' => html_entity_decode($this->getDescription()),
        ));
    }

    abstract protected function parseIndex();

    /**
     * @return true if the given url is a thread, or a index
     */
    abstract protected function isThread(Search_Url $url);

    abstract protected function getName();
    abstract protected function getAuthor();
    abstract protected function getDescription();


}
