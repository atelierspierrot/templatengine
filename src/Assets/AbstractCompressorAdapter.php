<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyleft (ↄ) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace Assets;

/**
 * Compressor Adapters interface
 *
 * All Compressor adapters must extend this abstract class and defines its abstract methods
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractCompressorAdapter
{

    /**
     * The file extension for destination file guessing
     * @var string
     */
    public $file_extension;

    /**
     * Process of combination of a content (a merge)
     * @param string $input The string to merge
     * @return string Must return the input string merged
     */
    abstract public static function merge( $input );

    /**
     * Process of minification of a content
     * @param string $input The string to minify
     * @return string Must return the input string minified
     */
    abstract public static function minify( $input );

    /**
     * Build a comment string to insert in final content
     * @param string $str The comment string to add
     * @return string Must return the comment string according to adapter type
     */
    abstract public static function buildComment( $str );

}

// Endfile