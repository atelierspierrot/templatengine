<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace TemplateEngine\TemplateObject\Abstracts;

/**
 * @author  piwi <me@e-piwi.fr>
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