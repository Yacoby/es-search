<?php

class HouseFligg extends Search_Parser_AbstractScraper{

    private $_mods = array();
    public function mods(){
        return $this->_mods;
    }

    public function addMod(array $mod){
        $this->_mods[] = $mod;
    }

    public function scrape(){
        $url = 'http://www.fliggerty.com/ghf_hosted_mods.xml';
        $client = new Search_Parser_HttpClient();

        $response = $client->request(new Search_Url($url))
                           ->method('GET')
                           ->exec();

        $xml = new SimpleXMLElement($response->text());
        foreach ( $xml->mods->mod as $mod ){
            $this->addMod(array(
                        'Name'        => $mod['title'],
                        'Author'      => $mod['author'],
                        'Description' => $mod['description'],
                        'Game'        => $mod['game'],
                        'Version'     => $mod['version'],
                        'Url'         => $mod['url'],
             ));
        }
    }
}
