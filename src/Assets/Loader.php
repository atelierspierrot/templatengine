<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013-2014 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <http://github.com/atelierspierrot/templatengine>
 */

namespace Assets;

use \Library\Helper\Filesystem as FilesystemHelper;
use \Library\Helper\Directory as DirectoryHelper;
use \AssetsManager\Loader as AssetsLoader;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Loader
    extends AssetsLoader
{

    protected $web_root_path;
    protected $cache_path;
    protected $assets_cache_path;

// --------------------------
// Global Setters / Getters
// --------------------------

    /**
     * Set the web root path
     *
     * @param   string $path The path to the web root directory
     * @return  self
     * @throws  \InvalidArgumentException if the path doesn't exist
     */
    public function setWebRootPath( $path )
    {
        if (@file_exists($path) && is_dir($path)) {
            $this->web_root_path = realpath($path).'/';
        } else {
            throw new \InvalidArgumentException(
                sprintf('Web root path "%s" was not found or is not a directory!', $path)
            );
        }
        return $this;
    }

    /**
     * Get the web root path
     *
     * @return string The object web root path
     */
    public function getWebRootPath()
    {
        return $this->web_root_path;
    }

    /**
     * Set the cache path (absolute or relative form the WebRootPath)
     *
     * @param   string $path The path to the web cache directory
     * @return  self
     * @throws  \InvalidArgumentException if the path doesn't exist
     */
    public function setCachePath( $path )
    {
        if (@file_exists($path) && is_dir($path)) {
            $this->cache_path = realpath($path).'/';
        } elseif (null!==$path_rp = $this->findRealPath($path)) {
            $this->cache_path = rtrim($path_rp, '/').'/';
        } else {
            throw new \InvalidArgumentException(
                sprintf('Cache path "%s" was not found or is not a directory!', $path)
            );
        }
        return $this;
    }

    /**
     * Get the web cache path
     *
     * @return string The object web cache path
     */
    public function getCachePath()
    {
        return $this->cache_path;
    }

    /**
     * Set the cache path for assets (absolute or relative form the WebRootPath)
     *
     * @param   string $path The path to the web assets cache directory
     * @return  self
     * @throws  \InvalidArgumentException if the path doesn't exist
     */
    public function setAssetsCachePath( $path )
    {
        if (@file_exists($path) && is_dir($path)) {
            $this->assets_cache_path = realpath($path).'/';
        } elseif (null!==$path_rp = $this->findRealPath($path)) {
            $this->assets_cache_path = rtrim($path_rp, '/').'/';
        } else {
            throw new \InvalidArgumentException(
                sprintf('Assets cache path "%s" was not found or is not a directory!', $path)
            );
        }
        return $this;
    }

    /**
     * Get the web cache path for assets
     *
     * @return string The object web assets cache path
     */
    public function getAssetsCachePath()
    {
        return $this->assets_cache_path;
    }

// --------------------------
// AssetObjects
// --------------------------

    /**
     * Get a template object class name
     *
     * @param   string $_type The template object type
     * @return  string The template object class name
     */
    public function getAssetObjectClassName( $_type )
    {
        $parent = '\AssetsManager\AssetObject\\'.ucfirst($_type);
        if (@class_exists($parent)) {
            return $parent;
        }
        return '\Assets\AssetObject\\'.ucfirst($_type);
    }

// --------------------------
// Utilities
// --------------------------

    /**
     * Find an asset file relative path web ready
     *
     * @param   string $file_path The file path to search
     * @return  string The relative web ready path for this file, null otherwise
     */
    public function findAsset( $file_path )
    {
        $real_path = $this->findRealPath( $file_path );
        if ($real_path) {
            return trim(FilesystemHelper::resolveRelatedPath($this->web_root_path, $real_path), '/');
        }
        return null;
    }

}

// Endfile