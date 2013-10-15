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
    Library\Helper\Html,
    Library\Helper\ConditionalComment;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class MetaTag
    extends AbstractTemplateObject
    implements TemplateObjectInterface
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
	 *
	 * @return self $this for method chaining
	 */
	public function reset()
	{
		$this->__template->registry->meta_tags = array();
		return $this;
	}

	/**
	 * Add a meta tag in metas stack
	 *
	 * @param string $name The meta tag name
	 * @param string|array $content The meta tag content, can be defined as an array which will be joined (for keywords for example)
	 * @param bool $http_equiv Is this meta tag written as "http-equiv" (false by default)
	 * @param string|null $condition Define a condition (for IE) for this stylesheet
	 *
	 * @return self $this for method chaining
	 */
	public function add($name, $content = '', $http_equiv = false, $condition = null)
	{
		$this->__template->registry->addEntry( array(
			'name'=>$name,
			'content'=>$content,
			'http-equiv'=>$http_equiv,
			'condition'=>$condition
		), 'meta_tags');
		return $this;
	}

	/**
	 * Set a full array of tags
	 *
	 * @param array $tags An array of tags to add
	 *
	 * @return self $this for method chaining
	 *
	 * @see self::add()
	 */
	public function set(array $tags)
	{
		if (!empty($tags)) {
			foreach($tags as $_tag) {
				if (is_array($_tag) && isset($_tag['name']) && isset($_tag['content'])) {
					$this->add(
					    $_tag['name'],
					    $_tag['content'],
					    isset($_tag['http-equiv']) && true===$_tag['http-equiv'] ? true : false,
					    isset($_tag['condition']) ? $_tag['condition'] : null
					);
				}
			}
		}
		return $this;
	}

	/**
	 * Get the meta tags stack
	 *
	 * @return array The stack of meta tags definitions
	 */
	public function get()
	{
		return $this->__template->registry->getEntry( 'meta_tags', false, array() );
	}

	/**
	 * Write the Template Object strings ready for template display
	 *
	 * @param string $mask A mask to write each line via "sprintf()"
	 *
	 * @return string The string to display fot this template object
	 */
	public function write($mask = '%s')
	{
		$str='';
		foreach ($this->cleanStack( $this->get(), 'name' ) as $entry) {
			$tag_attrs = array();
			if (true===$entry['http-equiv'])
				$tag_attrs['http-equiv'] = $entry['name'];
			else
				$tag_attrs['name'] = $entry['name'];
			$tag_attrs['content'] = $entry['content'];
			$tag = Html::writeHtmlTag('meta', null, $tag_attrs, true);
			if (isset($entry['condition']) && !empty($entry['condition'])) {
			    $tag = ConditionalComment::buildCondition($tag, $entry['condition']);
			}
			$str .= sprintf($mask, $tag);
		}
		return $str;
	}

}

// Endfile