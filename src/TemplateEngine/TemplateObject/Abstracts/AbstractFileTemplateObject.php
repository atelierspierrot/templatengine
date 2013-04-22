<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject\Abstracts;

use Patterns\Commons\Registry;

use TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject,
    TemplateEngine\Template;

use Assets\Compressor;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractFileTemplateObject
    extends AbstractTemplateObject
{

	/**
	 * The object Registry
	 */
	protected $_registry;
	
	/**
	 * The merger class object
	 * @var object Assets\Compressor
	 */
	protected $__compressor;
	
	/**
	 * Constructor
	 *
	 * @param Template $_tpl The whole template object
	 */
	public function __construct(Template $_tpl)
	{
		$this->registry = new Registry;
		$this->__template = $_tpl;
		$this->__compressor = new Compressor;
		$this->__compressor
			->setWebRootPath( $this->__template->getWebRootPath() )
			->setDestinationDir( $this->__template->getAssetsCachePath() );
		$this->init();
	}

	/**
	 * Merge a stack of files
	 *
	 * @param array $stack The stack to clean
	 * @param bool $silent Set up the Compressor $silence flag (default is true)
	 * @param bool $direct_output Set up the Compressor $direct_output flag (default is false)
	 * @return array Return the extracted stack
	 */
	protected function mergeStack(array $stack, $silent = true, $direct_output = false)
	{
		$this->__compressor->reset();
		if (false===$silent)
			$this->__compressor->setSilent(false);
		if (true===$direct_output)
			$this->__compressor->setDirectOutput(true);

		return $this->__compressor
			->setFilesStack( $stack )
			->merge()
			->getDestinationWebPath();
	}

	/**
	 * Minify a stack of files
	 *
	 * @param array $stack The stack to clean
	 * @param bool $silent Set up the Compressor $silence flag (default is true)
	 * @param bool $direct_output Set up the Compressor $direct_output flag (default is false)
	 * @return array Return the extracted stack
	 */
	protected function minifyStack(array $stack, $silent = true, $direct_output = false)
	{
		$this->__compressor->reset();
		if (false===$silent)
			$this->__compressor->setSilent(false);
		if (true===$direct_output)
			$this->__compressor->setDirectOutput(true);

		return $this->__compressor
			->setFilesStack( $stack )
			->minify()
			->getDestinationWebPath();
	}

}

// Endfile