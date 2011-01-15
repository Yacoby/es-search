<?php
class Search_Table_Locations extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('Location',$conn);
    }
}