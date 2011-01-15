<?php

class Search_Table_Categories extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Category',$conn);
    }

}