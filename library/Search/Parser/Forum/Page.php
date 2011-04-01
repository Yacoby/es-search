<?php

abstract class Search_Parser_Forum_Page extends Search_Parser_Page_Abstract{

    public function parsePage(){
        $this->isThread() ? $this->parseThread() : $this->parseIndex();
    }

    protected function parseThread(){
        $this->addMod(array(
            'Name'        => $this->getName(),
            'Author'      => $this->getAuthor(),
            'Description' => $this->getDescription(),
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