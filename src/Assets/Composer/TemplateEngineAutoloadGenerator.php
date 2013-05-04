<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Composer;

use Composer\Composer,
    Composer\IO\IOInterface,
    Composer\Autoload\AutoloadGenerator,
    Composer\Package\PackageInterface,
    Composer\Repository\RepositoryInterface,
    Composer\Script\Event,
    Composer\Script\EventDispatcher;

use Library\Helper\Directory as DirectoryHelper;

use AssetsManager\Config,
    AssetsManager\Composer\Installer\AssetsInstaller,
    AssetsManager\Composer\Autoload\AssetsAutoloadGenerator;

use Assets\Package\Package,
    Assets\Composer\ComposerConfig;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngineAutoloadGenerator
    extends AssetsAutoloadGenerator
{

    protected $_package;

    /**
     * Method called at the creation of the Composer autoload file
     *
     * @param object Composer\Script\Event
     * @return void
     * @throws Throws errors to the IO interface
     */
    public static function postAutoloadDump(Event $event)
    {
        Config::load('Assets\Composer\ComposerConfig');
        $_cls = __CLASS__;
        $_this = $_cls::getInstance();
        $_this->setPackage($event->getComposer()->getPackage());
        AssetsAutoloadGenerator::setGenerator(array($_this, 'generate'));
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $extra = $this->_package->getExtra();
        if (!empty($extra) && !empty($extra['assets-dir'])) {
            $this->assets_db[$package->getPrettyName()] = 
                $this->parseComposerExtra($this->_package, $this->app_base_path);
        }

        $app_base_path = $this->assets_installer->getAppBasePath();
        $assets_dir = str_replace($app_base_path . '/', '', $this->assets_installer->getAssetsDir());
        $assets_vendor_dir = str_replace($app_base_path . '/' . $assets_dir . '/', '', $this->assets_installer->getAssetsVendorDir());
        $full_db = array(
            'assets-dir' => $assets_dir,
            'assets-vendor-dir' => $assets_vendor_dir,
            'document-root' => $this->assets_installer->getDocumentRoot(),
            'packages' => $this->assets_db
        );

        return $this->writeJsonDatabase($full_db);
    }

    /**
     * @param object $package Composer\Package\PackageInterface
     * @return void
     */
    public function setPackage(PackageInterface $package)
    {
        $this->_package = $package;
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param array $package The package, Composer\Package\PackageInterface
     * @return void
     */
    public function parseComposerExtra(PackageInterface $package, $package_dir)
    {
        $data = $this->assets_installer->parseComposerExtra($package, $package_dir);

        $package_dir = rtrim($package_dir, '/') . '/';
        $extra = $package->getExtra();

        if (isset($extra['views'])) {
            $views = is_array($extra['views']) ? $extra['views'] : array($extra['views']);
            $data['view'] = array();
            foreach ($views as $view_path) {
                $data['view'][] = $package_dir . $view_path;
            }
        }

        if (isset($extra['views-functions'])) {
            $views_fcts = is_array($extra['views-functions']) ? $extra['views-functions'] : array($extra['views-functions']);
            $data['views_functions'] = array();
            foreach ($views_fcts as $view_fct_path) {
                $data['views_functions'][] = $package_dir . $view_fct_path;
            }
        }

        return $data;
    }

}

// Endfile