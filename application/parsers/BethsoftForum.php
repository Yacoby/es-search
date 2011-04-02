<?php

class BethsoftForumPage extends Search_Parser_Forum_Page{

    private function getPost(){
        $post = $this->_html->find('#content .post_wrap',0);
        if ( $post == false ){
            throw new Exception('There');
        }
        return $post;
    }

    private function getTitleFromText($title){
        if ( preg_match('%\[RELz\](.+)%', (string)$title, $matches) == 0 ){
            return false;
        }
        return trim($matches[1]);
    }

    private function isModRelease($title){
        return $this->getTitleFromText($title) !== false;
    }

    protected function getAuthor() {
        return $this->getPost()->find('h3 .author a',0)->plaintext;
    }
    protected function getDescription() {
        return $this->getPost()->find('.post_body .entry-content',0)->plaintext;
    }
    protected function getName() {
        $eall  = $this->_html->find('.main_topic_title',0);
        $edesc = $eall->find('.main_topic_desc', 0);
        if ( $edesc == false ){
            return $eall->plaintext;
        }

        $fullText = trim($eall->plaintext);
        $descText = trim($edesc->plaintext);

        $name = substr($fullText,
                       0,
                       strlen($fullText) - strlen($descText));
        $name = trim($name, " \r\n");
        
        return $this->getTitleFromText($name);
    }

    protected function isThread(Search_Url $url) {
        return preg_match(
            '%^http://forums\.bethsoft\.com/index\.php\?/topic/[0-9]+.*/$%',
            (string)$url
            ) == 1;
    }

    protected function parseIndex() {
        $threads = $this->_html->find('.topic_list tr a.topic_title');
        foreach ( $threads as $e ){
            if ( $this->isModRelease($e->plaintext) ){
                $this->addLink($a->href);
            }
        }
    }

    public function isModNotFoundPage($client) {
        $result = $this->_html->find('#content h2',0);
        if ( $result == false ){
            return false;
        }

        $text = $this->_html->find('#content h2',0)->plaintext;
        return $text == 'An Error Occurred';
    }
}