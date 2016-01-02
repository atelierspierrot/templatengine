<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2016 Pierre Cassat <me@e-piwi.fr> and contributors
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

use \TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface;

/**
 * @author  piwi <me@e-piwi.fr>
 */
interface FileTemplateObjectInterface
    extends TemplateObjectInterface
{

    /**
     * Add an entry if file exists
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function addIfExists($arg);

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
    public function addMerged($arg);

    /**
     * Set a stack of merged files
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function setMerged(array $arg);

    /**
     * Get the stack of merged files
     */
    public function getMerged();

    /**
     * Write merged versions of the files stack in the cache directory
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string Must return a string ready to write
     */
    public function writeMerged($mask='%s');

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
    public function addMinified($arg);

    /**
     * Set a stack of minified files
     * @param mixed $arg
     * @return self Must return the object itself for method chaining
     */
    public function setMinified(array $arg);

    /**
     * Get the stack of minified files
     */
    public function getMinified();

    /**
     * Write minified versions of the files stack in the cache directory
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string Must return a string ready to write
     */
    public function writeMinified($mask='%s');
}
