<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013-2014 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <http://github.com/atelierspierrot/templatengine>
 */

namespace Assets\AssetObject;

use \AssetsManager\AssetObject\AbstractAssetObject;
use \AssetsManager\AssetObject\AssetObjectInterface;
use \Library\Helper\Html;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class LinkTag
    extends AbstractAssetObject
    implements AssetObjectInterface
{

// ------------------------
// AssetObjectInterface
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
        $this->__registry->link_tags = array();
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
            $this->__registry->addEntry( $tag_attributes, 'link_tags');
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
        return $this->__registry->getEntry( 'link_tags', false, array() );
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
//        foreach($this->_cleanStack( $this->get(), 'rel' ) as $entry) {
        foreach($this->get() as $entry) {
            $str .= sprintf($mask, Html::writeHtmlTag( 'link', null, $entry, true ));
        }
        return $str;
    }

}

// Endfile