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
/**
 * Abstract class for writing classes to generate html to load resources
 */
abstract class Helper_IncludeResources extends Zend_View_Helper_Abstract {

    /**
     *
     * @param string $basePath
     *      The base path appended to the start of all files
     * 
     * @param array $production
     *      An array of files to be used if in production mode
     *
     * @param array $development
     *      An array of files to be used if in development mode
     *
     * @param string $version this is appended to the end of the url to support
     *              caching
     *
     * @param function $callback protected function to call to transform the file
     *              name into html
     *
     * @return string html
     */
    protected function inlucdeResources(
            $basePath,
            array $production,
            array $development,
            $version,
            $callback
    ) {
        $files = $development;
        if ( APPLICATION_ENV == 'production' ) {
            $files = $production;
        }

        $string = '';
        foreach ($files as $file ) {
            $string .= $this->{$callback}($basePath.$file.'?v='.$version);
        }
        return $string;
    }

}
