<?php

final class YacobyPage extends Search_Parser_Site_Page {
	/**
	 * Gets data for checking which pages are valid
	 */
	protected function doIsValidModPage($url) {
		if ( $url->toString() == "http://yacoby.silgrad.com/MW/Mods/index.htm" ){
			return false;
                }
		return (preg_match("%http://yacoby\\.silgrad\\.com/MW/Mods/\\w*\\.htm%", $url->toString()) == 1 );
	}

	protected  function doIsValidPage($url) {
		return false;
	}

	public function getGame() {
		return "MW";
	}
	public function getName() {
		$r = $this->_html->find(".modTitle", 0);
		if ( isset($r->plaintext) ){
			return $r->plaintext;
		}
		return null;
	}
	public function getAuthor() {
		return "Yacoby";
	}
	public function getDescription() {
		$d = "";
		foreach ( $this->_html->find(".content p") as $p ){
			$d .= $p->innertext;
		}
		return self::getDescriptionText($d);
	}

}

