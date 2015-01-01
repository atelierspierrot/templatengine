<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyleft (ↄ) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * The source code of this package is available online at 
 * <http://github.com/atelierspierrot/templatengine>.
 */

namespace TemplateEngine\TemplateObject\Abstracts;

use \Patterns\Commons\Registry;
use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\Template;
use \Assets\Compressor;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractFileTemplateObject
    extends AbstractTemplateObject
{

    /**
     * The object Registry
     */
    protected $_registry;

    /**
     * The merger class object
     *
     * @var \Assets\Compressor
     */
    protected $__compressor;

    /**
     * Constructor
     *
     * @param \TemplateEngine\Template $_tpl The whole template object
     */
    public function __construct(Template $_tpl)
    {
        $this->registry = new Registry;
        $this->__template = $_tpl;
        $this->__compressor = new Compressor;
        $this->__compressor
            ->setWebRootPath( $this->__template->getWebRootPath() )
            ->setDestinationDir( $this->__template->getAssetsCachePath() );
        $this->init();
    }

    /**
     * Merge a stack of files
     *
     * @param array $stack The stack to clean
     * @param bool $silent Set up the Compressor $silence flag (default is true)
     * @param bool $direct_output Set up the Compressor $direct_output flag (default is false)
     * @return array Return the extracted stack
     */
    protected function mergeStack(array $stack, $silent = true, $direct_output = false)
    {
        $this->__compressor->reset();
        if (false===$silent)
            $this->__compressor->setSilent(false);
        if (true===$direct_output)
            $this->__compressor->setDirectOutput(true);

        return $this->__compressor
            ->setFilesStack( $stack )
            ->merge()
            ->getDestinationWebPath();
    }

    /**
     * Minify a stack of files
     *
     * @param array $stack The stack to clean
     * @param bool $silent Set up the Compressor $silence flag (default is true)
     * @param bool $direct_output Set up the Compressor $direct_output flag (default is false)
     * @return array Return the extracted stack
     */
    protected function minifyStack(array $stack, $silent = true, $direct_output = false)
    {
        $this->__compressor->reset();
        if (false===$silent)
            $this->__compressor->setSilent(false);
        if (true===$direct_output)
            $this->__compressor->setDirectOutput(true);

        return $this->__compressor
            ->setFilesStack( $stack )
            ->minify()
            ->getDestinationWebPath();
    }

}

// Endfile