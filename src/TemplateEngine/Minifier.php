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
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Minifier
{

	protected $files_stack;
	protected $destination_dir;
	protected $destination_file;
	protected $minified_content;
	protected $silent;
	protected $direct_output;
	protected $web_root_path;

	protected $isCleaned_files_stack;
	protected $isInited;

	protected $__adapter_type;
	protected $__adapter;

	/**
	 * Construction of a new Minifier object
	 * @param null|array $files_stack An array of the files stack to treat
	 * @param null|string $destination_file The destination file name to write in
	 * @param null|string $destination_dir The destination directory to force creating a file with the result
	 * @param null|string $adapter_type The adapter type name (which will be guessed if empty)
	 */
	public function __construct(array $files_stack = array(), $destination_file = null, $destination_dir = null, $adapter_type = null)
	{
		$this->reset(true);
		if (!empty($files_stack))
			$this->setFilesStack( $files_stack );
		if (!empty($destination_file))
			$this->setDestinationFile( $destination_file );
		if (!empty($destination_dir))
			$this->setDestinationDir( $destination_dir );
		if (!empty($adapter_type))
			$this->setAdapterType( $adapter_type );
	}

	/**
	 * Initialization : creation of the adapter
	 * @throw Throws a RuntimeException if the adapter doesn't exist
	 */
	protected function init()
	{
		if (true===$this->isInited) return;

		$this->_cleanFilesStack();

		if (empty($this->__adapter_type))
			$this->_guessAdapterType();
		
		if (!empty($this->__adapter_type)) {
			if (class_exists($this->__adapter_type)) {
				$this->__adapter = new $this->__adapter_type;
			} else {
				throw new \RuntimeException(
					sprintf('Minifier adapter for type "%s" doesn\'t exist!', $this->__adapter_type)
				);
			}
		}

		$this->isInited = true;
	}

// -------------------
// Getters / Setters
// -------------------

	/**
	 * Reset all object properties to default or empty values
	 * @param bool $hard Reset all object properties (destination directory and web root included)
	 * @return self $this for method chaining
	 */
	public function reset($hard = false)
	{
		$this->files_stack 				= array();
		$this->destination_file 		= '';
		$this->minified_content 		= '';
		$this->silent 					= true;
		$this->direct_output			= false;
		$this->isCleaned_files_stack	= false;
		$this->isInited					= false;
		if (true===$hard) {
			$this->destination_dir 		= '';
			$this->web_root_path		= null;
			$this->__adapter_type		= null;
			$this->__adapter			= null;
		}
		return $this;
	}

	/**
	 * Set the silence object flag
	 * @param bool $silence True to avoid the class throwing exceptions
	 * @return self $this for method chaining
	 */
	public function setSilent($silence)
	{
		$this->silent = (bool) $silence;
		return $this;
	}

	/**
	 * Set the direct_output object flag
	 * @param bool $direct_output True to avoid writing of the compressed result in a file
	 * @return self $this for method chaining
	 */
	public function setDirectOutput($direct_output)
	{
		$this->direct_output = (bool) $direct_output;
		return $this;
	}

	/**
	 * Set the adapter type to use, this type will be guessed if not set
	 * @param string $type The type name
	 * @return self $this for method chaining
	 */
	public function setAdapterType($type)
	{
		$this->__adapter_type = '\TemplateEngine\MinifierAdapter\\'.strtoupper($type);
		return $this;
	}

	/**
	 * Add a file to treat in the files stack
	 * @param string $file A file path to add in stack
	 * @return self $this for method chaining
	 */
	public function addFile($file)
	{
		$this->files_stack[] = $file;
		return $this;
	}

	/**
	 * Set a full files stack to treat
	 * @param array $files_stack An array of file paths to treat
	 * @return self $this for method chaining
	 */
	public function setFilesStack(array $files_stack)
	{
		$this->isCleaned_files_stack = false;
		$this->files_stack = $files_stack;
		return $this;
	}

	/**
	 * Get the files stack
	 * @return array The current files stack of the object
	 */
	public function getFilesStack()
	{
		return $this->files_stack;
	}

	/**
	 * Get the minified content
	 * @return array The minified content string
	 */
	public function getMinifiedContent()
	{
		return $this->minified_content;
	}

	/**
	 * Set the destination file to write the result in
	 * @param string $destination_file The file path or name to create and write in
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the file name is not a string (and if $silent==false)
	 */
	public function setDestinationFile($destination_file)
	{
		if (is_string($destination_file)) {
			$this->destination_file = $destination_file;
		} else {
			if (false===$this->silent) {
				throw new \InvalidArgumentException(
					sprintf('[Minifier] Destination file name must be a string (got "%s")!', gettype($destination_file))
				);
			}
		}
		return $this;
	}

	/**
	 * Get the destination file to write the result in
	 * @return string The file name to write in
	 */
	public function getDestinationFile()
	{
		return $this->destination_file;
	}

	/**
	 * Build a destination filename based on the files stack names
	 * @return string The file name built
	 * @throw Throws a RuntimeException if the files stack is empty (filename can not be guessed)
	 */
	public function guessDestinationFilename()
	{
		if (!empty($this->files_stack)) {
			$this->_cleanFilesStack();
			$this->init();

			$_fs = array();
			foreach($this->files_stack as $_file) {
				$_fs[] = $_file->getFilename();
			}
			if (!empty($_fs)) {
				sort($_fs);
				$this->setDestinationFile( md5( join('', $_fs) ).'.'.$this->__adapter->file_extension );
				return $this->getDestinationFile();
			}
		}
		if (false===$this->silent) {
			throw new \RuntimeException(
				'[Minifier] Destination filename can\'t be guessed because files stack is empty!'
			);
		}
		return null;
	}

	/**
	 * Set the destination directory to write the destination file in
	 * @param string $destination_dir The directory path to create the file in
	 * @return self $this for method chaining
	 * @throw Throws an InvalidArgumentException if the directory name is not a string (and if $silent==false)
	 */
	public function setDestinationDir($destination_dir)
	{
		if (is_string($destination_dir)) {
			$destination_dir = realpath($destination_dir);
			if (@file_exists($destination_dir) && @is_dir($destination_dir)) {
				$this->destination_dir = rtrim($destination_dir, '/').'/';
			} elseif (false===$this->silent) {
				throw new \InvalidArgumentException(
					sprintf('[Minifier] Destination directory "%s" must exist!', $destination_dir)
				);
			}
		} else {
			if (false===$this->silent) {
				throw new \InvalidArgumentException(
					sprintf('[Minifier] Destination directory must be a string (got "%s")!', gettype($destination_dir))
				);
			}
		}
		return $this;
	}

	/**
	 * Get the destination directory to write the file in
	 * @return string The directory name to write in
	 */
	public function getDestinationDir()
	{
		return $this->destination_dir;
	}

	/**
	 * Set the web root path (the real path to clear in DestinationRealPath) to build web path of destination file
	 * @param string $path The realpath of the web root to clear it from DestinationRealPath to build DestinationWebPath
	 * @return self $this for method chaining
	 */
	public function setWebRootPath($path)
	{
		if (is_string($path)) {
			$path = realpath($path);
			if (@file_exists($path) && @is_dir($path)) {
				$this->web_root_path = rtrim($path, '/').'/';
			} elseif (false===$this->silent) {
				throw new \InvalidArgumentException(
					sprintf('[Minifier] Web root path "%s" must exist!', $path)
				);
			}
		} else {
			if (false===$this->silent) {
				throw new \InvalidArgumentException(
					sprintf('[Minifier] Web root path must be a string (got "%s")!', gettype($path))
				);
			}
		}
		return $this;
	}

	/**
	 * Get the web root path
	 * @return string The current web root path seted
	 */
	public function getWebRootPath()
	{
		return $this->web_root_path;
	}

	/**
	 * Get the destination file path ready for web inclusion
	 * @return string The file path to write in
	 * @throw Throws a LogicException if the web root path has not been set (and silent==false)
	 */
	public function getDestinationWebPath()
	{
		if (!empty($this->web_root_path)) {
			return str_replace($this->web_root_path, '', $this->getDestinationRealPath());
		} elseif (false===$this->silent) {
			throw new \LogicException(
				'[Minifier] Can\'t create web path because "web_root_path" is not defined!'
			);
		}
		return null;
	}

	/**
	 * Get the destination file absolute path
	 * @return string The file path to write in
	 */
	public function getDestinationRealPath()
	{
		return rtrim($this->destination_dir, '/').'/'.$this->destination_file;
	}

	/**
	 * Check if a destination file already exist for the current object
	 * @return bool True if the minified file exists
	 */
	public function fileExists()
	{
		if (empty($this->destination_file))
			$this->guessDestinationFilename();
		return file_exists( $this->getDestinationRealPath() );
	}

	/**
	 * Check if a destination file already exist for the current object and if it is fresher than sources
	 * @return bool True if the sources had been modified after minified file creation
	 */
	public function mustRefresh()
	{
		if ($this->fileExists()) {
			$this->_cleanFilesStack();
			$_dest = new \SplFileInfo( $this->getDestinationRealPath() );
			if (!empty($this->files_stack)) {
				foreach($this->files_stack as $_file) {
					if ($_file->getMTime() > $_dest->getMTime())
						return true;
				}
				return false;
			}
		}
		return true;
	}

// -------------------
// Process stack
// -------------------

	/**
	 * Process the current files stack
	 * @return self $this for method chaining
	 */
	public function process()
	{
		$this->_cleanFilesStack();
		$this->init();

		if (empty($this->destination_file) && false===$this->direct_output)
			$this->guessDestinationFilename();
	
		if (false===$this->direct_output) {
			if (!$this->mustRefresh()) {
				$this->minified_content = file_get_contents( $this->getDestinationRealPath() );
				return $this;
			}
		}

		$contents = array();
		foreach($this->files_stack as $_file) {
			$contents[] = '';
			$contents[] = $this->__adapter->buildComment( $_file->getFilename() );
			$contents[] = $this->__adapter->minify(
				file_get_contents( $_file->getRealPath() )
			);
		}
		$this->minified_content = implode("\n", $contents);

		if (!empty($this->minified_content) && false===$this->direct_output) {
			$this->_writeDestinationFile();
		}
		return $this;
	}

// -------------------
// Files stack cleaning
// -------------------

	/**
	 * Rebuild the current files stack as an array of File objects
	 * @return void
	 * @throw Throws a RuntimeException if one of the files stack doesn't exist (and if $silent==false)
	 */
	protected function _cleanFilesStack()
	{
		if (true===$this->isCleaned_files_stack) return;

		$new_stack = array();
		foreach($this->files_stack as $_file) {
			if (is_object($_file) && ($_file instanceof \SplFileInfo)) {
				$new_stack[] = $_file;
			} elseif (is_string($_file) && @file_exists($_file)) {
				$new_stack[] = new \SplFileInfo($_file);
			} elseif (false===$this->silent) {
				throw new \RuntimeException(
					sprintf('[Minifier] Source to minify "%s" not found!', $_file)
				);
			}
		}
		$this->files_stack = $new_stack;
		$this->isCleaned_files_stack = true;
	}
	
	/**
	 * Guess the adapter type based on extension of the first file in stack
	 * @return bool True if the adapter type had been guessed
	 * @throw Throws a RuntimeException if no file was found in the stack
	 */
	protected function _guessAdapterType()
	{
		$this->_cleanFilesStack();
		if (!empty($this->files_stack)) {
			$_fs = $this->files_stack;
			$_file = array_shift($_fs);
			$this->setAdapterType( $_file->getExtension() );
			return true;
		} elseif (false===$this->silent) {
			throw new \RuntimeException(
				'[Minifier] Trying to guess adapter from an empty files stack!'
			);
		}
		return false;
	}
		
// -------------------
// Utilities
// -------------------

	/**
	 * Writes the compressed content in the destination file
	 * @return bool|string The filename if it has been created, false otherwise
	 * @throw Throws a RuntimeException if the file can't be written
	 */
	protected function _writeDestinationFile()
	{
		if (empty($this->destination_file))
			$this->guessDestinationFilename();
	
		$content = $this->_getHeaderComment()."\n".$this->minified_content;
		$dest_file = $this->getDestinationRealPath();
		if (false!==file_put_contents($dest_file, $content)) {
			return true;
		} else {
			if (false===$this->silent) {
				throw new \RuntimeException(
					sprintf('Destination minified file "%s" can\'t be written on disk!', $dest_file)
				);
			}
			return false;
		}
	}

	/**
	 * Build the compressed content header comment information
	 * @return string A comment string to write in top of the content
	 */
	protected function _getHeaderComment()
	{
		$this->init();
		return $this->__adapter->buildComment(
			sprintf('Generated by PHP Minifier class on %s at %s', date('Y-m-d'), date('H:i'))
		);
	}

}

// Endfile