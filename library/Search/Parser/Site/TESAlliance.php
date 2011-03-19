<?php
final class tes_alliance extends Search_Parser_Site {
    protected $_details = array(
        'host'            => 'tesalliance.org',
        'domain'          => null,
        'modUrlPrefix'    => '/forums/index.php?/files/file/',
        'initialPages'    => array(
                "/forums/index.php?/files/",
        ),
        'updateUrl'       => array(),
        'updateFrequency' => 31,
        'loginRequired'   => false,
        'limitBytes'      => 100100,
    );
}

final class tes_alliance_page extends Search_Parser_Page {

    protected function doIsValidModPage($url) {
        $pages = array(
                'http://tesalliance\.org/forums/index\.php\?/files/file/[0-9]+.*/'
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        $pages = array(
                'http://tesalliance\.org/forums/index\.php\?/files/category/[0-9+].*/',
                'http://tesalliance.org/forums/index.php\?/files/category/[0-9]+.*/page__sort_by__DESC__sort_key__file_submitted__num__10__st__[0-9]+'
        );
        return $this->isAnyMatch($pages, $url);
    }

    public function getGame() {
        $crumb = $this->_html->find('#breadcrumb li a',2);
        if ( $crumb === null ){
            return null;
        }
        $game = trim(str_replace(' Mods', '', $crumb->plaintext));

        $validGames = array(
            'Oblivion'  => 'OB',
            'Morrowind' => 'MW',
        );

        if (array_key_exists($game, $validGames) ){
            return $validGames[$game];
        }
        return null;
    }

    public function getName() {
        $crumb = $this->_html->find('#breadcrumb',0);
        if ( $crumb === null ){
            return null;
        }
        $name = html_entity_decode($crumb->lastChild()->plaintext);
        $name = str_replace(0xa0, ' ' , $name);
        return trim($name, '> ');
    }
    public function getAuthor() {
        return trim($this->_html->find('.submitter_name a',0)
                         ->plaintext);

    }
    public function getCategory() {
        return trim($this->_html->find('#breadcrumb',0)
                         ->lastChild()
                         ->previousSibling()
                         ->find('a',0)
                         ->plaintext);
    }
    public function getDescription() {
        return trim($this->_html->find('.description',0)
                           ->plaintext);
    }


}
