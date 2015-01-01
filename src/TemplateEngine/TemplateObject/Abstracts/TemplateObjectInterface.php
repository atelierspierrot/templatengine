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

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface TemplateObjectInterface
{

    /**
     * Init the object
     */
    public function init();

    /**
     * Reset the object
     * @return self Must return the object itself for method chaining
     */
    public function reset();

    /**
     * Add an entry
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function add( $arg );

    /**
     * Set a stack of entries
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function set( array $arg );

    /**
     * Get the stack of entries
     */
    public function get();

    /**
     * Write the Template Object strings ready for template display
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string Must return a string ready to write
     */
    public function write( $mask='%s' );

}

// Endfile