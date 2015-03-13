<?php
/**
 * This file is part of the TemplateEngine package.
 *
 * Copyright (c) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
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

namespace TemplateEngine\TemplateObject;

use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface;
use \Library\Helper\Html;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class TitleTag
    extends AbstractTemplateObject
    implements TemplateObjectInterface
{

// ------------------------
// TemplateObjectInterface
// ------------------------

    /**
     * Init the object
     */
    public function init()
    {
        $this->reset();
    }

    /**
     * Reset the object
     *
     * @return self
     */
    public function reset()
    {
        $this->__template->registry->header_title = array();
        return $this;
    }

    /**
     * Add a string to build page header title
     *
     * @param string $title The title string
     * @return self
     */
    public function add($title)
    {
        $this->__template->registry->addEntry( $title, 'header_title');
        return $this;
    }

    /**
     * Set a strings stack to build page header title
     *
     * @param array $strs An array of title strings
     * @return self
     * @see self::add()
     */
    public function set(array $strs)
    {
        if (!empty($strs)) {
            foreach($strs as $_str) {
                $this->add( $_str );
            }
        }
        return $this;
    }

    /**
     * Get the titles stack
     *
     * @return array The stack of title strings
     */
    public function get()
    {
        return $this->__template->registry->getEntry( 'header_title', false, array() );
    }

    /**
     * Write the Template Object strings ready for template display
     *
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string The string to display fot this template object
     */
    public function write($mask = '%s')
    {
        $str='';
        foreach($this->cleanStack( $this->get() ) as $entry) {
            $str .= (strlen($str)>0 ? $this->separator : '').$entry;
        }
        $title_str = Html::writeHtmlTag( 'title', strip_tags($str) );
        return sprintf($mask, $title_str);
    }

// ------------------------
// Custom methods
// ------------------------

    public $separator = ' - ';

    /**
     * Set a separator string (used to join each title strings)
     *
     * @param string $str The string separator
     * @return self
     */
    public function setSeparator($str)
    {
        $this->separator = $str;
        return $this;
    }

}

// Endfile