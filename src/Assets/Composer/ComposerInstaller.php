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
    AssetsManager\Composer\Autoload\AssetsAutoloaderGenerator,
    AssetsManager\Composer\Util\Filesystem as UtilFilesystem;

use Assets\Package\AssetsPackage;

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
        $_this = new ComposerInstaller($event->getComposer(), $event->getIO());
        if (false!==$ok_assets = $_this->moveAssets()) {
            if ($ok_assets>0) {
                if (false!==$_assetsDbPath = $_this->_generateAssetsDb()) {
                    $_this->io->write( 
                        sprintf('Writing assets json DB to <info>%s</info>.',
                        str_replace(rtrim($_this->app_base_path, '/').'/', '', $_assetsDbPath))
                    );
                } else {
                    $_this->io->write('ERROR while trying to create assets DB file!');
                }
            }
        } else {
            $_this->io->write('ERROR while trying to move assets!');
        }
    }

    /**
     * Initializes installer: creation of "assets-dir" directory if so.
     *
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library')
    {
        parent::__construct($io, $composer, $type);

        $package = $composer->getPackage();
        $extra = $this->package->getExtra();
        if (!empty($extra) && !empty($extra['assets'])) {
            $this->assets_db[$package->getPrettyName()] = 
                $this->parseComposerExtra($package, $this->app_base_path);
        }
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