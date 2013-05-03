<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Package;

use InvalidArgumentException;

use AssetsManager\Config,
    AssetsManager\Preset as OriginalPreset,
    AssetsManager\Package\AssetsPackage,
    AssetsManager\Package\AssetsPackageInterface,
    AssetsManager\Package\AssetsPresetInterface;

use TemplateEngine\TemplateEngine,
    TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Preset extends OriginalPreset
{

    /**
     * @var Assets\Loader
     */
    protected $assets_loader;

    /**
     * @var TemplateEngine\TemplateEngine
     */
    protected $template_engine;

    /**
     * @param string $package_name
     * @param array $package_data
     * @param object $package AssetsManager\Package\AssetsPackage
     * @param object $engine TemplateEngine\TemplateEngine
     */
    public function __construct($preset_name, array $preset_data, AssetsPackageInterface $package, TemplateEngine $engine)
    {
        parent::__construct($preset_name, $preset_data, $package);
    }

    /**
     * Parse and load an assets file in a template object
     *
     * @param string $path
     * @param object $object The template object to work on
     * @return void
     */
    public function parse($path, AbstractTemplateObject $object)
    {
        $package = $this->_findPresetPackageName();
        if (substr($path, 0, strlen('min:'))=='min:') {
            $file_path = $this->assets_loader->findInPackage(substr($path, strlen('min:')), $package);
            $object->addMinified($file_path);
        } elseif (substr($path, 0, strlen('pack:'))=='pack:') {
            $file_path = $this->assets_loader->findInPackage(substr($path, strlen('pack:')), $package);
            $object->addMinified($file_path);
        } else {
            $file_path = $this->assets_loader->findInPackage($path, $package);
            $object->add($file_path);
        }
    }

	/**
	 * Automatic assets loading from an Assets package declare in a `composer.json`
	 *
	 * @param string $package_name The name of the package to use
	 * @return void
	 */
	public function load()
	{
        foreach ($this->preset_data as $type=>$data) {
            if ('css'===$type) {
                foreach ($data as $path) {
                    $this->parse($path, $this->template_engine->getTemplateObject('CssFile'));
                }
            } elseif ('jsfiles_header'===$type) {
                foreach ($data as $path) {
                    $this->parse($path, $this->template_engine->getTemplateObject('JavascriptFile', 'jsfiles_header'));
                }
            } elseif ('jsfiles_footer'===$type) {
                foreach ($data as $path) {
                    $this->parse($path, $this->template_engine->getTemplateObject('JavascriptFile', 'jsfiles_footer'));
                }
            }
        }
	}

}

// Endfile