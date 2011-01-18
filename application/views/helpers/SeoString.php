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
 * Converts a string into a string containing only valid charachters, converting
 * a space into - and stripping anything not a-zA-Z0-9
 */
class Zend_View_Helper_SeoString{
    public function seoString($str){
            $str = preg_replace('/[^a-zA-Z0-9\s]/', '', (string)$str);
            $str = preg_replace('/\s+/', ' ', $str);
            return str_replace(' ', '-', $str);
    }
}
