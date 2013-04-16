<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine;

use Patterns\Commons\Registry,
    Patterns\Abstracts\AbstractSingleton;

use Library\Helper\Html as HtmlHelper;

use TemplateEngine\Template,
    TemplateEngine\View;

use Assets\Loader as AssetsLoader;

use \ArrayAccess;

/**
 * General template builder
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class TemplateEngine
    extends AbstractSingleton
    implements ArrayAccess
{

    /**
     * @var TemplateEngine\Template
     */
    protected $template;

    /**
     * @var TemplateEngine\View
     */
    protected $view;

    /**
     * @var Patterns\Commons\Registry
     */
    protected $registry;

    /**
     * @var Assets\Loader
     */
    protected $assets_loader;

    /**
     * @var int
     */
    protected $flags;

    const NO_ERRORS             = 1;
    const THROW_TEMPLATE_ERRORS = 2;
    const THROW_VIEW_ERRORS     = 4;
    const THROW_ALL_ERRORS      = 8;

    public static $default_page_structure = array(
        0=>'header',
        1=>'title',
        2=>'menu',
        3=>'content',
        4=>'footer',
    );

    public static $default_page_layout = 'html5boilerplate/layout.html.php';

// ------------------------
// Magic methods
// ------------------------

	/**
	 * Constructor
	 */
	protected function init($flags = TemplateEngine::NO_ERRORS)
	{
	    $this->setFlags($flags);
        $this->template = new Template;
        $this->view = new View;
        $this->registry = new Registry;
	}

    public function __call($name, array $arguments)
    {
        return (
            method_exists($this, $name) ?
                call_user_func_array(array($this, $name), $arguments)
                :
                $this->_getFallbackMethod($name, $arguments, false)
        );
    }
    
    public function __toString()
    {
        return $this->renderLayout();
    }
    
// ------------------------
// Getters/Setters
// ------------------------

    public function setFlags($flags)
    {
        $this->flags = $flags;
        return $this;
    }

    public function getFlags()
    {
        return $this->flags;
    }

	/**
	 */
	public function setPageStructure(array $structure)
	{
		foreach($structure as $_ref) {
			HtmlHelper::getNewId($_ref, true);
		}
        $this->registry->setEntry('page_structure', $structure);
        return $this;
	}

	/**
	 */
	public function getPageStructure()
	{
        return $this->registry->getEntry('page_structure', array());
	}

	/**
	 */
	public function setLayoutsDir($path)
	{
        $this->view->setIncludePath($path);
        return $this;
	}

// ------------------------
// Template and View objects Getters/Setters
// ------------------------

	/**
	 */
	public function setToTemplate($var, $val)
	{
	    $args = func_get_args();
	    array_shift($args);
	    $this->templateFallback($var, $args, 'set');
	    return $this;
	}

	/**
	 */
	public function getFromTemplate($var)
	{
	    $args = func_get_args();
	    array_shift($args);
	    return $this->templateFallback($var, $args, 'get');
	}

    public function templateFallback($name, array $args = array(), $fallback = null)
    {
        return $this->_runFallbackMethod(
            $this->template, $name, $args, ($this->getFlags() & TemplateEngine::THROW_TEMPLATE_ERRORS), $fallback
        );
    }

	/**
	 */
	public function setToView($var, $val)
	{
	    $args = func_get_args();
	    array_shift($args);
	    $this->viewFallback($var, $args, 'set');
	    return $this;
	}

	/**
	 */
	public function getFromView($var)
	{
	    $args = func_get_args();
	    array_shift($args);
	    return $this->viewFallback($var, $args, 'get');
	}

    public function viewFallback($name, array $args = array(), $fallback = null)
    {
        return $this->_runFallbackMethod(
            $this->view, $name, $args, ($this->getFlags() & TemplateEngine::THROW_VIEW_ERRORS), $fallback
        );
    }

// ------------------------
// Views rendering
// ------------------------

    protected function _prepareRendering()
    {
		$this->view->addDefaultViewParam('_template', $this);

		$structure = $this->getPageStructure();
		if (empty($structure)) {
		    $this->setPageStructure(self::$default_page_structure);
		}
		
    }

    public function renderLayout($view = null, array $params = array(), $display = false, $exit = false)
    {
        if (is_null($view)) $view = self::$default_page_layout;
        return $this->render($view, $params, $display, $exit);
    }

    /**
     * Building of a view content including a view file passing it parameters
     *
     * @param string $view The view filename (which must exist)
     * @param array $params An array of the parameters passed for the view parsing
	 * @param bool $display Must the rendering be displayed directly (default is `false`)
	 * @param bool $exit Must the system exists after the rendering (default is `false`)
     * @return string Returns the view file content rendering
     * @see TemplateEngine\View::render()
     */
    public function render($view, array $params = array(), $display = false, $exit = false)
    {
        $this->_prepareRendering();
        $this->view->render($view, $params);
        if ($display) {
            echo $this->view->getOutput();
            if ($exit) exit(0);
        } else {
            return $this->view->getOutput();
        }
    }

    /**
     * Building of a view content including a view file passing it parameters and display it on screen
     *
     * @param string $view The view filename (which must exist)
     * @param array $params An array of the parameters passed for the view parsing
	 * @param bool $exit Must the system exists after the rendering (default is `false`)
     * @return string Returns the view file content rendering
     * @see TemplateEngine\View::render()
     */
    public function display($view, array $params = array(), $exit = false)
    {
        $this->_prepareRendering();
        $this->view->render($view, $params);
        echo $this->view->getOutput();
        if ($exit) exit(0);
    }


// ------------------------
// Fallback system
// ------------------------

    protected function _getFallbackMethod(
        $varname, array $arguments = array(), $throw_errors = false, $fallback = null
    ) {
        $var = $this->_getFallbackVarname($varname);

        $template_val = $this->_runFallbackMethod($this->template, $var, $arguments, $throw_errors, $fallback);
        if (!empty($template_val)) {
            return $template_val;
        }

        $view_val = $this->_runFallbackMethod($this->view, $var, $arguments, $throw_errors, $fallback);
        if (!empty($view_val)) {
            return $view_val;
        }

        return null;
    }
    
    protected function _getFallbackVarname($name)
    {
        if (false===strpos($name, '_')) return $name;
        $parts = explode('_', $name);
        $str = '';
        foreach($parts as $_part) {
            $str .= strlen($str) ? ucfirst($_part) : $_part;
        }
        return $str;
    }

    protected function _runFallbackMethod(
        $object, $varname, array $arguments = array(), $throw_errors = false, $fallback = null
    ) {
	    if (property_exists($object, $varname)) {
	        try {
	            $object->{$varname} = $args;
                return $object;
	        } catch(\Exception $e) {}
	    }

	    if (method_exists($object, $varname)) {
	        try {
                $val = call_user_func_array(array($object, $varname), $arguments);
                return $val;
	        } catch(\Exception $e) {}
        }

        if (!empty($fallback)) {
            $_meth = $fallback.ucfirst($varname);
            if (method_exists($object, $_meth)) {
                try {
                    $val = call_user_func_array(array($object, $_meth), $arguments);
                    return $val;
                } catch(\Exception $e) {}
            }
        }
	    
        if ($throw_errors) {
            if (!empty($_meth)) {
                $msg = sprintf('Neither variable "%s" nor method "%s" or "%s" were found in object "%s"!',
                    $varname, $varname, $_meth, get_class($object));
            } else {
                $msg = sprintf('Neither variable "%s" nor method "%s" were found in object "%s"!',
                    $varname, $varname, get_class($object));
            }
            throw new TemplateEngineException($msg);
        }
        return null;
    }

// ------------------------
// ArrayAccess interface
// ------------------------

    public function offsetExists($offset)
    {
        $val = $this->registry->getEntry($offset, null);
        if (empty($val)) {
            $val = $this->_getFallbackMethod($offset, array(), false, 'get');
        }
        return (bool) null!==$val;
    }
    
    public function offsetGet($offset)
    {
        $val = $this->registry->getEntry($offset, null);
        if (empty($val)) {
            $val = $this->_getFallbackMethod($offset, array(), false, 'get');
        }
        return $val;
    }
    
    public function offsetSet($offset, $value)
    {
        $this->registry->setEntry($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        $this->registry->setEntry($offset, null);
    }

// ------------------------
// Assets loader
// ------------------------

    public function setAssetsLoader(AssetsLoader $loader)
    {
        $this->assets_loader = $loader;
        $assets_db = $this->assets_loader->getAssets();
        if (!empty($assets_db)) {
            foreach($assets_db as $package=>$infos) {
                if (isset($infos['views'])) {
                    $this->setToView('setIncludePath', $infos['views']);
                }
            }
        }
        return $this;
    }

    public function getAssetsLoader()
    {
        return $this->assets_loader;
    }

    public function guessFromAssetsLoader(AssetsLoader $loader)
    {
        $this->setAssetsLoader($loader);
        $this
            ->setLayoutsDir( $this->assets_loader->getAssetsPath() )
            ->setToTemplate('setWebRootPath', $this->assets_loader->getAssetsWebPath() )
            ->setToView('addDefaultViewParam', 'assets', $this->assets_loader->getAssetsWebPath() )
            ->setToTemplate('setWebRootPath', $this->assets_loader->getDocumentRoot() )
            ;
        return $this;
    }

// ------------------------
// Special view methods
// ------------------------

    public static function __error($message = 'View error', $file = null, $line = null)
    {
        if (!is_null($file)) {
            $message .= sprintf(' in file "%s"', $file);
            if (!is_null($line)) {
                $message .= sprintf(' at line %s', $line);
            }
        }
        trigger_error($message, E_USER_WARNING);
    }

    public static function __closurable($value, array $arguments = array())
    {
        if (is_callable($value)) {
            try {
                $val = call_user_func_array($value, $arguments);
                return $val;
            } catch(Exception $e) {
                self::__error(sprintf('Closure error: "%s"!', $e->getMessage()), $e->getFile(), $e->getLine());
            }
        } else {
            return $value;
        }
    }

    public static function __string($value, $glue = ', ')
    {
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return $value->__toString();
            }
            if ($value instanceof ArrayAccess) {
                return implode($glue, $value);
            }
            return get_class($value);
        } elseif(is_array($value)) {
            return implode($glue, $value);
        } elseif(is_bool($value)) {
            return (true===$value ? 'true' : 'false');
        } elseif(is_callable($value)) {
            return self::__closurable($value);
        }
        return (string) $value;
    }

}

// Endfile