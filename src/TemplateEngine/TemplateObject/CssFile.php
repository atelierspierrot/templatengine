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

use \TemplateEngine\TemplateObject\Abstracts\AbstractFileTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\FileTemplateObjectInterface;
use \Library\Helper\Html;
use \Library\Helper\ConditionalComment;

/**
 * @author  piwi <me@e-piwi.fr>
 */
class CssFile
    extends AbstractFileTemplateObject
    implements FileTemplateObjectInterface
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
        $this->registry->css_files = array();
        $this->registry->css_minified_files = array();
        return $this;
    }
    
    /**
     * Add a CSS file in CSS stack if it exists
     *
     * @param string $file_path The new CSS path
     * @param string $media The media type for the CSS file (default is "screen")
     * @param string|null $condition Define a condition (for IE) for this stylesheet
     * @return self
     */
    public function addIfExists($file_path, $media = 'screen', $condition = null)
    {
        $_fp = $this->__template->findAsset($file_path);
        if ($_fp || \AssetsManager\Loader::isUrl($file_path)) {
            return $this->add($file_path, $media, $condition);
        }
        return $this;
    }
    
    /**
     * Add a CSS file in CSS stack
     *
     * @param string $file_path The new CSS path
     * @param string $media The media type for the CSS file (default is "screen")
     * @param string|null $condition Define a condition (for IE) for this stylesheet
     * @return self
     * @throws \InvalidArgumentException if the path doesn't exist
     */
    public function add($file_path, $media = 'screen', $condition = null)
    {
        $_fp = $this->__template->findAsset($file_path);
        if ($_fp || \AssetsManager\Loader::isUrl($file_path)) {
            $this->registry->addEntry(array(
                'file'=>$_fp, 'media'=>$media, 'condition'=>$condition
            ), 'css_files');
        } else {
            throw new \InvalidArgumentException(
                sprintf('CSS file "%s" not found!', $file_path)
            );
        }
        return $this;
    }
    
    /**
     * Set a full CSS stack
     *
     * @param array $files An array of CSS files paths
     * @return self
     * @see self::add()
     */
    public function set(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                if (is_array($_file) && isset($_file['file'])) {
                    $this->add(
                        $_file['file'],
                        isset($_file['media']) ? $_file['media'] : '',
                        isset($_file['condition']) ? $_file['condition'] : null
                    );
                } elseif (is_string($_file)) {
                    $this->add($_file);
                }
            }
        }
        return $this;
    }
    
    /**
     * Get the CSS files stack
     *
     * @return array The stack of CSS
     */
    public function get()
    {
        return $this->registry->getEntry('css_files', false, array());
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
        foreach ($this->cleanStack($this->get(), 'file') as $entry) {
            $tag_attrs = array(
                'rel'=>'stylesheet',
                'type'=>'text/css',
                'href'=>$entry['file']
            );
            if (isset($entry['media']) && !empty($entry['media']) && $entry['media']!='screen') {
                $tag_attrs['media'] = $entry['media'];
            }
            $tag = Html::writeHtmlTag('link', null, $tag_attrs, true);
            if (isset($entry['condition']) && !empty($entry['condition'])) {
                $tag = ConditionalComment::buildCondition($tag, $entry['condition']);
            }
            $str .= sprintf($mask, $tag);
        }
        return $str;
    }

// ------------------------
// FileTemplateObjectInterface
// ------------------------

    /**
     * Merge the files if possible and loads them in files_merged stack
     *
     * @return self Must return the object itself for method chaining
     */
    public function merge()
    {
        $css_files = $this->cleanStack($this->get(), 'file');

        $organized_css = array( 'rest'=>array() );
        foreach ($css_files as $_file) {
            if (!empty($_file['media'])) {
                if (!isset($organized_css[ $_file['media'] ])) {
                    $organized_css[ $_file['media'] ] = array();
                }
                $organized_css[ $_file['media'] ][] = $_file;
            } else {
                $organized_css['rest'][] = $_file;
            }
        }

        foreach ($organized_css as $media=>$stack) {
            $cleaned_stack = $this->extractFromStack($stack, 'file');
            if (!empty($cleaned_stack)) {
                $this->addMerged(
                    $this->mergeStack($cleaned_stack), $media=='rest' ? 'screen' : $media
                );
            }
        }

        return $this;
    }

    /**
     * Add an merged file
     *
     * @param string $file_path The new CSS path
     * @param string $media The media type for the CSS file (default is "screen")
     * @return self
     * @throws \InvalidArgumentException if the path doesn't exist
     */
    public function addMerged($file_path, $media = 'screen')
    {
        $_fp = $this->__template->findAsset($file_path);
        if ($_fp || \AssetsManager\Loader::isUrl($file_path)) {
            $this->registry->addEntry(array(
                'file'=>$_fp, 'media'=>$media
            ), 'css_merged_files');
        } else {
            throw new \InvalidArgumentException(
                sprintf('CSS merged file "%s" not found!', $file_path)
            );
        }
        return $this;
    }

    /**
     * Set a stack of merged files
     *
     * @param array $files An array of CSS files paths
     * @return self
     * @see self::add()
     */
    public function setMerged(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                if (is_array($_file) && isset($_file['file'])) {
                    if (isset($_file['media'])) {
                        $this->add($_file['file'], $_file['media']);
                    } else {
                        $this->add($_file['file']);
                    }
                } elseif (is_string($_file)) {
                    $this->add($_file);
                }
            }
        }
        return $this;
    }

    /**
     * Get the stack of merged files
     *
     * @return array The stack of CSS
     */
    public function getMerged()
    {
        return $this->registry->getEntry('css_merged_files', false, array());
    }

    /**
     * Write merged versions of the files stack in the cache directory
     *
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string The string to display fot this template object
     */
    public function writeMerged($mask = '%s')
    {
        $str='';
        foreach ($this->cleanStack($this->getMerged(), 'file') as $entry) {
            $tag_attrs = array(
                'rel'=>'stylesheet',
                'type'=>'text/css',
                'href'=>$entry['file']
            );
            if (isset($entry['media']) && !empty($entry['media']) && $entry['media']!='screen') {
                $tag_attrs['media'] = $entry['media'];
            }
            $str .= sprintf($mask, Html::writeHtmlTag('link', null, $tag_attrs, true));
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
        $css_files = $this->cleanStack($this->get(), 'file');

        $organized_css = array('rest'=>array());
        foreach ($css_files as $_file) {
            if (!empty($_file['media'])) {
                if (!isset($organized_css[ $_file['media'] ])) {
                    $organized_css[ $_file['media'] ] = array();
                }
                $organized_css[ $_file['media'] ][] = $_file;
            } else {
                $organized_css['rest'][] = $_file;
            }
        }

        foreach ($organized_css as $media=>$stack) {
            $cleaned_stack = $this->extractFromStack($stack, 'file');
            if (!empty($cleaned_stack)) {
                $this->addMinified(
                    $this->minifyStack($cleaned_stack), $media=='rest' ? 'screen' : $media
                );
            }
        }

        return $this;
    }

    /**
     * Add an minified file
     *
     * @param string $file_path The new CSS path
     * @param string $media The media type for the CSS file (default is "screen")
     * @return self
     * @throws \InvalidArgumentException if the path doesn't exist
     */
    public function addMinified($file_path, $media = 'screen')
    {
        $_fp = $this->__template->findAsset($file_path);
        if ($_fp || \AssetsManager\Loader::isUrl($file_path)) {
            $this->registry->addEntry(array(
                'file'=>$_fp, 'media'=>$media
            ), 'css_minified_files');
        } else {
            throw new \InvalidArgumentException(
                sprintf('CSS minified file "%s" not found!', $file_path)
            );
        }
        return $this;
    }

    /**
     * Set a stack of minified files
     *
     * @param array $files An array of CSS files paths
     * @return self
     * @see self::add()
     */
    public function setMinified(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $_file) {
                if (is_array($_file) && isset($_file['file'])) {
                    if (isset($_file['media'])) {
                        $this->add($_file['file'], $_file['media']);
                    } else {
                        $this->add($_file['file']);
                    }
                } elseif (is_string($_file)) {
                    $this->add($_file);
                }
            }
        }
        return $this;
    }

    /**
     * Get the stack of minified files
     *
     * @return array The stack of CSS
     */
    public function getMinified()
    {
        return $this->registry->getEntry('css_minified_files', false, array());
    }

    /**
     * Write minified versions of the files stack in the cache directory
     *
     * @param string $mask A mask to write each line via "sprintf()"
     * @return string The string to display fot this template object
     */
    public function writeMinified($mask = '%s')
    {
        $str='';
        foreach ($this->cleanStack($this->getMinified(), 'file') as $entry) {
            $tag_attrs = array(
                'rel'=>'stylesheet',
                'type'=>'text/css',
                'href'=>$entry['file']
            );
            if (isset($entry['media']) && !empty($entry['media']) && $entry['media']!='screen') {
                $tag_attrs['media'] = $entry['media'];
            }
            $str .= sprintf($mask, Html::writeHtmlTag('link', null, $tag_attrs, true));
        }
        return $str;
    }
}
