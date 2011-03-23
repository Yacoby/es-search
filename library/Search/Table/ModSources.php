<?php
class Search_Table_ModSources extends Search_Table_Abstract {
    public function __construct($conn = null){
        parent::__construct('ModSource',$conn);
    }
}
