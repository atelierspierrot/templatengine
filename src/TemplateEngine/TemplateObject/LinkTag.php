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

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class LinkTag
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
        $this->__template->registry->link_tags = array();
        return $this;
    }

    /**
     * Add a link header attribute
     *
     * @param array $tag_attributes The link tag attributes
     * @return self
     */
    public function add($tag_attributes)
    {
        if (!empty($tag_attributes)) {
            $this->__template->registry->addEntry( $tag_attributes, 'link_tags');
        }
        return $this;
    }

    /**
     * Set a full links header stack
     *
     * @param array $tags An array of tags definitions
     * @return self
     * @see self::add()
     */
    public function set(array $tags)
    {
        if (!empty($tags)) {
            foreach($tags as $_tag) {
                $this->add( $_tag );
            }
        }
        return $this;
    }

    /**
     * Get the header link tags stack
     *
     * @return array The stack of header link tags
     */
    public function get()
    {
        return $this->__template->registry->getEntry( 'link_tags', false, array() );
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
// allow multi same links
//        foreach($this->cleanStack( $this->get(), 'rel' ) as $entry) {
        foreach($this->get() as $entry) {
            $str .= sprintf($mask, Html::writeHtmlTag( 'link', null, $entry, true ));
        }
        return $str;
    }

}

// Endfile