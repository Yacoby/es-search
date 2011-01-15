<?php
class Search_Table_Abstract extends Doctrine_Table{
    public function  __construct($name, $conn) {
        parent::__construct(
                        $name,
                        $conn ? $conn : Doctrine_Manager::getInstance()
                                                          ->getCurrentConnection(),
                        true
                );
    }

}