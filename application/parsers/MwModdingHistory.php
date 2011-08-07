<?php

class ModdingHistory extends Search_Parser_AbstractScraper{

    public function scrape(){
        $result = new Search_Parser_ScrapeResult();

        $url = 'http://mw.modhistory.com/results.xml';
        $client = new Search_Parser_HttpClient();

        $response = $client->request(new Search_Url($url))
                           ->method('GET')
                           ->exec();

        $xml = simplexml_load_string($response->text());
        foreach ( $xml->mod as $mod ){
            $attrs = $mod->attributes();

            $result->addMod(array('Name'        => $attrs['title'],
                                  'Author'      => $mod->author,
                                  'Description' => $mod->description,
                                  'Game'        => 'MW',
                                  'Url'         => $mod->url,));
        }
        return $result;
    }
}
