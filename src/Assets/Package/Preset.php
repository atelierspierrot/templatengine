<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013-2014 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <http://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Package;

use \InvalidArgumentException;
use \AssetsManager\Config;
use \AssetsManager\Package\Preset as OriginalPreset;
use \AssetsManager\Package\Package as AssetsPackage;
use \AssetsManager\Package\PackageInterface;
use \TemplateEngine\TemplateEngine;
use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Preset
    extends OriginalPreset
{

    /**
     * @param string $preset_name
     * @param array $preset_data
     * @param \AssetsManager\Package\PackageInterface $package
     */
    public function __construct(
        $preset_name, array $preset_data, PackageInterface $package
    ) {
        parent::__construct($preset_name, $preset_data, $package);
    }

    /**
     * Automatic assets loading from an Assets package declare in a `composer.json`
     *
     * @return void
     */
    public function load()
    {
        if (empty($this->_statements)) parent::load();

        /* @var $template_engine \TemplateEngine\TemplateEngine */
        $template_engine = TemplateEngine::getInstance();

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
                        $template_object->addMinified(
                            $js['src'],
                            (isset($js['position']) ? $js['position'] : null)
                        );
                    } else {
                        $template_object->add(
                            $js['src'],
                            (isset($js['position']) ? $js['position'] : null)
                        );
                    }
                }
                
                elseif (in_array($type, array('js', 'jsfiles_footer'))) {
                    $template_object = $template_engine->getTemplateObject('JavascriptFile', 'jsfiles_footer');
                    $js = $statement->getData();
                    if (
                        (isset($js['minified']) && true===$js['minified']) ||
                        (isset($js['packed']) && true===$js['packed'])
                    ) {
                        $template_object->addMinified(
                            $js['src'],
                            (isset($js['position']) ? $js['position'] : null)
                        );
                    } else {
                        $template_object->add(
                            $js['src'],
                            (isset($js['position']) ? $js['position'] : null)
                        );
                    }
                }
            }
        }
    }

}

// Endfile