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

namespace TemplateEngine\TemplateObject\Abstracts;

use \Patterns\Commons\Registry;
use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\Template;
use \Assets\Compressor;

/**
 * @author  piwi <me@e-piwi.fr>
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
            ->setWebRootPath($this->__template->getWebRootPath())
            ->setDestinationDir($this->__template->getAssetsCachePath());
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
        if (false===$silent) {
            $this->__compressor->setSilent(false);
        }
        if (true===$direct_output) {
            $this->__compressor->setDirectOutput(true);
        }

        return $this->__compressor
            ->setFilesStack($stack)
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
        if (false===$silent) {
            $this->__compressor->setSilent(false);
        }
        if (true===$direct_output) {
            $this->__compressor->setDirectOutput(true);
        }

        return $this->__compressor
            ->setFilesStack($stack)
            ->minify()
            ->getDestinationWebPath();
    }
}
