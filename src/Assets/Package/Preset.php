<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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