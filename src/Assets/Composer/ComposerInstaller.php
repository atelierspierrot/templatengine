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

use AssetsManager\Package\AbstractAssetsPackage,
    AssetsManager\Config,
    AssetsManager\Composer\Installer\AssetsInstaller,
    AssetsManager\Composer\Autoload\AssetsAutoloadGenerator;

use Assets\Package\Package,
    Assets\Composer\ComposerConfig;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class ComposerInstaller
    extends AssetsInstaller
{

    /**
     * Method called at the creation of the Composer autoload file
     *
     * @param object Composer\Script\Event
     * @return void
     * @throws Throws errors to the IO interface
     */
    public static function postAutoloadDump(Event $event)
    {
        $_cls = __CLASS__;
        $_this = new $_cls($event->getIO(), $event->getComposer());
        AssetsAutoloadGenerator::setGenerator(array($_this, 'generate'));
    }

    /**
     * Initializes installer
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library')
    {
        Config::load('Assets\Composer\ComposerConfig');
        parent::__construct($io, $composer, $type);

        $package = $composer->getPackage();
        $extra = $package->getExtra();
        if (!empty($extra) && !empty($extra['assets-dir'])) {
            $this->assets_db[$package->getPrettyName()] = 
                $this->parseComposerExtra($package, $this->app_base_path);
        }
    }

    public function generate()
    {
        $app_base_path = $this->assets_installer->getAppBasePath();
        $assets_dir = str_replace($app_base_path . '/', '', $this->assets_installer->getAssetsDir());
        $assets_vendor_dir = str_replace($app_base_path . '/' . $assets_dir . '/', '', $this->assets_installer->getAssetsVendorDir());
        $full_db = array(
            'test'=>'YO',
            'assets-dir' => $assets_dir,
            'assets-vendor-dir' => $assets_vendor_dir,
            'document-root' => $this->assets_installer->getDocumentRoot(),
            'packages' => $this->assets_db
        );

        $assets_file = $this->assets_installer->getVendorDir() . '/' . $this->assets_installer->getAssetsDbFilename();
        $this->assets_installer->getIo()->write( 
            sprintf('Writing assets json DB to <info>%s</info>',
                str_replace(dirname($this->assets_installer->getVendorDir()).'/', '', $assets_file)
            )
        );
        try {
            $json = new JsonFile($assets_file);
            $json->write($full_db);
            return $assets_file;
        } catch(\Exception $e) {
            if (file_put_contents($assets_file, json_encode($full_db, version_compare(PHP_VERSION, '5.4')>0 ? JSON_PRETTY_PRINT : 0))) {
                return $assets_file;
            }
        }        
        return false;
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param array $package The package, Composer\Package\PackageInterface
     * @return void
     */
    public function parseComposerExtra(PackageInterface $package, $package_dir)
    {
        $data = parent::parseComposerExtra($package, $package_dir);

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