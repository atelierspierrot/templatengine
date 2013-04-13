<?php
/**
 * CarteBlanche - PHP framework package - Installers package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/carte-blanche>
 */

namespace Assets;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Autoload\AutoloadGenerator;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Composer\Script\Event;
use Composer\Script\EventDispatcher;

use Assets\Util\Filesystem;
use Assets\Autoload\AssetsAutoloader;
use Assets\Autoload\AssetsAutoloaderGenerator;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class ComposerInstaller extends AutoloadGenerator
{

    protected $composer;
    protected $io;
    protected $config;
    protected $package;
    protected $filesystem;

    public $assetsDir;
    public $assetsDbFilename;
    public $vendorDir;
    public $appBasePath;

    /**
     * Array filled like 'package_name' => 'assets path' used to write the json AssetsDb file
     */
    protected $assets_db = array();

    /**
     */
    public static function postAutoloadDump(Event $event)
    {
        $_this = new AssetsInstaller($event->getComposer(), $event->getIO());
        if (false!==$ok_assets = $_this->moveAssets()) {
            if ($ok_assets>0) {
                if (false!==$_assetsDbPath = $_this->_generateAssetsDb()) {
                    $_this->io->write( 
                        sprintf('Assets json DB written in "%s".', $_assetsDbPath)
                    );
                } else {
                    $_this->io->write('ERROR while trying to create assets DB file!');
                }
            }
        } else {
            $_this->io->write('ERROR while trying to move assets!');
        }
    }

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->config = $composer->getConfig();
        $this->package = $composer->getPackage();
        parent::__construct($this->composer->getEventDispatcher());

        $this->filesystem = new Filesystem();
        $vendor_dir = $this->config->get('vendor-dir');
        $this->vendorDir = strtr(realpath($vendor_dir), '\\', '/');
        $this->appBasePath = rtrim(str_replace($vendor_dir, '', $this->vendorDir), '/');

        $extra = $this->package->getExtra();
        $this->assetsDir = isset($extra['assets']) ? $extra['assets'] : AssetsAutoloader::DEFAULT_ASSETS_DIR;
        $this->assetsDbFilename = isset($extra['assets_db']) ? $extra['assets_db'] : AssetsAutoloader::DEFAULT_ASSETS_DB;
    }

    protected function _getInstallPath(PackageInterface $package)
    {
        return $this->_getAssetsRootPath() . '/' . $package->getPrettyName();
    }

    protected function _getAssetsRootPath()
    {
        $path = $this->appBasePath . '/' . $this->assetsDir;
        $this->filesystem->ensureDirectoryExists($path);
        return $path;
    }

    protected function _getPackageBasePath(PackageInterface $package)
    {
        return ($this->vendorDir ? $this->vendorDir.'/' : '') . $package->getPrettyName();
    }

    public function moveAssets()
    {
        $ok = 0;
        $must_install = false;

        $localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        foreach($localRepo->getPackages() as $packageitem) {
            $extra = $packageitem->getExtra();
            if (!empty($extra) && isset($extra['assets'])) {
                $must_install = true;
                $this->io->write( 
                    sprintf('Installing assets to "%s" for package "%s".', $this->assetsDir, $packageitem->getPrettyName())
                );
                if ($this->_movePackageAssets($packageitem)) {
                    $ok++;
                }
            }
        }

        return true===$must_install ? $ok : true;
    }

    protected function _generateAssetsDb()
    {
        $generator = new AssetsAutoloaderGenerator($this);
        return $generator->generate();
    }

    protected function _movePackageAssets(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!empty($extra) && isset($extra['assets'])) {
            $from = $this->_getPackageBasePath($package) . '/' . $extra['assets'];
            $target = $this->_getInstallPath($package);
            if (file_exists($from)) {
                $this->filesystem->copy($from, $target);
                $this->assets_db[$package->getPrettyName()] = $target;
            } else {
                throw new \Exception(
                    'Unable to find assets in package "'.$package->getPrettyName().'"'
                );
            }
            return true;
        }
        return false;
    }
    
}

// Endfile