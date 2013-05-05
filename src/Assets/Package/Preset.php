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
    AssetsManager\Package\Preset as OriginalPreset,
    AssetsManager\Package\AssetsPackage,
    AssetsManager\Package\AssetsPackageInterface;

use Assets\Package\TemplateEnginePresetInterface;

use TemplateEngine\TemplateEngine,
    TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Preset extends OriginalPreset
{

    /**
     * @param string $package_name
     * @param array $package_data
     * @param object $package AssetsManager\Package\AssetsPackage
     * @param object $engine TemplateEngine\TemplateEngine
     */
    public function __construct(
        $preset_name, array $preset_data, \AssetsManager\Package\AssetsPackageInterface $package
    ) {
        parent::__construct($preset_name, $preset_data, $package);
    }

	/**
	 * Automatic assets loading from an Assets package declare in a `composer.json`
	 *
	 * @param string $package_name The name of the package to use
	 * @return void
	 */
	public function load()
	{
	    if (empty($this->_statements)) parent::load();

        /* @var $template_engine TemplateEngine\TemplateEngine */
        $template_engine = \TemplateEngine\TemplateEngine::getInstance();

        foreach ($this->getOrganizedStatements() as $type=>$statements) {
            foreach ($statements as $statement) {
                if ('css'===$type) {
                    $template_object = $template_engine->getTemplateObject('CssFile');
                    $css = $statement->getData();
                    if (isset($css['minified']) && true===$css['minified']) {
                        $template_object->addMinified($css['src'], $css['media']);
                    } else {
                        $template_object->add($css['src'], $css['media']);
                    }
                }
                
                elseif ('jsfiles_header'===$type) {
                    $template_object = $template_engine->getTemplateObject('JavascriptFile', 'jsfiles_header');
                    $js = $statement->getData();
                    if (
                        (isset($js['minified']) && true===$js['minified']) ||
                        (isset($js['packed']) && true===$js['packed'])
                    ) {
                        $template_object->addMinified($js['src']);
                    } else {
                        $template_object->add($js['src']);
                    }
                }
                
                elseif (in_array($type, array('js', 'jsfiles_footer'))) {
                    $template_object = $template_engine->getTemplateObject('JavascriptFile', 'jsfiles_footer');
                    $js = $statement->getData();
                    if (
                        (isset($js['minified']) && true===$js['minified']) ||
                        (isset($js['packed']) && true===$js['packed'])
                    ) {
                        $template_object->addMinified($js['src']);
                    } else {
                        $template_object->add($js['src']);
                    }
                }
            }
        }
	}

}

// Endfile