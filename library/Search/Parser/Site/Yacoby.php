<?php
/* l-b
 * This file is part of ES Search.
 *
 * Copyright (c) 2009 Jacob Essex
 *
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */

final class yacoby_silgrad_com extends Search_Parser_Site {
    protected $_details = array(
        'host'            => 'yacoby.silgrad.com',
        'domain'          => null,
        'modUrlPrefix'    => '/MW/Mods/',
        'initialPages'    => array(),
        'updateUrl'       => array('/MW/Mods/index.htm',),
        'updateFrequency' => 31,
        'loginRequired'   => false,
        'limitBytes'      => 1048578,
    );
}

final class yacoby_silgrad_com_page extends Search_Parser_Page {
	/**
	 * Gets data for checking which pages are valid
	 */
	protected function doIsValidModPage($url) {
		if ( $url->toString() == "http://yacoby.silgrad.com/MW/Mods/index.htm" )
			return false;
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

