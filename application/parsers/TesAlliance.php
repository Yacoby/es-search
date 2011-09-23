<?php

final class TesAlliancePage extends Search_Parser_Site_Page{

    protected function doIsValidModPage($url) {
        $pages = array(
                'http://tesalliance\.org/forums/index\.php\?/files/file/[0-9]+.*/'
        );
        return $this->isAnyMatch($pages, $url);
    }

    protected function doIsValidPage($url) {
        $re = 'http://tesalliance\.org/'
            . 'forums/index\.php\?/files/category/[0-9+].*/';
        $pages = array(
            $re,
            $re . 'page__sort_by__DESC__sort_key__file_submitted__num__10__st__[0-9]+',
        );
        return $this->isAnyMatch($pages, $url);
    }

    public function getGame() {
        $html = $this->getResponse()->html();
        $crumb = $html->xpathOne('(//*[@id="breadcrumb"]//li//a)[3]');
        if ( $crumb == null ){
            return null;
        }
        $game = trim(str_replace(' Mods', '', $crumb->toString()->getAscii()));

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
        $html = $this->getResponse()->html();
        $name = $html->xpathOne('(//*[@id="breadcrumb"]/li)[last()]/text()');
        if ( $name == null ){
            return null;
        }
        $name = $name->toString();
        $name->htmlEntityDecode();
        $name->replace(0xa0, ' ');
        $name->trim(' >');
        return $name;
    }

    public function getAuthor() {
        $html = $this->getResponse()->html();
        $name = $html->xpathOne('//*[@class="submitter_name"]//a/text()')->toString();
        $name->trim();
        return $name;
    }

    public function getCategory() {
        $html = $this->getResponse()->html();
        $xp = '(//*[@id="breadcrumb"]/*)[last()-1]/a/text()';
        return trim($html->xpathOne($xp)->toString()->getAscii());
    }

    public function getDescription() {
        $html = $this->getResponse()->html();
        $text = $html->xpathOne('//*[@class="description"]/text()');
        return $text->toString();
    }
}
