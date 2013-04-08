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

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class CssFile extends AbstractTemplateObject implements TemplateObjectInterface
{

	/**
	 * Init the object
	 */
	public function init()
	{
		$this->__template->registry->css_files = array();
	}

	/**
	 * Add a CSS file in CSS stack
	 * @param string $file_path The new CSS path
	 * @param string $media The media type for the CSS file (default is "screen")
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function add( $file_path, $media='screen' )
	{
		$_fp = $this->__template->findAsset($file_path);
		if ($_fp)
		{
			$this->__template->registry->addEntry( array(
				'file'=>$_fp, 'media'=>$media
			), 'css_files');
		}
		else
		{
			throw new \InvalidArgumentException(
				sprintf('CSS file "%s" not found!', $file_path)
			);
		}
	}

	/**
	 * Set a full CSS stack
	 * @param array $files An array of CSS files paths
	 * @param string $media The media type for the CSS file (default is "screen")
	 * @see self::add()
	 */
	public function set( array $files, $media='screen' )
	{
		if (!empty($files))
		{
			foreach($files as $_file)
			{
				$this->add( $_file, $media );
			}
		}
	}

	/**
	 * Get the CSS files stack
	 * @return array The stack of CSS
	 */
	public function get()
	{
		return $this->__template->registry->getEntry( 'css_files', false, array() );
	}

	/**
	 * Write the Template Object strings ready for template display
	 * @return string The string to display fot this template object
	 */
	public function write()
	{
		$str='';
		foreach($this->get() as $entry)
		{
			$str .= "\n"
				.'<link rel="stylesheet" type="text/css" href="'.$entry['file'].'"'
				.(isset($entry['media']) && !empty($entry['media']) ? ' media="'.$entry['media'].'"' : '')
				.' />';
		}
		return $str;
	}

}

// Endfile