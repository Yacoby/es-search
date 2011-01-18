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

/**
 * Helper class desinged to try and stop words getting cut off mid word
 */
class Zend_View_Helper_TrimDescription{

    /**
     * Ensures a string is under 255 characters. If it exceeds 255 characters
     * it takes the substring from 0 to the last space in the range 0 < x < 255
     *
     * @param string $str
     * @return string
     */
    public function trimDescription($str){
        $str = (string)$str;

        if ( strlen($str) < 255 ){
            return $str;
        }

        $index = strripos($str, ' ', 255-strlen($str));
        if ( $index === false ){
            return trim(substr($str,0,255)) . '...';
        }
        return trim(substr($str,0,$index)) . '...';
    }
}
