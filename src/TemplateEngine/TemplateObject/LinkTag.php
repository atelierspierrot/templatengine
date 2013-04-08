<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject;

use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface;
use \Library\Helper\Html;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class LinkTag extends AbstractTemplateObject implements TemplateObjectInterface
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
		$this->__template->registry->link_tags = array();
		return $this;
	}

	/**
	 * Add a link header attribute
	 * @param array $tag_attributes The link tag attributes
	 * @return self $this for method chaining
	 */
	public function add( $tag_attributes )
	{
		if (!empty($tag_attributes))
		{
			$this->__template->registry->addEntry( $tag_attributes, 'link_tags');
		}
		return $this;
	}

	/**
	 * Set a full links header stack
	 * @param array $tags An array of tags definitions
	 * @return self $this for method chaining
	 * @see self::add()
	 */
	public function set( array $tags )
	{
		if (!empty($tags))
		{
			foreach($tags as $_tag)
			{
				$this->add( $_tag );
			}
		}
		return $this;
	}

	/**
	 * Get the header link tags stack
	 * @return array The stack of header link tags
	 */
	public function get()
	{
		return $this->__template->registry->getEntry( 'link_tags', false, array() );
	}

	/**
	 * Write the Template Object strings ready for template display
	 * @param string $mask A mask to write each line via "sprintf()"
	 * @return string The string to display fot this template object
	 */
	public function write( $mask='%s' )
	{
		$str='';
		foreach($this->cleanStack( $this->get(), 'rel' ) as $entry)
		{
			$str .= sprintf($mask, Html::writeHtmlTag( 'link', null, $entry, true ));
		}
		return $str;
	}

}

// Endfile