<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2016 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace Assets\Composer;

use \Composer\Composer;
use \Composer\IO\IOInterface;
use \Composer\Autoload\AutoloadGenerator;
use \Composer\Package\PackageInterface;
use \Composer\Repository\RepositoryInterface;
use \Composer\Script\Event;
use \Composer\Script\EventDispatcher;
use \Library\Helper\Directory as DirectoryHelper;
use \AssetsManager\Config;
use \AssetsManager\Composer\Util\Filesystem;
use \AssetsManager\Composer\Installer\AssetsInstaller;
use \AssetsManager\Composer\Autoload\AssetsAutoloadGenerator;
use \Assets\Package\Package;
use \Assets\Composer\TemplateEngineConfig;
use \Assets\Composer\TemplateEngineInstaller;

/**
 * @author  piwi <me@e-piwi.fr>
 */
class TemplateEngineAutoloadGenerator
    extends AutoloadGenerator
{

    protected $_composer;
    protected $_autoloader;
    protected $_package;

    /**
     * @param \Composer\Package\PackageInterface $package
     * @param \Composer\Composer $composer
     */
    public function __construct(PackageInterface $package, Composer $composer)
    {
        parent::__construct($composer->getEventDispatcher());
        Config::load('Assets\Composer\TemplateEngineConfig');
        $this->_composer = $composer;
        $this->_autoloader = AssetsAutoloadGenerator::getInstance();
        $this->_autoloader->setGenerator(array($this, 'generate'));
        $this->_package = $package;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $full_db = $this->getFullDb();
        return $this->_autoloader->writeJsonDatabase($full_db);
    }

    /**
     * Build the complete database array
     *
     * @return array
     */
    public function getFullDb()
    {
        $filesystem = new Filesystem();
        $config = $this->_composer->getConfig();
        $assets_db = $this->_autoloader->getRegistry();
        $vendor_dir = $this->_autoloader->getAssetsInstaller()->getVendorDir();
        $app_base_path = $this->_autoloader->getAssetsInstaller()->getAppBasePath();
        $assets_dir = str_replace($app_base_path . '/', '', $this->_autoloader->getAssetsInstaller()->getAssetsDir());
        $assets_vendor_dir = str_replace($app_base_path . '/' . $assets_dir . '/', '', $this->_autoloader->getAssetsInstaller()->getAssetsVendorDir());
        $document_root = $this->_autoloader->getAssetsInstaller()->getDocumentRoot();
        $extra = $this->_package->getExtra();

        $root_data = $this->parseComposerExtra($this->_package, $app_base_path, '');
        if (!empty($root_data)) {
            $root_data['relative_path'] = '../';
            $assets_db[$this->_package->getPrettyName()] = $root_data;
        }

        $vendor_path = strtr(realpath($vendor_dir), '\\', '/');
        $rel_vendor_path = $filesystem->findShortestPath(getcwd(), $vendor_path, true);

        $local_repo = $this->_composer->getRepositoryManager()->getLocalRepository();
        $package_map = $this->buildPackageMap($this->_composer->getInstallationManager(), $this->_package, $local_repo->getPackages());

        foreach ($package_map as $i=>$package) {
            if ($i===0) {
                continue;
            }
            $package_object = $package[0];
            $package_install_path = $package[1];
            if (empty($package_install_path)) {
                $package_install_path = $app_base_path;
            }
            $package_name = $package_object->getPrettyName();
            $data = $this->parseComposerExtra(
                $package_object,
                $this->_autoloader->getAssetsInstaller()->getAssetsInstallPath($package_object),
                str_replace($app_base_path . '/', '', $vendor_path) . '/' . $package_object->getPrettyName()
            );
            if (!empty($data)) {
                $assets_db[$package_name] = $data;
            }
        }

        $full_db = array(
            'assets-dir' => $assets_dir,
            'assets-vendor-dir' => $assets_vendor_dir,
            'document-root' => $document_root,
            'cache-dir' => isset($extra['cache-dir']) ? $extra['cache-dir'] : Config::getDefault('cache-dir'),
            'cache-assets-dir' => isset($extra['cache-assets-dir']) ? $extra['cache-assets-dir'] : Config::getDefault('cache-assets-dir'),
            'packages' => $assets_db
        );
        return $full_db;
    }

    /**
     * Parse the `composer.json` "extra" block of a package and return its transformed data
     *
     * @param \Composer\Package\PackageInterface $package
     * @param string $assets_package_dir
     * @param string $vendor_package_dir
     * @return array|null
     */
    public function parseComposerExtra(PackageInterface $package, $assets_package_dir, $vendor_package_dir)
    {
        $data = $this->_autoloader->getAssetsInstaller()->parseComposerExtra($package, $assets_package_dir);
        if (is_null($data)) {
            $data = array();
        }
        $extra = $package->getExtra();
        $assets_package_dir = rtrim($assets_package_dir, '/') . '/';
        if (strlen($vendor_package_dir)) {
            $vendor_package_dir = rtrim($vendor_package_dir, '/') . '/';
        }

        $mapping = array(
            'layouts'=>'layouts_path',
            'views'=>'views_path',
            'views-functions'=>'views_functions'
        );
        foreach ($mapping as $json_name=>$var_name) {
            if (isset($extra[$json_name])) {
                $json_vals = is_array($extra[$json_name]) ? $extra[$json_name] : array($extra[$json_name]);
                $data[$var_name] = array();
                foreach ($json_vals as $json_val) {
                    $data[$var_name][] = $vendor_package_dir . $json_val;
                }
            }
        }
/*
        if (isset($extra['layouts'])) {
            $layouts = is_array($extra['layouts']) ? $extra['layouts'] : array($extra['layouts']);
            $data['layouts_path'] = array();
            foreach ($layouts as $layout_path) {
                $data['layouts_path'][] = $vendor_package_dir . $layout_path;
            }
        }

        if (isset($extra['views'])) {
            $views = is_array($extra['views']) ? $extra['views'] : array($extra['views']);
            $data['views_path'] = array();
            foreach ($views as $view_path) {
                $data['views_path'][] = $vendor_package_dir . $view_path;
            }
        }

        if (isset($extra['views-functions'])) {
            $views_fcts = is_array($extra['views-functions']) ? $extra['views-functions'] : array($extra['views-functions']);
            $data['views_functions'] = array();
            foreach ($views_fcts as $view_fct_path) {
                $data['views_functions'][] = $vendor_package_dir . $view_fct_path;
            }
        }
*/
        return !empty($data) ? $data : null;
    }
}
