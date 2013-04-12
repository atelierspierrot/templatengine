<?php
/**
 * CarteBlanche - PHP framework package - Installers package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/carte-blanche>
 */

namespace CarteBlanche\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

/**
 * The framework internal Composer "Tools" installer
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class ToolInstaller extends LibraryInstaller
{

    /**
     * Initializes installer: creation of "tools/" directory if so.
     *
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library')
    {
        parent::__construct($io, $composer, $type);
        $this->filesystem->ensureDirectoryExists(CarteBlancheInstaller::CARTEBLANCHE_TOOLS_DIR);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === CarteBlancheInstaller::CARTEBLANCHE_TOOLTYPE;
    }

    /**
     * Determines the install path for templates,
     *
     * The installation path is determined by checking whether the package is included in another composer configuration
     * or installed as part of the normal CarteBlanche installation.
     *
     * When the package is included as part of a different project it will be installed in the `src/tools` folder
     * of phpDocumentor (thus `/atelierspierrot/carte-blanche/src/tools`); if it is installed as part of
     * CarteBlanche it will be installed in the root of the project (thus `/src/tools`).
     *
     * @param PackageInterface $package
     * @throws \InvalidArgumentException if the name of the package does not start with `carte-blanche/tool-`.
     * @return string a path relative to the root of the composer.json that is being installed.
     */
    public function getInstallPath(PackageInterface $package)
    {
        if ($this->extractPrefix($package) != CarteBlancheInstaller::CARTEBLANCHE_TOOLNAME) {
            throw new \InvalidArgumentException(
                'Unable to install tool, CarteBlanche tools should always start their package name with '
                .'"'.CarteBlancheInstaller::CARTEBLANCHE_TOOLNAME.'"'
            );
        }
        $tool_name = $this->extractShortName($package);
        if ('all'===$tool_name) {
            return $this->getToolRootPath();
        } else {
            return $this->getToolRootPath() . '/' . $tool_name;
        }
    }

    /**
     * Extract the first 19 characters ("carte-blanche/tool-") of the package name; which is expected to be the prefix.
     *
     * @param PackageInterface $package
     * @return string
     */
    protected function extractPrefix(PackageInterface $package)
    {
        return substr($package->getPrettyName(), 0, strlen(CarteBlancheInstaller::CARTEBLANCHE_TOOLNAME));
    }

    /**
     * Extract the everything after the first 19 characters of the package name; which is expected to be the short name.
     *
     * @param PackageInterface $package
     * @return string
     */
    protected function extractShortName(PackageInterface $package)
    {
        return substr($package->getPrettyName(), strlen(CarteBlancheInstaller::CARTEBLANCHE_TOOLNAME));
    }

    /**
     * Returns the root installation path for templates.
     *
     * @return string a path relative to the root of the composer.json that is being installed where the templates
     *     are stored.
     */
    protected function getToolRootPath()
    {
        return (file_exists($this->vendorDir . '/atelierspierrot/carte-blanche/composer.json'))
            ? $this->vendorDir . '/atelierspierrot/carte-blanche/src/tools'
            : 'src/tools';
    }

}

// Endfile