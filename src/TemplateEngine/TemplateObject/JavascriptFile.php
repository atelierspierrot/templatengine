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

namespace TemplateEngine\TemplateObject;

use \TemplateEngine\TemplateEngineException;
use \TemplateEngine\TemplateObject\Abstracts\AbstractFileTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\FileTemplateObjectInterface;
use \Library\Helper\Html;

/**
 * @author  piwi <me@e-piwi.fr>
 * @TODO    Manage files priority
 */
class JavascriptFile
    extends AbstractFileTemplateObject
    implements FileTemplateObjectInterface
{

    protected $_default_stack_entry = array(
        'file'=>null,
        'path'=>null,
        'priority'=>0,
        'minified'=>false,
    );

    /**
     * Get a full formated stack entry
     *
     * @param string|array $file_path The new javascript path or an array like the `$_default_stack_entry`
     * @param int $priority The priority for the file in the global files stack
     * @param bool $is_minified Is the file content already minified (default is `false`)
     * @return self 
     * @throws \TemplateEngine\TemplateEngineException if the path doesn't exist
     */
    protected function _getStackEntry($file_path, $priority = null, $is_minified = null)
    {
        $stack_entry = $this->_default_stack_entry;

        if (is_array($file_path)) {
            $stack_entry = array_merge($stack_entry, $file_path);
            if (isset($stack_entry['is_minified'])) {
                $stack_entry['minified'] = $stack_entry['is_minified'];
                unset($stack_entry['is_minified']);
            }
        } else {
            $stack_entry['file'] = $file_path;
        }
        
        if (!is_null($priority)) {
            $stack_entry['priority'] = $priority;
        }
        
        if (!is_null($is_minified)) {
            $stack_entry['minified'] = $is_minified;
        }

        if (\AssetsManager\Loader::isUrl($stack_entry['file'])) {
            $stack_entry['path'] = $stack_entry['file'];
            return $stack_entry;
        }

        $_fp = $this->__template->findAsset($stack_entry['file']);
        if ($_fp) {
            $stack_entry['path'] = $_fp;
            return $stack_entry;
        } else {
            throw new TemplateEngineException(
                sprintf('Javascript file "%s" not found!', $file_path)
            );
        }
    }

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
        $this->registry->javascript_files = array();
        $this->registry->javascript_minified_files = array();
        return $this;
    }
    
    /**
     * Add a javascript file in javascript stack
     * 
     * @param string|array $file_path The new javascript path or an array like the `$_default_stack_entry`
     * @return self 
     * @throw Throws an InvalidArgumentException if the path doesn't exist
     */
    public function addIfExists($file_path)
    {
        $_fp = $this->__template->findAsset($file_path);
        if ($_fp || \AssetsManager\Loader::isUrl($file_path)) {
            return $this->add($file_path);
        }
        return $this;
    }
    
    /**
     * Add a javascript file in javascript stack
     * 
     * @param string|array $file_path The new javascript path or an array like the `$_default_stack_entry`
     * @return self 
     * @throw Throws an InvalidArgumentException if the path doesn't exist
     */
    public function add($file_path)
    {
        $this->registry->addEntry($this->_getStackEntry($file_path), 'javascript_files');
        return $this;
    }
    
    /**
     * Set a full javascript stack
     * 
     * @param array $files An array of javascript files paths
     * @return self 
     * @see self::add()
     */
    public function set(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                $this->add($_file);
            }
        }
        return $this;
    }
    
    /**
     * Get the javascript files stack
     * 
     * @return array The stack of javascript
     */
    public function get()
    {
        return $this->registry->getEntry('javascript_files', false, array());
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
        foreach ($this->cleanStack($this->get(), 'path') as $entry) {
            $tag_attrs = array(
                'type'=>'text/javascript',
                'src'=>$entry['path']
            );
            $str .= sprintf($mask, Html::writeHtmlTag('script', null, $tag_attrs));
        }
        return $str;
    }

// ------------------------
// FileTemplateObjectInterface
// ------------------------

    /**
     * Merge the files if possible and loads them in files_merged stack
     * 
     * @return self
     */
    public function merge()
    {
        $cleaned_stack = $this->cleanStack($this->get(), 'path');
        if (!empty($cleaned_stack)) {
            $this->addMerged(
                $this->mergeStack($this->extractFromStack($cleaned_stack, 'path'))
            );
        }
        return $this;
    }
    
    /**
     * Add an merged file
     * 
     * @param string $file_path The new javascript path
     * @return self 
     * @throw Throws an InvalidArgumentException if the path doesn't exist
     */
    public function addMerged($file_path)
    {
        $stack = $this->_getStackEntry($file_path, null, true);
        $this->registry->addEntry($stack, 'javascript_files');
        $this->registry->addEntry($stack, 'javascript_merged_files');
        return $this;
    }
    
    /**
     * Set a stack of merged files
     * 
     * @param array $files An array of javascript files paths
     * @return self 
     * @see self::add()
     */
    public function setMerged(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                $this->addMerged($_file);
            }
        }
        return $this;
    }
    
    /**
     * Get the stack of merged files
     * 
     * @return array The stack of javascript
     */
    public function getMerged()
    {
        return $this->registry->getEntry('javascript_merged_files', false, array());
    }
    
    /**
     * Write merged versions of the files stack in the cache directory
     */
    public function writeMerged($mask = '%s')
    {
        $str='';
        foreach ($this->cleanStack($this->getMerged(), 'path') as $entry) {
            $tag_attrs = array(
                'type'=>'text/javascript',
                'src'=>$entry['path']
            );
            $str .= sprintf($mask, Html::writeHtmlTag('script', null, $tag_attrs));
        }
        return $str;
    }
    
    /**
     * Minify the files if possible and loads them in files_minified stack
     *
     * @return self Must return the object itself for method chaining
     */
    public function minify()
    {
        $cleaned_stack = $this->cleanStack($this->get(), 'path');
        if (!empty($cleaned_stack)) {
            foreach ($cleaned_stack as $i=>$item) {
                if (isset($item['minified']) && true===$item['minified']) {
                    unset($cleaned_stack[$i]);
                }
            }
        }
        if (!empty($cleaned_stack)) {
            $this->addMinified(
                $this->minifyStack($this->extractFromStack($cleaned_stack, 'path'))
            );
        }
        return $this;
    }
    
    /**
     * Add an minified file
     *
     * @param string $file_path The new javascript path
     * @return self 
     * @throw Throws an InvalidArgumentException if the path doesn't exist
     */
    public function addMinified($file_path)
    {
        $stack = $this->_getStackEntry($file_path, null, true);
        $this->registry->addEntry($stack, 'javascript_files');
        $this->registry->addEntry($stack, 'javascript_minified_files');
        return $this;
    }
    
    /**
     * Set a stack of minified files
     *
     * @param array $files An array of javascript files paths
     * @return self 
     * @see self::add()
     */
    public function setMinified(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                $this->addMinified($_file);
            }
        }
        return $this;
    }
    
    /**
     * Get the stack of minified files
     *
     * @return array The stack of javascript
     */
    public function getMinified()
    {
        return $this->registry->getEntry('javascript_minified_files', false, array());
    }
    
    /**
     * Write minified versions of the files stack in the cache directory
     */
    public function writeMinified($mask = '%s')
    {
        $str='';
        foreach ($this->cleanStack($this->getMinified(), 'path') as $entry) {
            $tag_attrs = array(
                'type'=>'text/javascript',
                'src'=>$entry['path']
            );
            $str .= sprintf($mask, Html::writeHtmlTag('script', null, $tag_attrs));
        }
        return $str;
    }
}
