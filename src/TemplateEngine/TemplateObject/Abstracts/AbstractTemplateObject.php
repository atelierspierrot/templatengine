<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject\Abstracts;

use TemplateEngine\Template;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractTemplateObject
{

	/**
	 * The whole template object
	 */
	protected $__template;
	
	/**
	 * Constructor
	 * @param Template $_tpl The whole template object
	 */
	public function __construct( Template $_tpl )
	{
		$this->__template = $_tpl;
		$this->init();
	}

	/**
	 * Write the Template Object strings ready for template display
	 */
	public function __toString()
	{
		return $this->write();
	}

	/**
	 * Clean a stack (an array) leaving just one set of an entry for the $clean_by variable
	 * @param array $stack The stack to clean
	 * @param string $clean_by The variable name to check
	 * @return array Return the stack cleaned with only one instance of $clean_by
	 */
	protected function cleanStack(array $stack, $clean_by = null)
	{
		$new_stack = array();
		foreach($stack as $_entry) {
			if (is_array($_entry) && !empty($clean_by)) {
				if (isset($_entry[$clean_by]) && !array_key_exists($_entry[$clean_by], $new_stack))
					$new_stack[ $_entry[$clean_by] ] = $_entry;
			} elseif (is_string($_entry)) {
				$ok = array_search($_entry, $new_stack);
				if (false===$ok)
					$new_stack[] = $_entry;
			}
		}
		return array_values($new_stack);
	}

	/**
	 * Build a stack (an array) leaving just one value of an entry searching a $clean_by index
	 * @param array $stack The stack to clean
	 * @param string $clean_by The variable name to check
	 * @return array Return the extracted stack
	 */
	protected function extractFromStack(array $stack, $clean_by)
	{
		$new_stack = array();
		foreach($stack as $_entry) {
			if (is_array($_entry) && isset($_entry[$clean_by])) {
				$new_stack[] = $_entry[$clean_by];
			}
		}
		return $new_stack;
	}

}

// Endfile