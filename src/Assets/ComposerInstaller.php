<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets;

use Composer\Composer,
    Composer\IO\IOInterface,
    Composer\Autoload\AutoloadGenerator,
    Composer\Package\PackageInterface,
    Composer\Repository\RepositoryInterface,
    Composer\Script\Event,
    Composer\Script\EventDispatcher;

use Assets\Util\Filesystem,
    Assets\Loader as AssetsLoader,
    Assets\Autoload\AssetsAutoloaderGenerator;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class ComposerInstaller
    extends AutoloadGenerator
{

    /**
     * @var object Composer\Composer
     */
    protected $composer;

    /**
     * @var object Composer\IO\IOInterface
     */
    protected $io;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var object Composer\Package\PackageInterface
     */
    protected $package;

    /**
     * @var object Assets\Util\Filesystem
     */
    protected $filesystem;

    /**
     * The package `composer.json` "extra" block
     * @var array
     */
    public $packageExtra;

    /**
     * The assets directory realpath
     * @var string
     */
    public $assetsDir;

    /**
     * The assets database file realpath
     * @var string
     */
    public $assetsDbFilename;

    /**
     * The vendor dir realpath
     * @var string
     */
    public $vendorDir;

    /**
     * The application base realpath
     * @var string
     */
    public $appBasePath;

    /**
     * The assets Document Root
     * @var string
     */
    public $documentRoot;

    /**
     * Array filled like 'package_name' => 'package assets infos' used to write the json AssetsDb file
     * @var array
     */
    protected $assets_db = array();

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
                        sprintf('Assets json DB written in "%s".', str_replace($_this->appBasePath, '', $_assetsDbPath))
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
     * Construction of a new non-static ComposerInstaller
     *
     * @param object Composer\Composer
     * @param object Composer\IO\IOInterface
     * @return void
     */
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
        if (!empty($extra)) {
            $this->_parsePackageExtra($this->package, true);
        }
        $this->packageExtra = $extra;
        $this->assetsDir = isset($extra['assets']) ? $extra['assets'] : AssetsLoader::DEFAULT_ASSETS_DIR;
        $this->documentRoot = isset($extra['document_root']) ? $extra['document_root'] : AssetsLoader::DEFAULT_DOCUMENT_ROOT;
        $this->assetsDbFilename = AssetsLoader::ASSETS_DB_FILENAME;
    }

    /**
     * Get the assets database
     *
     * @return array
     */
    public function getAssetsDb()
    {
        return $this->assets_db;
    }

    /**
     * Get the install directory realpath of a package
     *
     * @param object $package Composer\Package\PackageInterface
     * @return string
     */
    protected function _getInstallPath(PackageInterface $package)
    {
        return $this->getAssetsRootPath() . '/' . $package->getPrettyName();
    }

    /**
     * Get the root directory realpath of package's assets
     *
     * @return string
     */
    public function getAssetsRootPath()
    {
        $path = $this->appBasePath . '/' . $this->assetsDir;
        $this->filesystem->ensureDirectoryExists($path);
        return $path;
    }

    /**
     * Get the base directory realpath of a package
     *
     * @param object $package Composer\Package\PackageInterface
     * @return string
     */
    protected function _getPackageBasePath(PackageInterface $package)
    {
        return ($this->vendorDir ? $this->vendorDir.'/' : '') . $package->getPrettyName();
    }

    /**
     * Get the relative assets directory of a package
     *
     * @param object $package Composer\Package\PackageInterface
     * @return string
     */
    protected function _getRelativePath(PackageInterface $package)
    {
        return str_replace($this->getAssetsRootPath(), '', $this->_getPackageBasePath($package));
    }

    /**
     * Copy the assets of installed packages in the assets directory
     *
     * @return void
     */
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

    /**
     * Build the package installation database file
     *
     * @return bool
     * @see Assets\Autoload\AssetsAutoloaderGenerator
     */
    protected function _generateAssetsDb()
    {
        $generator = new AssetsAutoloaderGenerator($this);
        return $generator->generate();
    }

    /**
     * Move the assets of a package
     *
     * @param object $package Composer\Package\PackageInterface
     * @return bool
     */
    protected function _movePackageAssets(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!empty($extra) && isset($extra['assets'])) {
            $from = $this->_getPackageBasePath($package) . '/' . $extra['assets'];
            $target = $this->_getInstallPath($package);
            if (file_exists($from)) {
                $this->filesystem->copy($from, $target);
                $this->_parsePackageExtra($package);
            } else {
                throw new \Exception(
                    'Unable to find assets in package "'.$package->getPrettyName().'"'
                );
            }
            return true;
        }
        return false;
    }
    
    /**
     * Parse the `composer.json` "extra" block of a package and set it to the `assets_db`
     *
     * @param object $package Composer\Package\PackageInterface
     * @param bool $main_package Is this the global package
     * @return void
     */
    protected function _parsePackageExtra(PackageInterface $package, $main_package = false)
    {
        $extra = $package->getExtra();
        if (!empty($extra) && isset($extra['assets'])) {
            $infos = array(
                'path'=>$main_package ? rtrim($this->getAssetsRootPath(), '/') . '/' . $extra['assets'] : $this->_getInstallPath($package),
                'version'=>$package->getVersion(),
            );
            if (isset($extra['views'])) {
                $infos['views'] = ($main_package ? $this->appBasePath . '/' : $this->_getPackageBasePath($package).'/') . $extra['views'];
            }
            if (isset($extra['views_aliases'])) {
                $infos['views_aliases'] = ($main_package ? $this->appBasePath . '/' : $this->_getPackageBasePath($package).'/') . $extra['views_aliases'];
            }
            if (isset($extra['assets_packages'])) {
                $use = array();
                foreach ($extra['assets_packages'] as $index=>$item) {
                    $use_item = array();
                    foreach (AssetsLoader::$use_statements as $statement) {
                        if (isset($item[$statement])) {
                            $item_statement = is_array($item[$statement]) ?
                                $item[$statement] : array($item[$statement]);
                            $use_item[$statement] = array();
                            foreach ($item_statement as $path) {
                                $use_item[$statement][] = 
                                    ($main_package ? '' : $this->_getRelativePath($package).'/') . $path;
                            }
                            $use[$index] = $use_item;
                        }
                    }
                }
                if (!empty($use)) {
                    $infos['assets_packages'] = $use;
                }
            }
            $this->assets_db[$package->getPrettyName()] = $infos;
        }
    }
    
}

// Endfile