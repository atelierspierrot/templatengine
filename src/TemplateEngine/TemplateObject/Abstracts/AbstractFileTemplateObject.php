<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine\TemplateObject\Abstracts;

use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\Template;
use \TemplateEngine\Minifier;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractFileTemplateObject extends AbstractTemplateObject
{

	/**
	 * The minifier class object
	 */
	protected $__minifier;
	
	/**
	 * Constructor
	 * @param Template $_tpl The whole template object
	 */
	public function __construct( Template $_tpl )
	{
		$this->__template = $_tpl;
		$this->__minifier = new Minifier;
		$this->__minifier
			->setWebRootPath( $this->__template->getWebRootPath() )
			->setDestinationDir( $this->__template->getAssetsCachePath() );
		$this->init();
	}

	/**
	 * Minify a stack of files
	 * @param array $stack The stack to clean
	 * @param bool $silent Set up the Minifier $silence flag (default is true)
	 * @param bool $direct_output Set up the Minifier $direct_output flag (default is false)
	 * @return array Return the extracted stack
	 */
	protected function minifyStack( array $stack, $silent=true, $direct_output=false )
	{
		$this->__minifier->reset();
		if (false===$silent)
			$this->__minifier->setSilent(false);
		if (true===$direct_output)
			$this->__minifier->setDirectOutput(true);

		return $this->__minifier
			->setFilesStack( $stack )
			->process()
			->getDestinationWebPath();
	}

}

// Endfile