<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject\Abstracts;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
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
	 * @return self Must return the object itself for method chaining
	 */
	public function add( $arg );

	/**
	 * Set a stack of entries
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