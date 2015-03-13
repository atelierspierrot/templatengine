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

namespace Assets;

use \AssetsManager\Config\ConfiguratorInterface;

/**
 * @author  piwi <me@e-piwi.fr>
 */
class TemplateEngineConfig
    implements ConfiguratorInterface
{

    /**
     * The real configuration entries
     * @return array
     */
    public static function getDefaults()
    {
        return array(
            // The default package type handles by the installer
            'package-type' => 'library-assets',
            // The default package vendor directory name (related to package root dir)
            'vendor-dir' => 'vendor',
            // The default package assets directory name (related to package root dir)
            'assets-dir' => 'www',
            // The default third-party packages'assets directory name (related to package assets dir)
            'assets-vendor-dir' => 'vendor',
            // The default package root directory is set on `$_SERVER['DOCUMENT_ROOT']`
            'document-root' => $_SERVER['DOCUMENT_ROOT'],
            // The assets database file created on install
            'assets-db-filename' => 'assets.json',
            // Composition of an `assets-presets` statement in `composer.json`
            // array pairs like "statement name => adapter"
            'use-statements' => array(
                'css' => 'Css',
                'js' => 'Javascript',
                'jsfiles_footer' => 'Javascript',
                'jsfiles_header' => 'Javascript',
                'require' => 'Requirement'
            ),
            'config-class' => null,
            'assets-package-class' => 'Assets\Package\Package',
            'assets-preset-class' => 'Assets\Package\Preset',
            'assets-package-installer-class' => 'Assets\Composer\ComposerInstaller',
        );
    }

}

// Endfile