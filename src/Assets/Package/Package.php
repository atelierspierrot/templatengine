<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Package;

use InvalidArgumentException;

use Library\Helper\Directory as DirectoryHelper;

use AssetsManager\Loader as AssetsLoader,
    AssetsManager\Package\AssetsPackage,
    AssetsManager\Package\AbstractAssetsPackage,
    AssetsManager\Package\Preset;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Package extends AssetsPackage
{

    /**
     * Current package layouts paths (relative to `$relative_path`)
     * @var array
     */
    protected $layouts_paths;

    /**
     * Current package views paths (relative to `$relative_path`)
     * @var array
     */
    protected $views_paths;

    /**
     * Current package views aliases files (relative to `$relative_path`)
     * @var array
     */
    protected $views_functions_paths;

    /**
     * Reset the package to empty values (except for global package)
     *
     * @return void
     */
    public function reset()
    {
        parent::reset();
        $this->layouts_paths            = array();
        $this->views_paths              = array();
        $this->views_functions_paths    = array();
    }

// -------------------------
// Setters / Getters
// -------------------------

    /**
     * @param array $paths
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     */
    public function setLayoutsPaths(array $paths, $type = 'vendor')
    {
        foreach ($paths as $path) {
            $this->addLayoutsPath($path, $type);
        }
        return $this;
    }

    /**
     * @param string $path Relative to `vendor`
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     * @throws `InvalidArgumentException` if the path doesn't exist
     */
    public function addLayoutsPath($path, $type = 'vendor')
    {
        $realpath = $this->getFullPath($path, $type);
        if (@file_exists($realpath) && is_dir($realpath)) {
            if (!in_array($path, $this->layouts_paths)) {
                $this->layouts_paths[] = $path;
            }
        } else {
            $relative_path = DirectoryHelper::slashDirname($this->getRelativePath()) . $path;
            $realpath = $this->getFullPath($relative_path, null);
            if (@file_exists($realpath) && is_dir($realpath)) {
                if (!in_array($relative_path, $this->layouts_paths)) {
                    $this->layouts_paths[] = $relative_path;
                }
            } else {
                throw new InvalidArgumentException(
                    sprintf('Views path directory "%s" for package "%s" not found !', $path, $this->getName())
                );
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getLayoutsPaths()
    {
        return $this->layouts_paths;
    }

    /**
     * @param array $paths
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     */
    public function setViewsPaths(array $paths, $type = 'vendor')
    {
        foreach ($paths as $path) {
            $this->addViewsPath($path, $type);
        }
        return $this;
    }

    /**
     * @param string $path Relative to `vendor`
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     * @throws `InvalidArgumentException` if the path doesn't exist
     */
    public function addViewsPath($path, $type = 'vendor')
    {
        $realpath = $this->getFullPath($path, $type);
        if (@file_exists($realpath) && is_dir($realpath)) {
            if (!in_array($path, $this->views_paths)) {
                $this->views_paths[] = $path;
            }
        } else {
            $relative_path = DirectoryHelper::slashDirname($this->getRelativePath()) . $path;
            $realpath = $this->getFullPath($relative_path, null);
            if (@file_exists($realpath) && is_dir($realpath)) {
                if (!in_array($relative_path, $this->views_paths)) {
                    $this->views_paths[] = $relative_path;
                }
            } else {
                throw new InvalidArgumentException(
                    sprintf('Views path directory "%s" for package "%s" not found !', $path, $this->getName())
                );
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getViewsPaths()
    {
        return $this->views_paths;
    }

    /**
     * @param array $paths
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     */
    public function setViewsFunctionsPaths(array $paths, $type = 'vendor')
    {
        foreach ($paths as $path) {
            $this->addViewsFunctionsPath($path, $type);
        }
        return $this;
    }

    /**
     * @param string $path Relative to `vendor`
     * @param string $type Type of the original relative path (can be `asset` or `vendor` or `null` - default is `vendor`)
     * @return self
     * @throws `InvalidArgumentException` if the path doesn't exist
     */
    public function addViewsFunctionsPath($path, $type = 'vendor')
    {
        $realpath = $this->getFullPath($path, $type);
        if (@file_exists($realpath) && is_file($realpath)) {
            if (!in_array($path, $this->views_functions_paths)) {
                $this->views_functions_paths[] = $path;
            }
        } else {
            $relative_path = DirectoryHelper::slashDirname($this->getRelativePath()) . $path;
            $realpath = $this->getFullPath($relative_path, null);
            if (@file_exists($realpath) && is_file($realpath)) {
                if (!in_array($relative_path, $this->views_functions_paths)) {
                    $this->views_functions_paths[] = $relative_path;
                }
            } else {
                throw new InvalidArgumentException(
                    sprintf('Views functions file "%s" for package "%s" not found !', $path, $this->getName())
                );
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getViewsFunctionsPaths()
    {
        return $this->views_functions_paths;
    }

// -------------------------
// Autobuilder
// -------------------------

    /**
     * Get all necessary arranged package infos as an array
     *
     * This is the data stored in the `Loader\Assets::ASSETS_DB_FILENAME`.
     *
     * @return array
     */    
    public function getArray()
    {
        $package = array(
            'name'=>$this->getName(),
            'version'=>$this->getVersion(),
            'relative_path'=>$this->getRelativePath(),
            'assets_path'=>$this->getAssetsPath(),
            'assets_presets'=>$this->getAssetsPresets(),
            'layouts_path'=>$this->getLayoutsPaths(),
            'views_path'=>$this->getViewsPaths(),
            'views_functions'=>$this->getViewsFunctionsPaths(),
        );
        return $package;
    }

    /**
     * Load a new package from the `Loader\Assets::ASSETS_DB_FILENAME` entry
     *
     * @param array
     * @return self
     */
     public function loadFromArray(array $entries)
     {
        foreach ($entries as $var=>$val) {
            switch ($var) {
                case 'name': $this->setName($val); break;
                case 'version': $this->setVersion($val); break;
                case 'relative_path': $this->setRelativePath($val); break;
                case 'assets_path':
                case 'path':
                    $this->setAssetsPath($val); break;
                case 'assets_presets': $this->setAssetsPresets($val); break;
                case 'layouts_path': $this->setLayoutsPaths(is_array($val) ? $val : array($val), null); break;
                case 'views_path': $this->setViewsPaths(is_array($val) ? $val : array($val), null); break;
                case 'views_functions': $this->setViewsFunctionsPaths(is_array($val) ? $val : array($val), null); break;
            }
        }
        return $this;
     }

}

// Endfile