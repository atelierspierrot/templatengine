<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine;

use Patterns\Commons\Registry;

use Library\Helper\File as FileHelper,
    Library\Helper\Filesystem as FilesystemHelper;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Template
{

	public $registry;
	
	protected $web_root_path;
	protected $cache_path;
	protected $assets_cache_path;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::init();
	}

	/**
	 * Initialization of different register stacks
	 */
	protected function init()
	{
		$this->registry = new Registry;
		$this->registry->template_objects = array();
	}

// --------------------------
// Global Setters / Getters
// --------------------------

	/**
	 * Set the web root path
	 * @param string $path The path to the web root directory
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function setWebRootPath( $path )
	{
		if (@file_exists($path) && is_dir($path)) {
			$this->web_root_path = realpath($path).'/';
		} else {
			throw new \InvalidArgumentException(
				sprintf('Web root path "%s" was not found or is not a directory!', $path)
			);
		}
		return $this;
	}

	/**
	 * Get the web root path
	 * @return string The object web root path
	 */
	public function getWebRootPath()
	{
		return $this->web_root_path;
	}
	
	/**
	 * Set the cache path (absolute or relative form the WebRootPath)
	 * @param string $path The path to the web cache directory
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function setCachePath( $path )
	{
		if (@file_exists($path) && is_dir($path)) {
			$this->cache_path = realpath($path).'/';
		} elseif (null!==$path_rp = $this->findRealPath($path)) {
			$this->cache_path = rtrim($path_rp, '/').'/';
		} else {
			throw new \InvalidArgumentException(
				sprintf('Cache path "%s" was not found or is not a directory!', $path)
			);
		}
		return $this;
	}

	/**
	 * Get the web cache path
	 * @return string The object web cache path
	 */
	public function getCachePath()
	{
		return $this->cache_path;
	}
	
	/**
	 * Set the cache path for assets (absolute or relative form the WebRootPath)
	 * @param string $path The path to the web assets cache directory
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the path doesn't exist
	 */
	public function setAssetsCachePath( $path )
	{
		if (@file_exists($path) && is_dir($path)) {
			$this->assets_cache_path = realpath($path).'/';
		} elseif (null!==$path_rp = $this->findRealPath($path)) {
			$this->assets_cache_path = rtrim($path_rp, '/').'/';
		} else {
			throw new \InvalidArgumentException(
				sprintf('Assets cache path "%s" was not found or is not a directory!', $path)
			);
		}
		return $this;
	}

	/**
	 * Get the web cache path for assets
	 * @return string The object web assets cache path
	 */
	public function getAssetsCachePath()
	{
		return $this->assets_cache_path;
	}
	
// --------------------------
// TemplateObjects
// --------------------------

	/**
	 * Get a template object and create it if so
	 * @param string $_type The template object type
	 * @return object The template object if found
	 */
	public function getTemplateObject($_type, $_ref = null)
	{
        $stack_name = !is_null($_ref) ? $_ref : $this->getTemplateObjectClassName( $_type );

		if (!$this->registry->isEntry( $stack_name, 'template_objects' )) {
			$this->createNewTemplateObject( $_type, $_ref );
		}

		return $this->registry->getEntry( $stack_name, 'template_objects' );
	}

	/**
	 * Create a new template object and reference it in the registry
	 * @param string $_type The template object type
	 * @return void
	 */
	public function createNewTemplateObject($_type, $_ref = null)
	{
		$_cls = $this->getTemplateObjectClassName( $_type );
        $stack_name = !is_null($_ref) ? $_ref : $_cls;

		if (class_exists($_cls)) {
			try {
				$_tpl_object = new $_cls( $this );
			} catch ( \Exception $e ) {
				throw new \RuntimeException(
					sprintf('An error occured while trying to create Template Object "%s"!', $_cls)
				);
			}
			if (!($_tpl_object instanceof \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject)) {
				throw new \DomainException(
					sprintf('A Template Object must extends the "\TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject" class (got "%s")!', $_cls)
				);
			} else {
				$this->registry->setEntry( $stack_name, $_tpl_object, 'template_objects' );
			}
		} else {
			throw new \RuntimeException(
				sprintf('Template Object for type "%s" doesn\'t exist!', $_type)
			);
		}
	}

	/**
	 * Get a template object class name
	 * @param string $_type The template object type
	 * @return string The template object class name
	 */
	public function getTemplateObjectClassName( $_type )
	{
		return '\TemplateEngine\TemplateObject\\'.ucfirst($_type);
	}

// --------------------------
// Utilities
// --------------------------

	/**
	 * Find an asset file relative path web ready
	 * @param string $file_path The file path to search
	 * @return string The relative web ready path for this file, null otherwise
	 */
	public function findAsset( $file_path )
	{
		$real_path = $this->findRealPath( $file_path );
		if ($real_path) {
			return trim(FilesystemHelper::resolveRelatedPath($this->web_root_path, $real_path), '/');
		}
		return null;
	}

	/**
	 * Find a file absolute path in application
	 * @param string $file_path The file path to search
	 * @return string The absolute path for this file, null otherwise
	 */
	public function findRealPath( $file_path )
	{
		if (@file_exists($file_path)) {
			return realpath($file_path);
		}
		if (@file_exists($this->web_root_path.$file_path)) {
			return $this->web_root_path.$file_path;
		}
		return null;
	}

}

// Endfile