<?php

class HouseFligg extends Search_Parser_AbstractScraper{

    public function scrape(){
        $result = new Search_Parser_ScrapeResult();

        $url = 'http://download.fliggerty.com/results.xml';
        $client = new Search_Parser_HttpClient();

        $response = $client->request(new Search_Url($url))
                           ->method('GET')
                           ->exec();

        $xml = simplexml_load_string($response->text());
        foreach ( $xml->mod as $mod ){
            $attrs = $mod->attributes();

            #when it doubt, decode everything. God knows how it was encoded
            $sname = urldecode((string)$attrs['title']);
            $sname = Search_Parser_Util::html_entity_decode_numeric($sname);
            $sname = html_entity_decode($sname, ENT_COMPAT, 'UTF-8');
            $sname = stripslashes($sname);

            $name   = new Search_Unicode($sname);
            $author = new Search_Unicode((string)$mod->author);
            $description = new Search_Unicode((string)$mod->description);
            $url = new Search_Url((string)$mod->url);

            if ( $author->getAscii() == '' ){
                continue;
            }

            $result->addMod(array('Name'        => $name, 
                                  'Author'      => $author,
                                  'Description' => $description,
                                  'Game'        => 'MW',
                                  'Url'         => $url,
                            ));
        }
        return $result;
    }
}
