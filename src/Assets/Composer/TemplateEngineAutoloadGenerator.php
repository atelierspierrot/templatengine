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
        $filesystem = new Filesystem();
        $config = $this->_composer->getConfig();
        $assets_db = $this->_autoloader->getRegistry();
        $vendor_dir = $this->assets_installer->getVendorDir();
        $app_base_path = $this->assets_installer->getAppBasePath();
        $assets_dir = str_replace($app_base_path . '/', '', $this->assets_installer->getAssetsDir());
        $assets_vendor_dir = str_replace($app_base_path . '/' . $assets_dir . '/', '', $this->assets_installer->getAssetsVendorDir());
        $document_root = $this->assets_installer->getDocumentRoot();
        $extra = $this->_package->getExtra();

        if (!empty($extra) && !empty($extra['assets-dir'])) {
            $assets_db[$package->getPrettyName()] = 
                $this->parseComposerExtra($this->_package, $app_base_path);
        }

        $vendor_path = strtr(realpath($vendor_dir), '\\', '/');
        $rel_vendor_path = $filesystem->findShortestPath(getcwd(), $vendor_path, true);

        $local_repo = $this->_composer->getRepositoryManager()->getLocalRepository();
        $package_map = $this->buildPackageMap($this->_composer->getInstallationManager(), $this->_package, $local_repo->getPackages());

var_export($package_map);

/*
        foreach ($assets_db as $package_name=>$package_data) {
            $assets_db[$package_name] = 
                $this->parseComposerExtraData($package_data, $app_base_path);
        }
*/

        $full_db = array(
            'assets-dir' => $assets_dir,
            'assets-vendor-dir' => $assets_vendor_dir,
            'document-root' => $document_root,
            'packages' => $assets_db
        );
        return $this->writeJsonDatabase($full_db);

/////////////////////////////////
        $vendor_path = strtr(realpath($vendor_dir), '\\', '/');
        $rel_vendor_path = $filesystem->findShortestPath(getcwd(), $vendor_path, true);

        $local_repo = $this->_composer->getRepositoryManager()->getLocalRepository();
        $package_map = $this->buildPackageMap($this->_composer->getInstallationManager(), $this->_package, $local_repo->getPackages());
        $autoloads = $this->parseAutoloads($package_map, $this->_package);

        foreach ($autoloads['psr-0'] as $namespace => $paths) {
            $exportedPaths = array();
            foreach ($paths as $path) {
                if (strstr($path, 'bundles'))
                {
                    $exportedPaths[] = var_export( trim( str_replace(CarteBlancheInstaller::CARTEBLANCHE_BUNDLES_DIR, '', $path), '/'), 1);
                }
            }
            if (count($exportedPaths)>0) {
                $exportedPrefix = var_export($namespace, true);
                $bootstrapFile .= "        $exportedPrefix => ";
                if (count($exportedPaths) > 1) {
                    $bootstrapFile .= "array(".implode(', ', $exportedPaths)."),\n";
                } else {
                    $bootstrapFile .= $exportedPaths[0].",\n";
                }
            }
        }

        return file_put_contents($appBasePath.'/data/bootstrap.php', $bootstrapFile);
/////////////////////////////////
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param object $package Composer\Package\PackageInterface
     * @param string $package_dir
     * @return void
     */
    public function parseComposerExtra(PackageInterface $package, $package_dir)
    {
        $data = $this->assets_installer->parseComposerExtra($package, $package_dir);
        $extra = $package->getExtra();
        return $this->parseComposerExtraData($data, $extra, $package_dir);
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param array $data
     * @param array $extra
     * @param string $package_dir
     * @return void
     */
    public function parseComposerExtraData(array $data, array $extra, $package_dir)
    {
        $package_dir = rtrim($package_dir, '/') . '/';

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