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
class JS
    extends AbstractCompressorAdapter
{

    public $file_extension = 'js';

    public static function merge( $input )
    {
        $input = preg_replace('!/\*.*?\*/!s', '', $input);
        $output = trim($input);
        return $output;
    }

    /**
     * Inspired by <http://code.seebz.net/p/minify-js/>
     */
    public static function minify($input)
    {
        $output = '';
        $input = self::merge($input);

        $inQuotes        = array();
        $noSpacesAround  = '{}()[]<>|&!?:;,+-*/="\'';

        $input = preg_replace("`(\r\n|\r)`", "\n", $input);
        $inputs = str_split($input);
        $inputs_count = count($inputs);
        $prevChr = null;
        for ($i=0; $i<$inputs_count; $i++) {
            $chr = $inputs[$i];
            $nextChr = $i+1 < $inputs_count ? $inputs[$i+1] : null;

            switch ($chr) {
                case '/':
                    if (!count($inQuotes) && $nextChr == '*' && $inputs[$i+2] != '@') {
                        $i = 1 + strpos($input, '*/', $i);
                        continue 2;
                    } elseif (!count($inQuotes) && $nextChr == '/') {
                        if (strpos($input, "\n", $i))
                            $i = strpos($input, "\n", $i);
                        else
                            $i = strlen($input);
                        continue 2;
                    } elseif (!count($inQuotes)) {
                        $eolPos = strpos($input, "\n", $i);
                        if ($eolPos===false) $eolPos = $inputs_count;
                        $eol = substr($input, $i, $eolPos-$i);
                        if (!preg_match('`^(/.+(?<=\\\/)/(?!/)[gim]*)[^gim]`U', $eol, $m)) {
                            preg_match('`^(/.+(?<!/)/(?!/)[gim]*)[^gim]`U', $eol, $m);
                        }
                        // it's a RegExp
                        if (isset($m[1])) {
                            $output .= $m[1];
                            $i += strlen($m[1])-1;
                            continue 2;
                        }
                    }
                    break;

                case "'":
                case '"':
                    if ($prevChr != '\\' || ($prevChr == '\\' && $inputs[$i-2] == '\\')) {
                        if (end($inQuotes) == $chr) {
                            array_pop($inQuotes);
                        } elseif (!count($inQuotes)) {
                            $inQuotes[] = $chr;
                        }
                    }
                    break;

                case ' ':
                case "\t":
                case "\n":
                    if (!count($inQuotes)) {
                        if (   strstr("{$noSpacesAround} \t\n", $nextChr)
                            || strstr("{$noSpacesAround} \t\n", $prevChr)
                        ) {
                            continue 2;
                        }
                        $chr = ' ';
                    }
                    break;

                default:
                    break;
            }

            $output .= $chr;
            $prevChr = $chr;
        }
        $output = trim($output);
        $output = str_replace(';}', '}', $output);

        return $output;
    }

    public static function buildComment($str)
    {
        return sprintf('/* %s */', $str);
    }

}

// Endfile