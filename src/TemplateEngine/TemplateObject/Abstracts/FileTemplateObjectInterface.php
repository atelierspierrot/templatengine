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

namespace TemplateEngine\TemplateObject\Abstracts;

use \TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface FileTemplateObjectInterface
    extends TemplateObjectInterface
{

    /**
     * Add an entry if file exists
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function addIfExists( $arg );

    /**
     * Merge the files if possible and loads them in files_merged stack
     * @return self Must return the object itself for method chaining
     */
    public function merge();

    /**
     * Add an merged file
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function addMerged( $arg );

    /**
     * Set a stack of merged files
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function setMerged( array $arg );

    /**
     * Get the stack of merged files
     */
    public function getMerged();

    /**
     * Write merged versions of the files stack in the cache directory
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string Must return a string ready to write
     */
    public function writeMerged( $mask='%s' );

    /**
     * Minify the files if possible and loads them in files_minified stack
     * @return self Must return the object itself for method chaining
     */
    public function minify();

    /**
     * Add an minified file
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function addMinified( $arg );

    /**
     * Set a stack of minified files
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function setMinified( array $arg );

    /**
     * Get the stack of minified files
     */
    public function getMinified();

    /**
     * Write minified versions of the files stack in the cache directory
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string Must return a string ready to write
     */
    public function writeMinified( $mask='%s' );

}

// Endfile