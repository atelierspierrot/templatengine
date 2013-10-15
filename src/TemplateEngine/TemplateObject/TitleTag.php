<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject;

use TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject,
    TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface,
    Library\Helper\Html;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TitleTag extends AbstractTemplateObject implements TemplateObjectInterface
{

// ------------------------
// TemplateObjectInterface
// ------------------------

	/**
	 * Init the object
	 */
	public function init()
	{
		$this->reset();
	}

	/**
	 * Reset the object
	 * @return self $this for method chaining
	 */
	public function reset()
	{
		$this->__template->registry->header_title = array();
		return $this;
	}

	/**
	 * Add a string to build page header title
	 * @param string $title The title string
	 * @return self $this for method chaining
	 */
	public function add($title)
	{
		$this->__template->registry->addEntry( $title, 'header_title');
		return $this;
	}

	/**
	 * Set a strings stack to build page header title
	 * @param array $strs An array of title strings
	 * @return self $this for method chaining
	 * @see self::add()
	 */
	public function set(array $strs)
	{
		if (!empty($strs)) {
			foreach($strs as $_str) {
				$this->add( $_str );
			}
		}
		return $this;
	}

	/**
	 * Get the titles stack
	 * @return array The stack of title strings
	 */
	public function get()
	{
		return $this->__template->registry->getEntry( 'header_title', false, array() );
	}

	/**
	 * Write the Template Object strings ready for template display
	 * @param string $mask A mask to write each line via "sprintf()"
	 * @return string The string to display fot this template object
	 */
	public function write($mask = '%s')
	{
		$str='';
		foreach($this->cleanStack( $this->get() ) as $entry) {
			$str .= (strlen($str)>0 ? $this->separator : '').$entry;
		}
		$title_str = Html::writeHtmlTag( 'title', strip_tags($str) );
		return sprintf($mask, $title_str);
	}

// ------------------------
// Custom methods
// ------------------------

	public $separator = ' - ';

	/**
	 * Set a separator string (used to join each title strings)
	 * @param string $str The string separator
	 * @return self $this for method chaining
	 */
	public function setSeparator($str)
	{
		$this->separator = $str;
		return $this;
	}

}

// Endfile