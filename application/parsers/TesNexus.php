<?php 
require_once dirname(__FILE__) . '/super/Nexus.php';

final class TesNexusPage extends NexusPage{
    public function __construct($response) {
        parent::__construct($response, new Search_Url('http://www.tesnexus.com'));
    }
}
