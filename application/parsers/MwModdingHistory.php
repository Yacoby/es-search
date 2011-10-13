<?php


class ModdingHistory extends Search_Parser_AbstractScraper{

    private function normalise($str){
        #when it doubt, decode everything. God knows how it was encoded
        $str = urldecode($str);
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        $str = Search_Parser_Util::html_entity_decode_numeric($str);
        return stripslashes($str);
    }

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

            $name = $this->normalise((string)$attrs['title']);
            $name = new Search_Unicode(preg_replace('/-\d+$/u', '', $name));

            $author = new Search_Unicode($this->normalise((string)$mod->author));
            $description = new Search_Unicode($this->normalise((string)$mod->description));

            #some things seem to end in -209820482 or simalar

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
