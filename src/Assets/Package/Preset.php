<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyleft (ↄ) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace Assets\Package;

use \InvalidArgumentException;
use \AssetsManager\Config;
use \AssetsManager\Package\Preset as OriginalPreset;
use \AssetsManager\Package\AssetsPackage;
use \AssetsManager\Package\AssetsPackageInterface;
use \Assets\Package\TemplateEnginePresetInterface;
use \TemplateEngine\TemplateEngine;
use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Preset extends OriginalPreset
{

    /**
     * @param string $preset_name
     * @param array $preset_data
     * @param \AssetsManager\Package\AssetsPackageInterface $package
     */
    public function __construct(
        $preset_name, array $preset_data, AssetsPackageInterface $package
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