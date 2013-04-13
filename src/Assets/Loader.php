<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Loader
{

    // assets
    const DEFAULT_VENDOR_DIR = 'vendor';
    const DEFAULT_ASSETS_DIR = 'www';
    const ASSETS_DB_FILENAME = 'assets.json';

    /**
     * Project root directory (absolute)
     */
    protected $base_dir;

    /**
     * Project assets directory (relative to `$base_dir`)
     */
    protected $assets_dir;

    /**
     * Project assets DB array
     */
    protected $assets_db;

// ---------------------
// Construction
// ---------------------

    public function __construct($base_dir = null, $assets_dir = null)
    {
        $this->base_dir = !is_null($base_dir) ? $base_dir : __DIR__.'/../../../../../';
        if (file_exists($this->base_dir)) {
            $this->base_dir = realpath($this->base_dir);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Project base directory "%s" not found!', $this->base_dir)
            );
        }

        $composer = $this->base_dir . '/composer.json';
        $vendor_dir = self::DEFAULT_VENDOR_DIR;
        if (file_exists($composer)) {
            $package = json_decode(file_get_contents($composer), true);
            if (isset($package['config']) && isset($package['config']['vendor-dir'])) {
                $vendor_dir = $package['config']['vendor-dir'];
            }
        }

        $db_file = $this->base_dir . '/' . $vendor_dir . '/' . self::ASSETS_DB_FILENAME;
        if (file_exists($db_file)) {
            $json_db = json_decode(file_get_contents($db_file), true);
            $this->assets_dir = $json_db['assets_dir'];
            $this->assets_db = $json_db['packages'];
        } else {
            throw new \Exception(
                sprintf('Assets json DB "%s" not found!', $db_file)
            );
        }

        if (!is_null($assets_dir)) {
            $this->assets_dir = $assets_dir;
        }
        $this->assets_dir = rtrim($this->base_dir, '/')===rtrim($this->assets_dir, '/') ?
            '' : str_replace($this->base_dir . '/', '', $this->assets_dir);
    }

// ---------------------
// Global getters
// ---------------------

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
        return isset($this->assets_db[$package_name]) ? $this->assets_db[$package_name]['path'] : null;
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
        return isset($this->assets_db[$package_name]) ? $this->buildWebPath($this->assets_db[$package_name]['path']) : null;
    }
    
// ---------------------
// Assets finder
// ---------------------

    public function find($filename, $package = null)
    {
        if (!is_null($package)) {
            return $this->findInPackage($filename, $package);
        }
    }

    public function findInPackage($filename, $package)
    {
        $package_path = $this->getPackageAssetsPath($package);
        if (!is_null($package_path)) {
            $asset_path = $package_path . '/' . $filename;
            if (file_exists($asset_path)) {
                return $this->buildWebPath($asset_path);
            }
        } else {
            return null;
        }
    }

}

// Endfile