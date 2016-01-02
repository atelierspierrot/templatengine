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

namespace Assets\CompressorAdapter;

use \Assets\AbstractCompressorAdapter;

/**
 * @author  piwi <me@e-piwi.fr>
 */
class CSS
    extends AbstractCompressorAdapter
{

    public $file_extension = 'css';

    public static function merge($input)
    {
        $input = preg_replace('!/\*.*?\*/!s', '', $input);
        $output = trim($input);
        return $output;
    }

    /**
     * Inspired by <http://code.seebz.net/p/minify-css/>
     */
    public static function minify($input)
    {
        $input = self::merge($input);
        $input = str_replace(array("\r", "\n"), '', $input);
        $input = preg_replace('`([^*/])\/\*([^*]|[*](?!/)){5,}\*\/([^*/])`Us', '$1$3', $input);
        $input = preg_replace('`\s*({|}|,|:|;)\s*`', '$1', $input);
        $input = str_replace(';}', '}', $input);
        $input = preg_replace('`(?=|})[^{}]+{}`', '', $input);
        $input = preg_replace('`[\s]+`', ' ', $input);
        return $input;
    }

    public static function buildComment($str)
    {
        return sprintf('/* %s */', $str);
    }
}
