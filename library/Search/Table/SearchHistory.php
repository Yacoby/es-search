<?php

class Search_Table_SearchHistory extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('SearchHistory',$conn);
    }

}