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

<?
class Helper_View_IncludeCss extends Helper_IncludeResources {
    public function includeCss($basePath, $production, $development, $version) {
        return $this->inlucdeResources(
                $basePath,
                $production,
                $development,
                $version,
                'generateCssInclude'
        );

    }

    protected function generateCssInclude($filePath) {
        return '<link type="text/css" href="'.$filePath.'" rel="stylesheet" />'."\n";
    }
}