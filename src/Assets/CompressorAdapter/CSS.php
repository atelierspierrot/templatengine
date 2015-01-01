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

namespace Assets\CompressorAdapter;

use \Assets\AbstractCompressorAdapter;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class CSS
    extends AbstractCompressorAdapter
{

    public $file_extension = 'css';

    public static function merge( $input )
    {
        $input = preg_replace('!/\*.*?\*/!s', '', $input);
        $output = trim($input);
        return $output;
    }

    /**
     * Inspired by <http://code.seebz.net/p/minify-css/>
     */
    public static function minify( $input )
    {
        $input = self::merge($input);
        $input = str_replace(array("\r","\n"), '', $input);
        $input = preg_replace('`([^*/])\/\*([^*]|[*](?!/)){5,}\*\/([^*/])`Us', '$1$3', $input);
        $input = preg_replace('`\s*({|}|,|:|;)\s*`', '$1', $input);
        $input = str_replace(';}', '}', $input);
        $input = preg_replace('`(?=|})[^{}]+{}`', '', $input);
        $input = preg_replace('`[\s]+`', ' ', $input);
        return $input;
    }

    public static function buildComment( $str )
    {
        return sprintf('/* %s */', $str);
    }

}

// Endfile