<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets;

use Library\Helper\Filesystem as FilesystemHelper;

define('_SERVER_DOCROOT', $_SERVER['DOCUMENT_ROOT']);

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Loader
{

    // defaults
    const DEFAULT_VENDOR_DIR = 'vendor';
    const DEFAULT_ASSETS_DIR = 'www';
    const DEFAULT_DOCUMENT_ROOT = _SERVER_DOCROOT;
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
     * The document root path (absolute, used to build assets web path)
     */
    protected $document_root;

    /**
     * Project assets DB array
     */
    protected $assets_db;

// ---------------------
// Construction
// ---------------------

    public function __construct($base_dir = null, $assets_dir = null, $document_root = null)
    {
        $this->setBaseDirectory(!is_null($base_dir) ? $base_dir : __DIR__.'/../../../../../');

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
            $this
                ->setAssetsDirectory(!is_null($assets_dir) ? $assets_dir : $json_db['assets_dir'])
                ->setDocumentRoot(!is_null($document_root) ? $document_root : $json_db['document_root'])
                ->setAssetsDb($json_db['packages']);
        } else {
            throw new \Exception(
                sprintf('Assets json DB "%s" not found!', $db_file)
            );
        }
    }

// ---------------------
// Setters / Getters
// ---------------------

    public function setBaseDirectory($path)
    {
        if (file_exists($path)) {
            $this->base_dir = realpath($path);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Base directory "%s" not found!', $path)
            );
        }
        return $this;
    }
    
    public function getBaseDirectory()
    {
        return $this->base_dir;
    }

    public function setAssetsDirectory($path)
    {
        if (file_exists($path)) {
            $this->assets_dir = rtrim($this->base_dir, '/')===rtrim($path, '/') ?
                '' : str_replace($this->base_dir . '/', '', realpath($path));
        } else {
            throw new \InvalidArgumentException(
                sprintf('Assets directory "%s" not found!', $path)
            );
        }
        return $this;
    }
    
    public function getAssetsDirectory()
    {
        return $this->assets_dir;
    }

    public function setDocumentRoot($path)
    {
        if (file_exists($path)) {
            $this->document_root = realpath($path);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Document root path "%s" doesn\'t exist!', $path)
            );
        }
        return $this;
    }
    
    public function getDocumentRoot()
    {
        return $this->document_root;
    }

    public function setAssetsDb(array $db)
    {
        $this->assets_db = $db;
        return $this;
    }
    
    public function getAssetsDb()
    {
        return $this->assets_db;
    }

// ---------------------
// Global getters
// ---------------------

    public function buildWebPath($path)
    {
        return trim(FilesystemHelper::resolveRelatedPath($this->getDocumentRoot(), realpath($path)), '/');
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
        return $this->getAssetsDb();
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
        } else {
            return $this->findInPath($filename, $this->getAssetsPath());
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
        }
        return null;
    }

    public function findInPath($filename, $path)
    {
        $asset_path = rtrim($path, '/') . '/' . $filename;
        if (file_exists($asset_path)) {
            return $this->buildWebPath($asset_path);
        }
        return null;
    }
}

// Endfile