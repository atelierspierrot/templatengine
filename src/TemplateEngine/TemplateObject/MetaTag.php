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

namespace TemplateEngine\TemplateObject;

use \TemplateEngine\TemplateObject\Abstracts\AbstractTemplateObject;
use \TemplateEngine\TemplateObject\Abstracts\TemplateObjectInterface;
use \Library\Helper\Html;
use \Library\Helper\ConditionalComment;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class MetaTag
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
        $this->__template->registry->meta_tags = array();
        return $this;
    }

    /**
     * Add a meta tag in meta stack
     *
     * @param string $name The meta tag name
     * @param string|array $content The meta tag content, can be defined as an array which will be joined (for keywords for example)
     * @param bool $http_equiv Is this meta tag written as "http-equiv" (false by default)
     * @param string|null $condition Define a condition (for IE) for this stylesheet
     * @return self
     */
    public function add($name, $content = '', $http_equiv = false, $condition = null)
    {
        $this->__template->registry->addEntry( array(
            'name'=>$name,
            'content'=>$content,
            'http-equiv'=>$http_equiv,
            'condition'=>$condition
        ), 'meta_tags');
        return $this;
    }

    /**
     * Set a full array of tags
     *
     * @param array $tags An array of tags to add
     * @return self
     * @see self::add()
     */
    public function set(array $tags)
    {
        if (!empty($tags)) {
            foreach($tags as $_tag) {
                if (is_array($_tag) && isset($_tag['name']) && isset($_tag['content'])) {
                    $this->add(
                        $_tag['name'],
                        $_tag['content'],
                        isset($_tag['http-equiv']) && true===$_tag['http-equiv'] ? true : false,
                        isset($_tag['condition']) ? $_tag['condition'] : null
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Get the meta tags stack
     *
     * @return array The stack of meta tags definitions
     */
    public function get()
    {
        return $this->__template->registry->getEntry( 'meta_tags', false, array() );
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
        foreach ($this->cleanStack( $this->get(), 'name' ) as $entry) {
            $tag_attrs = array();
            if (true===$entry['http-equiv'])
                $tag_attrs['http-equiv'] = $entry['name'];
            else
                $tag_attrs['name'] = $entry['name'];
            $tag_attrs['content'] = $entry['content'];
            $tag = Html::writeHtmlTag('meta', null, $tag_attrs, true);
            if (isset($entry['condition']) && !empty($entry['condition'])) {
                $tag = ConditionalComment::buildCondition($tag, $entry['condition']);
            }
            $str .= sprintf($mask, $tag);
        }
        return $str;
    }

}

// Endfile