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
 * Thrown when a defined function is called, but the function isn't implemented
 */
class UnimplementedException extends Exception{}

/**
 * Class that all mod databases should inherit from. There is no requirment to
 * implement all the listed functions. They were mainly put here to provide
 * documentation. Functions that do not exist in the inherited class are not
 * called.
 */
abstract class Search_Data_ModDatabase{
    /**
 * Tests if the subclass has a method that matches the given name.
 * It should not take into account parent methods
 *
 * @param string $method
 * @return bool
 */
    public final function hasMethod($method) {
        $class = new ReflectionClass(get_class($this));
        if ( !$class->hasMethod($method) ) {
            return false;
        }

        $func = new ReflectionMethod(get_class($this), $method);
        return $func->isPublic();

    }

    protected static function notImplemented($f = '(UNKNOWN)') {
        throw new UnimplementedException("Function $f not implemented");
    }

        /**
     * Searchs for the exact ID. There should only be a single database with this
     * function registered
     *
     * @param string $game
     * @param string $name
     * @param string $author
     * @return int
     */
    public function searchExact($game, $name, $author) {
        self::notImplemented(__FUNCTION__);
    }


    public function addMod($game, $mid, array $details) {
        self::notImplemented(__FUNCTION__);
    }

    /**
     * Removes a mod. Should fail silently if the mod doesn't exist
     *
     * @param string $game
     * @param int $mid
     */
    public function removeMod($game, $mid) {
        self::notImplemented(__FUNCTION__);
    }



}