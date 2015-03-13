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