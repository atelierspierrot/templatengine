<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine;

/**
 * Minifier Adapters interface
 *
 * All Minifier adapters must extend this abstract class and defines its abstract methods
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractMinifierAdapter
{

	/**
	 * The file extension for destination file guessing
	 */
	public $file_extension;

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