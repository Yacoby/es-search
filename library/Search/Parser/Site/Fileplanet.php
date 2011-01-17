<?php
class Fileplanet extends Search_Parser_Site {

    protected $_details = array(
        'host'            => 'www.fileplanet.com',
        'domain'          => null,
        'modUrlPrefix'    => '/',
        'initialPages'    => array(),
        'updateUrl'       => array(
            '/41184/0/section/Mods',
            '/104054/0/section/Mods',
        ),
        'updateFrequency' => 31,
        'loginRequired'   => false,
        'limitBytes'      => 1048578,
    );

}


class Fileplanet_page extends Search_Parser_Page {
    /**
     * The only change in the derived function is it only finds links in #main
     */
    protected function getPageLinks() {
        foreach( $this->_html->find('#main a') as $a ) {
            $url = new Search_Url(html_entity_decode($a->href), $this->_url);
            $url = $this->preAddLink($url);

            if ( !$url->isValid() || $url->toString() == $this->_url->toString() ) {
                continue;
            }

            if ( $this->isValidModPage($url) || $this->isValidPage($url) ) {
                $this->_links[] = $url;
            }
        }
    }


    protected function doIsValidModPage($url) {
        return preg_match('%http://www.fileplanet.com/[0-9]+/[0-9]+/fileinfo/%', (string)$url) == 1;
    }

    protected function doIsValidPage($url) {
        return preg_match('%http://www.fileplanet.com/[0-9]+/0/0/0/[0-9]+/section/%', (string)$url) == 1;
        $pages = array(
            'http://www.fileplanet.com/[0-9]+/0/0/0/[0-9]+/section/',
            'http://www.fileplanet.com/[0-9]+/0/section/',
        );
        return $this->isAnyMatch($pages, $url);
    }

    public function  preAddLink(Search_Url $url) {
        return new Search_Url(
            preg_replace(
                '%http://www.fileplanet.com/([0-9]+)/0/section/%',
                'http://www.fileplanet.com/$1/0/0/0/1/section/',
                (string)$url
            )
        );
    }

    function getGame() {
        foreach ( $this->_html->find('.col-24 .smaller a') as $e ){
            if ( $e->plaintext == 'Elder Scrolls III: Morrowind' ){
                return 'MW';
            }else if ( $e->plaintext == 'Elder Scrolls IV: Oblivion' ){
                return 'OB';
            }
        }
        return null;
    }

    function getCategory() {
        foreach ( $this->_html->find('.col-24 .smaller') as $e ){
            return $e->lastChild()->plaintext;
        }
        return null;
    }

    function getName() {
        foreach ( $this->_html->find('h2.section-title') as $e ){
            if ( preg_match('%File Info: .* - (.*)%', $e->plaintext, $regs) == 1 ) {
                return $regs[1];
            }
        }
        return null;
    }

    function getAuthor() {
        foreach ( $this->_html->find('.alpha') as $e ){
            if ( $e->plaintext == 'Author:' ){
                return $e->nextSibling()->plaintext;
            }
        }
        return null;
    }

    function getDescription() {
        foreach ( $this->_html->find('h2.section-title') as $e ){
            if ( strpos($e->plaintext, 'Description:') !== false ){
                return substr($e->parent()->plaintext,
                              strlen($e->plaintext));
            }
        }
        return null;
    }

}