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
    AssetsManager\Composer\Util\Filesystem,
    AssetsManager\Composer\Installer\AssetsInstaller,
    AssetsManager\Composer\Autoload\AssetsAutoloadGenerator;

use Assets\Package\Package,
    Assets\Composer\TemplateEngineConfig,
    Assets\Composer\TemplateEngineInstaller;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngineAutoloadGenerator
    extends AutoloadGenerator
{

    protected $_composer;
    protected $_autoloader;
    protected $_package;

    /**
     * @param object $package Composer\Package\PackageInterface
     * @param object $composer Composer\Composer
     * @return void
     */
    public function __construct(PackageInterface $package, Composer $composer)
    {
        parent::__construct($composer->getEventDispatcher());
        Config::load('Assets\Composer\TemplateEngineConfig');
        $this->_composer = $composer;
        $this->_autoloader = AssetsAutoloadGenerator::getInstance();
        $this->_autoloader->setGenerator(array($this, 'generate'));
        $this->_package = $package;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $full_db = $this->getFullDb();
        return $this->_autoloader->writeJsonDatabase($full_db);
    }

    /**
     * Build the complete database array
     * @return array
     */
    public function getFullDb()
    {
        $filesystem = new Filesystem();
        $config = $this->_composer->getConfig();
        $assets_db = $this->_autoloader->getRegistry();
        $vendor_dir = $this->_autoloader->getAssetsInstaller()->getVendorDir();
        $app_base_path = $this->_autoloader->getAssetsInstaller()->getAppBasePath();
        $assets_dir = str_replace($app_base_path . '/', '', $this->_autoloader->getAssetsInstaller()->getAssetsDir());
        $assets_vendor_dir = str_replace($app_base_path . '/' . $assets_dir . '/', '', $this->_autoloader->getAssetsInstaller()->getAssetsVendorDir());
        $document_root = $this->_autoloader->getAssetsInstaller()->getDocumentRoot();
        $extra = $this->_package->getExtra();

        $root_data = $this->parseComposerExtra($this->_package, $app_base_path, '');
        if (!empty($root_data)) {
            $root_data['relative_path'] = '../';
            $assets_db[$this->_package->getPrettyName()] = $root_data;
        }

        $vendor_path = strtr(realpath($vendor_dir), '\\', '/');
        $rel_vendor_path = $filesystem->findShortestPath(getcwd(), $vendor_path, true);

        $local_repo = $this->_composer->getRepositoryManager()->getLocalRepository();
        $package_map = $this->buildPackageMap($this->_composer->getInstallationManager(), $this->_package, $local_repo->getPackages());

        foreach ($package_map as $i=>$package) {
            if ($i===0) { continue; }
            $package_object = $package[0];
            $package_install_path = $package[1];
            if (empty($package_install_path)) {
                $package_install_path = $app_base_path;
            }
            $package_name = $package_object->getPrettyName();
            $data = $this->parseComposerExtra(
                $package_object,
                $this->_autoloader->getAssetsInstaller()->getAssetsInstallPath($package_object),
                str_replace($app_base_path . '/', '', $vendor_path) . '/' . $package_object->getPrettyName()
            );
            if (!empty($data)) {
                $assets_db[$package_name] = $data;
            }
        }

        $full_db = array(
            'assets-dir' => $assets_dir,
            'assets-vendor-dir' => $assets_vendor_dir,
            'document-root' => $document_root,
            'cache-dir' => isset($extra['cache-dir']) ? $extra['cache-dir'] : Config::getDefault('cache-dir'),
            'cache-assets-dir' => isset($extra['cache-assets-dir']) ? $extra['cache-assets-dir'] : Config::getDefault('cache-assets-dir'),
            'packages' => $assets_db
        );
        return $full_db;
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param object $package Composer\Package\PackageInterface
     * @param string $assets_package_dir
     * @param string $vendor_package_dir
     * @return void
     */
    public function parseComposerExtra(PackageInterface $package, $assets_package_dir, $vendor_package_dir)
    {
        $data = $this->_autoloader->getAssetsInstaller()->parseComposerExtra($package, $assets_package_dir);
        $extra = $package->getExtra();
        $assets_package_dir = rtrim($assets_package_dir, '/') . '/';
        if (strlen($vendor_package_dir)) {
            $vendor_package_dir = rtrim($vendor_package_dir, '/') . '/';
        }

        if (isset($extra['layouts'])) {
            $layouts = is_array($extra['layouts']) ? $extra['layouts'] : array($extra['layouts']);
            $data['layouts_path'] = array();
            foreach ($layouts as $layout_path) {
                $data['layouts_path'][] = $vendor_package_dir . $layout_path;
            }
        }

        if (isset($extra['views'])) {
            $views = is_array($extra['views']) ? $extra['views'] : array($extra['views']);
            $data['views_path'] = array();
            foreach ($views as $view_path) {
                $data['views_path'][] = $vendor_package_dir . $view_path;
            }
        }

        if (isset($extra['views-functions'])) {
            $views_fcts = is_array($extra['views-functions']) ? $extra['views-functions'] : array($extra['views-functions']);
            $data['views_functions'] = array();
            foreach ($views_fcts as $view_fct_path) {
                $data['views_functions'][] = $vendor_package_dir . $view_fct_path;
            }
        }

        return $data;
    }

}

// Endfile