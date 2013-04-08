<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\MinifierAdapter;

use \TemplateEngine\AbstractMinifierAdapter;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class CSS extends AbstractMinifierAdapter
{

	public $file_extension = 'css';

	/**
	 * Inspired by <http://code.seebz.net/p/minify-css/>
	 */
	public static function minify( $input )
	{
		$input = preg_replace('!/\*.*?\*/!s', '', $input);
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