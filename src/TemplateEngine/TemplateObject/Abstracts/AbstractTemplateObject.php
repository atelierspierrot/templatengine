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

namespace TemplateEngine\TemplateObject\Abstracts;

use \TemplateEngine\Template;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractTemplateObject
{

    /**
     * The whole template object
     */
    protected $__template;

    /**
     * Constructor
     *
     * @param \TemplateEngine\Template $_tpl The whole template object
     */
    public function __construct( Template $_tpl )
    {
        $this->__template = $_tpl;
        $this->init();
    }

    /**
     * Write the Template Object strings ready for template display
     */
    public function __toString()
    {
        return $this->write();
    }

    /**
     * Clean a stack (an array) leaving just one set of an entry for the $clean_by variable
     *
     * @param array $stack The stack to clean
     * @param string $clean_by The variable name to check
     * @return array Return the stack cleaned with only one instance of $clean_by
     */
    protected function cleanStack(array $stack, $clean_by = null)
    {
        $new_stack = array();
        foreach($stack as $_entry) {
            if (is_array($_entry) && !empty($clean_by)) {
                if (isset($_entry[$clean_by]) && !array_key_exists($_entry[$clean_by], $new_stack))
                    $new_stack[ $_entry[$clean_by] ] = $_entry;
            } elseif (is_string($_entry)) {
                $ok = array_search($_entry, $new_stack);
                if (false===$ok)
                    $new_stack[] = $_entry;
            }
        }
        return array_values($new_stack);
    }

    /**
     * Build a stack (an array) leaving just one value of an entry searching a $clean_by index
     *
     * @param array $stack The stack to clean
     * @param string $clean_by The variable name to check
     * @return array Return the extracted stack
     */
    protected function extractFromStack(array $stack, $clean_by)
    {
        $new_stack = array();
        foreach($stack as $_entry) {
            if (is_array($_entry) && isset($_entry[$clean_by])) {
                $new_stack[] = $_entry[$clean_by];
            }
        }
        return $new_stack;
    }

}

// Endfile