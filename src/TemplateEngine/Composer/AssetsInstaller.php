<?php
/**
 * CarteBlanche - PHP framework package - Installers package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/carte-blanche>
 */

namespace TemplateEngine\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Autoload\AutoloadGenerator;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Composer\Util\Filesystem;
use Composer\Script\Event;
use Composer\Script\EventDispatcher;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class AssetsInstaller extends AutoloadGenerator
{

    // assets
    const DEFAULT_ASSETS_DIR = 'www';

    protected $composer;
    protected $config;
    protected $package;
    protected $assets_dir;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->config = $composer->getConfig();
        $this->package = $composer->getPackage();
        parent::__construct($composer->getEventDispatcher());
    }

    /**
     */
    public static function postAutoloadDump(Event $event)
    {
        $_this = new AssetsInstaller($event->getComposer(), $event->getIO());
        if (false!=$_this->moveAssets()) {
            $io->write( 'Assets moved to '.$_this->assets_dir );
        } else {
            $io->write( 'ERROR while trying to move assets!' );
        }
    }

    public function moveAssets()
    {
        $filesystem = new Filesystem();
        $vendorDir = $this->config->get('vendor-dir');
        $vendorPath = strtr(realpath($vendorDir), '\\', '/');
        $appBasePath = str_replace($vendorDir, '', $vendorPath);
        $relVendorPath = $filesystem->findShortestPath(getcwd(), $vendorPath, true);

        $localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        $packageMap = $this->buildPackageMap($this->composer->getInstallationManager(), $this->package, $localRepo->getPackages());
var_export($packageMap);
exit('yo');


    }

}

// Endfile