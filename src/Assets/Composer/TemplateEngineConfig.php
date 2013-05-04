<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Composer;

use AssetsManager\Config\DefaultConfig;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngineConfig
    extends DefaultConfig
{

    /**
     * The real configuration entries
     * @return array
     */
    public static function getDefaults()
    {
        return array(
            // The default package type handles by the installer
            'package-type' => array( 'library-assets-template' ),
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
            // the configuration class (this class, can be null but must be present)
            // must impelements AssetsManager\Config\ConfiguratorInterface
            'assets-config-class' => null,
            // the AssetsPackage class
            // must implements AssetsManager\Package\AssetsPackageInterface
            'assets-package-class' => 'Assets\Package\Package',
            // the AssetsPreset class
            // must implements AssetsManager\Package\AssetsPresetInterface
            'assets-preset-class' => 'Assets\Package\Preset',
            // the AssetsInstaller class
            // must implements AssetsManager\Composer\Installer\AssetsInstallerInterface
            'assets-package-installer-class' => 'Assets\Composer\TemplateEngineInstaller',
            // the AssetsAutoloadGenerator class
            // must extends AssetsManager\Composer\Autoload\AbstractAutoloadGenerator
            'assets-autoload-generator-class' => 'Assets\Composer\TemplateEngineAutoloadGenerator',

            // Template Engine specific: relative cache directory from assets-dir
            'cache-dir' => 'tmp',
            // Template Engine specific: relative assets cache directory from assets-dir
            'cache-assets-dir' => 'tmp_assets',
            // Template Engine specific: relative layouts from root-dir
            'layouts' => 'www/html5boilerplate',
            // Template Engine specific: relative views from root-dir
            'views' => array('www', 'www/html5boilerplate'),
            // Template Engine specific: relative views functions from root-dir
            'views-functions' => 'src/TemplateEngine/view_functions.php',
        );
    }

}

// Endfile