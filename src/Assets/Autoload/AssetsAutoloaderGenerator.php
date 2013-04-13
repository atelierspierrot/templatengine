<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace Assets\Autoload;

use Assets\ComposerInstaller;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class AssetsAutoloaderGenerator
{

    protected $assets_installer;

    public function __construct(ComposerInstaller $installer)
    {
        $this->assets_installer = $installer;
    }

    protected function _getAssetsDbPath()
    {
        return ($this->installer->vendorDir ? $this->installer->vendorDir.'/' : '') . $this->installer->assetsDbFilename;
    }

    public function generate()
    {
        return file_put_contents($this->_getAssetsDbPath(), json_encode($this->installer->assets_db));
    }
    
}

// Endfile