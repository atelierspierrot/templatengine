<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject;

use \TemplateEngine\TemplateObject\Abstracts\AbstractFileTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\FileTemplateObjectInterface;
use \Library\Helper\Html;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class JavascriptFile extends AbstractFileTemplateObject implements FileTemplateObjectInterface
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
		$this->__template->registry->javascript_files = array();
		$this->__template->registry->javascript_minified_files = array();
		return $this;
	}

	/**
	 * Add a javascript file in javascripts stack
	 * @param string $file_path The new javascript path
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function add( $file_path )
	{
		$_fp = $this->__template->findAsset($file_path);
		if ($_fp)
		{
			$this->__template->registry->addEntry( array(
				'file'=>$_fp
			), 'javascript_files');
		}
		else
		{
			throw new \InvalidArgumentException(
				sprintf('Javascript file "%s" not found!', $file_path)
			);
		}
		return $this;
	}

	/**
	 * Set a full javascripts stack
	 * @param array $files An array of javascript files paths
	 * @return self $this for method chaining
	 * @see self::add()
	 */
	public function set( array $files )
	{
		if (!empty($files))
		{
			foreach($files as $_file)
			{
				if (is_array($_file) && isset($_file['file']))
					$this->add( $_file['file'] );
				elseif (is_string($_file))
					$this->add( $_file );
			}
		}
		return $this;
	}

	/**
	 * Get the javascript files stack
	 * @return array The stack of javascripts
	 */
	public function get()
	{
		return $this->__template->registry->getEntry( 'javascript_files', false, array() );
	}

	/**
	 * Write the Template Object strings ready for template display
	 * @param string $mask A mask to write each line via "sprintf()"
	 * @return string The string to display fot this template object
	 */
	public function write( $mask='%s' )
	{
		$str='';
		foreach($this->cleanStack( $this->get(), 'file' ) as $entry)
		{
			$tag_attrs = array(
				'type'=>'text/javascript',
				'src'=>$entry['file']
			);
			$str .= sprintf($mask, Html::writeHtmlTag( 'script', null, $tag_attrs ));
		}
		return $str;
	}

// ------------------------
// FileTemplateObjectInterface
// ------------------------

	/**
	 * Minify the files if possible and loads them in files_minified stack
	 * @return self Must return the object itself for method chaining
	 */
	public function minify()
	{
		$js_files = $this->cleanStack( $this->get(), 'file' );
		$cleaned_stack = $this->extractFromStack( $js_files, 'file' );
		if (!empty($cleaned_stack))
			$this->addMinified( 
				$this->minifyStack( $cleaned_stack )
			);
		return $this;
	}

	/**
	 * Add an minified file
	 * @param string $file_path The new javascript path
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function addMinified( $file_path )
	{
		$_fp = $this->__template->findAsset($file_path);
		if ($_fp)
		{
			$this->__template->registry->addEntry( array(
				'file'=>$_fp
			), 'javascript_minified_files');
		}
		else
		{
			throw new \InvalidArgumentException(
				sprintf('Javascript minified file "%s" not found!', $file_path)
			);
		}
		return $this;
	}

	/**
	 * Set a stack of minified files
	 * @param array $files An array of javascript files paths
	 * @return self $this for method chaining
	 * @see self::add()
	 */
	public function setMinified( array $files )
	{
		if (!empty($files))
		{
			foreach($files as $_file)
			{
				if (is_array($_file) && isset($_file['file']))
					$this->add( $_file['file'] );
				elseif (is_string($_file))
					$this->add( $_file );
			}
		}
		return $this;
	}

	/**
	 * Get the stack of minified files
	 * @return array The stack of javascripts
	 */
	public function getMinified()
	{
		return $this->__template->registry->getEntry( 'javascript_minified_files', false, array() );
	}

	/**
	 * Write minified versions of the files stack in the cache directory
	 */
	public function writeMinified( $mask='%s' )
	{
		$str='';
		foreach($this->cleanStack( $this->getMinified(), 'file' ) as $entry)
		{
			$tag_attrs = array(
				'type'=>'text/javascript',
				'src'=>$entry['file']
			);
			$str .= sprintf($mask, Html::writeHtmlTag( 'script', null, $tag_attrs ));
		}
		return $str;
	}

}

// Endfile