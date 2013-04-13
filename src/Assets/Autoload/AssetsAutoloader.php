<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Autoload;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class AssetsAutoloader
{

    // assets
    const DEFAULT_ASSETS_DIR = 'www';
    const DEFAULT_ASSETS_DB = 'assets.json';

    /**
     * Project root directory (absolute)
     */
    protected $base_dir;

    /**
     * Project assets directory (relative to `$base_dir`)
     */
    protected $assets_dir;

    /**
     * Project assets DB filename
     */
    protected $assets_db_file;

    /**
     * Project assets DB array
     */
    protected $assets_db;

    public function __construct($base_dir = null, $assets_dir = null)
    {
        $this->base_dir = !is_null($base_dir) ? $base_dir : __DIR__.'/../../../../../../';
        if (file_exists($this->base_dir)) {
            $this->base_dir = realpath($this->base_dir);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Project base directory "%s" not found!', $this->base_dir)
            );
        }
        $this->_init();
        if (!is_null($assets_dir)) {
            $this->assets_dir = $assets_dir;
        }
        $this->assets_dir = str_replace($this->base_dir . '/', '', $this->assets_dir);
    }

    protected function _init()
    {
        $composer = $this->base_dir . '/composer.json';
        if (file_exists($composer)) {
            $package = json_decode(file_get_contents($composer), true);
            $extra = isset($package['extra']) ? $package['extra'] : array();
        } else {
            $package = $extra = array();
        }
        $this->assets_dir = isset($extra['assets']) ? $extra['assets'] : self::DEFAULT_ASSETS_DIR;
        $this->assets_db_file = isset($extra['assets_db']) ? $extra['assets_db'] : self::DEFAULT_ASSETS_DB;
        $vendor_dir = isset($package['config']) && isset($package['config']['vendor-dir']) ? $package['config']['vendor-dir'] : 'vendor';

        $db_file = $this->base_dir . '/' . $vendor_dir . '/' . $this->assets_db_file;
        if (file_exists($db_file)) {
            $this->assets_db = json_decode(file_get_contents($db_file), true);
        } else {
            throw new \Exception(
                sprintf('Assets json DB "%s" not found!', $db_file)
            );
        }
    }

    public function buildWebPath($path)
    {
        return trim(str_replace($this->getAssetsPath(), '', $path), '/');
    }
    
    public function getAssetsPath()
    {
        return $this->base_dir . '/' . $this->assets_dir;
    }
    
    public function getPackageAssetsPath($package_name)
    {
        return isset($this->assets_db[$package_name]) ? $this->assets_db[$package_name] : null;
    }
    
    public function getAssets()
    {
        return $this->assets_db;
    }
    
    public function getAssetsWebPath()
    {
        return $this->buildWebPath($this->getAssetsPath());
    }
    
    public function getPackageAssetsWebPath($package_name)
    {
        return isset($this->assets_db[$package_name]) ? $this->buildWebPath($this->assets_db[$package_name]) : null;
    }
    
}

// Endfile