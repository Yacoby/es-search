<?php

class HouseFligg extends Search_Parser_AbstractScraper{

    public function scrape(){
        $result = new Search_Parser_ScrapeResult();

        $url = 'http://www.fliggerty.com/ghf_hosted_mods.xml';
        $client = new Search_Parser_HttpClient();

        $response = $client->request(new Search_Url($url))
                           ->method('GET')
                           ->exec();

        $xml = simplexml_load_string($response->text());
        foreach ( $xml->mods->mod as $mod ){
            $result->addMod(array('Name'        => $mod['title'],
                                  'Author'      => $mod['author'],
                                  'Description' => $mod['description'],
                                  'Game'        => $mod['game'],
                                  'Version'     => $mod['version'],
                                  'Url'         => $mod['url'],));
        }
        return $result;
    }
}
