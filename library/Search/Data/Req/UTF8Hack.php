<?php /* l-b
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
 * l-b */ ?>

<?php
function translateUTF8($string) {
    $utf8 = array(
        '‚Ç¨', // ‚Ç¨
        '‚Äô', // ‚Äô
        '¬£', // ¬£
        'Ä', // Ä
        'Å', // Å
        'Ç', // Ç
        'É', // É
        'Ñ', // Ñ
        'Ö', // Ö
        'Ü', // Ü
        'á', // á
        'à', // à
        'â', // â
        'ä', // ä
        'ã', // ã
        'å', // å
        'ç', // ç
        'é', // é
        'è', // è
        'ê', // ê
        'ë', // ë
        'í', // í
        'ì', // ì
        'î', // î
        'ï', // ï
        'ñ', // ñ
        'ó', // ó
        'ò', // ò
        'ô', // ô
        'ö', // ö
        'õ', // õ
        'ú', // ú
        'ù', // ù
        'û', // û
        'ü', // ü
        '†', // †
        '°', // °
        '¢', // ¢
        '£', // £
        '§', // §
        '•', // •
        '¶', // ¶
        'ß', // ß
        '®', // ®
        '©', // ©
        '™', // ™
        '´', // ´
        '¨', // ¨
        '≠', // ≠
        'Æ', // Æ
        'Ø', // Ø
        '∞', // ∞
        '±', // ±
        '≤', // ≤
        '≥', // ≥
        '¥', // ¥
        'µ', // µ
        '∂', // ∂
        '∑', // ∑
        '∏', // ∏
        'π', // π
        '∫', // ∫
        'ª', // ª
        'º', // º
        'Ω', // Ω
        'æ', // æ
        'ø', // ø
    );

    $cp1252 = array(
        chr(128), // ‚Ç¨
        chr(146), // ‚Äô
        chr(163), // ¬£
        chr(192), // Ä
        chr(193), // Å
        chr(194), // Ç
        chr(195), // É
        chr(196), // Ñ
        chr(197), // Ö
        chr(198), // Ü
        chr(199), // á
        chr(200), // à
        chr(201), // â
        chr(202), // ä
        chr(203), // ã
        chr(204), // å
        chr(205), // ç
        chr(206), // é
        chr(207), // è
        chr(208), // ê
        chr(209), // ë
        chr(210), // í
        chr(211), // ì
        chr(212), // î
        chr(213), // ï
        chr(214), // ñ
        chr(215), // ó
        chr(216), // ò
        chr(217), // ô
        chr(218), // ö
        chr(219), // õ
        chr(220), // ú
        chr(221), // ù
        chr(222), // û
        chr(223), // ü
        chr(224), // †
        chr(225), // °
        chr(226), // ¢
        chr(227), // £
        chr(228), // §
        chr(229), // •
        chr(230), // ¶
        chr(231), // ß
        chr(232), // ®
        chr(233), // ©
        chr(234), // ™
        chr(235), // ´
        chr(236), // ¨
        chr(237), // ≠
        chr(238), // Æ
        chr(239), // Ø
        chr(240), // ∞
        chr(241), // ±
        chr(242), // ≤
        chr(243), // ≥
        chr(244), // ¥
        chr(245), // µ
        chr(246), // ∂
        chr(247), // ∑
        chr(248), // ∏
        chr(249), // π
        chr(250), // ∫
        chr(251), // ª
        chr(252), // º
        chr(253), // Ω
        chr(254), // æ
        chr(255), // ø
    );

    return str_replace($utf8, $cp1252, $string);
}

?>
