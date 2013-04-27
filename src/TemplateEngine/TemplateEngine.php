<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

namespace TemplateEngine;

use \ArrayAccess;

use Patterns\Commons\Registry,
    Patterns\Abstracts\AbstractSingleton;

use Library\Helper\Html as HtmlHelper;

use TemplateEngine\Template,
    TemplateEngine\View;

use Assets\Loader as AssetsLoader,
    Assets\Package\Cluster,
    Assets\Package\Preset;

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

    /**
     * Using this flag, no error will be thrown during template rendering
     */
    const NO_ERRORS             = 1;

    /**
     * Using this flag, the template engine can throw errors
     */
    const THROW_TEMPLATE_ERRORS = 2;

    /**
     * Using this flag, the template engine AND the views can throw errors
     */
    const THROW_VIEW_ERRORS     = 4;

    /**
     * Using this flag, all errors catched during rendering will be thrown 
     */
    const THROW_ALL_ERRORS      = 8;

    /**
     * Stack of the default page blocks
     * @static array
     */
    public static $default_page_structure = array(
        0=>'header',
        1=>'title',
        2=>'menu',
        3=>'content',
        4=>'footer',
    );

    /**
     * Path of the default page global layout
     * @static string
     */
    public static $default_page_layout = 'html5boilerplate/layout.html.php';

// ------------------------
// Magic methods
// ------------------------

	/**
	 * Constructor
	 *
	 * @param int $flags The error flag for the template engine, must be one of the
	 *                   class `_ERRORS` constants
	 */
	protected function init($flags = TemplateEngine::NO_ERRORS)
	{
	    $this->setFlags($flags);
        $this->template = new Template;
        $this->view = new View;
        $this->registry = new Registry;
	}

	/**
	 * Object call dispatcher
	 *
	 * This will distribute any call of a non-existing method to the template object or
	 * the view object.
	 *
	 * @param string $name The called method name
	 * @param array $arguments The arguments passed to the method
	 * @return misc
	 * @see TemplateEngine\TemplateEngine::_getFallbackMethod()
	 */
    public function __call($name, array $arguments)
    {
        return (
            method_exists($this, $name) ?
                call_user_func_array(array($this, $name), $arguments)
                :
                $this->_getFallbackMethod($name, $arguments, false)
        );
    }
    
	/**
	 * Object to string
	 *
	 * @return string
	 * @see TemplateEngine\TemplateEngine::renderLayout()
	 */
    public function __toString()
    {
        return $this->renderLayout();
    }
    
// ------------------------
// Getters/Setters
// ------------------------

	/**
	 * Set the object error flags
	 *
	 * @param int $flags The error flag for the template engine, must be one of the
	 *                   class `_ERRORS` constants
	 * @return self Returns `$this` for chainability
	 */
    public function setFlags($flags)
    {
        $this->flags = $flags;
        return $this;
    }

	/**
	 * Get the object error flags
	 *
	 * @return int
	 */
    public function getFlags()
    {
        return $this->flags;
    }

	/**
	 * Set the page structure
	 *
	 * This will store the page structure in the registry and create a DOM unique ID for
	 * each one.
	 *
	 * @param array $structure The page blocks to set
	 * @return self Returns `$this` for chainability
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
	 * Get the page structure
	 *
	 * @return array
	 */
	public function getPageStructure()
	{
        return $this->registry->getEntry('page_structure', array());
	}

	/**
	 * Set a layouts directory
	 *
	 * @param string $path The path to add in the template include path
	 * @return self Returns `$this` for chainability
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
	 * Set to Template object
	 *
	 * @param string $var The variable to set
	 * @param misc $val The value of the variable to set
	 * @return self Returns `$this` for chainability
	 * @see TemplateEngine\TemplateEngine::templateFallback()
	 */
	public function setToTemplate($var, $val)
	{
	    $args = func_get_args();
	    array_shift($args);
	    $this->templateFallback($var, $args, 'set');
	    return $this;
	}

	/**
	 * Get from Template object
	 *
	 * @param string $var The variable to get
	 * @return misc
	 * @see TemplateEngine\TemplateEngine::templateFallback()
	 */
	public function getFromTemplate($var)
	{
	    $args = func_get_args();
	    array_shift($args);
	    return $this->templateFallback($var, $args, 'get');
	}

	/**
	 * Fallback system to call a Template object's method
	 *
	 * @param string $name The name of the method to call
	 * @param array $args The arguments to pass to the method
	 * @param string $fallback A fallback prefix used as the method scope
	 * @return misc
	 * @see TemplateEngine\TemplateEngine::_runFallbackMethod()
	 */
    public function templateFallback($name, array $args = array(), $fallback = null)
    {
        return $this->_runFallbackMethod(
            $this->template, $name, $args, ($this->getFlags() & TemplateEngine::THROW_TEMPLATE_ERRORS), $fallback
        );
    }

	/**
	 * Set to View object
	 *
	 * @param string $var The variable to set
	 * @param misc $val The value of the variable to set
	 * @return self Returns `$this` for chainability
	 * @see TemplateEngine\TemplateEngine::viewFallback()
	 */
	public function setToView($var, $val)
	{
	    $args = func_get_args();
	    array_shift($args);
	    $this->viewFallback($var, $args, 'set');
	    return $this;
	}

	/**
	 * Get from Template object
	 *
	 * @param string $var The variable to get
	 * @return misc
	 * @see TemplateEngine\TemplateEngine::viewFallback()
	 */
	public function getFromView($var)
	{
	    $args = func_get_args();
	    array_shift($args);
	    return $this->viewFallback($var, $args, 'get');
	}

	/**
	 * Fallback system to call a View object's method
	 *
	 * @param string $name The name of the method to call
	 * @param array $args The arguments to pass to the method
	 * @param string $fallback A fallback prefix used as the method scope
	 * @return misc
	 * @see TemplateEngine\TemplateEngine::_runFallbackMethod()
	 */
    public function viewFallback($name, array $args = array(), $fallback = null)
    {
        return $this->_runFallbackMethod(
            $this->view, $name, $args, ($this->getFlags() & TemplateEngine::THROW_VIEW_ERRORS), $fallback
        );
    }

	/**
	 * Automatic assets loading from an Assets package declare in a `composer.json`
	 *
	 * @param string $package_name The name of the package to use
	 * @return void
	 */
	public function useAssetsPackage($package_name = null)
	{
	    $preset = new Preset($package_name, $this->assets_loader, $this);
	    $preset->load();
	}

	/**
	 * Automatic loading of assets views functions
	 *
	 * @return void
	 */
	public function includePackagesViewsFunctions()
	{
	    $_cluster = Cluster::newClusterFromAssetsLoader($this->assets_loader);
        foreach ($this->assets_loader->getAssetsDb() as $package=>$config) {
            if (!empty($config['views_functions'])) {
                $cluster = clone $_cluster;
                $cluster->loadClusterFromArray($config);
                foreach ($cluster->getViewsFunctionsPaths() as $fcts) {
                    $fct_file = $cluster->getFullPath($fcts);
                    if (@file_exists($fct_file)) {
                        @include_once $fct_file;
                    }
                }
            }
        }
	}

// ------------------------
// Views rendering
// ------------------------

	/**
	 * Required settings before rendering
	 *
	 * @return void
	 */
    protected function _prepareRendering()
    {
		$this->view->addDefaultViewParam('_template', $this);

		$structure = $this->getPageStructure();
		if (empty($structure)) {
		    $this->setPageStructure(self::$default_page_structure);
		}
		
    }

	/**
	 * Rendering of a layout
	 *
	 * @param string $view The view file name
	 * @param array $params The parameters to pass to the view
	 * @param bool $display Set to `true` to echo the result
	 * @param bool $exit Set to `true` to exit after display
	 * @return string The result of the rendering
	 * @see TemplateEngine\TemplateEngine::render()
	 */
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

    /**
     * Process a fallback method on Template and View and returns the result
     *
     * @param string $varname The variable name to search
     * @param array $arguments The arguments to pass to the method
	 * @param bool $throw_errors Throw errors during process ?
	 * @param string $fallback A fallback prefix used as the method scope
     * @return misc Returns the result of the processed method if so
     */
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
    
    /**
     * Get a variable name in CamelCase
     *
     * @param string $name The variable name to search
     * @return string
     */
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

    /**
     * Process a fallback method on an object
     *
     * @param string $varname The variable name to search
     * @param array $arguments The arguments to pass to the method
	 * @param bool $throw_errors Throw errors during process ?
	 * @param string $fallback A fallback prefix used as the method scope
     * @return misc Returns the result of the processed method if so
     * @throws Throws a `TemplateEngineException` if `$throw_errors` is `true` and the method was not found
     */
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

    /**
     * Check existence of a variable by array access to the TemplateEngine
     *
     * @param string $offset The variable trying to be get
     * @return bool
     */
    public function offsetExists($offset)
    {
        $val = $this->registry->getEntry($offset, null);
        if (empty($val)) {
            $val = $this->_getFallbackMethod($offset, array(), false, 'get');
        }
        return (bool) null!==$val;
    }
    
    /**
     * Get a variable by array access to the TemplateEngine
     *
     * @param string $offset The variable trying to be get
     * @return bool
     */
    public function offsetGet($offset)
    {
        $val = $this->registry->getEntry($offset, null);
        if (empty($val)) {
            $val = $this->_getFallbackMethod($offset, array(), false, 'get');
        }
        return $val;
    }
    
    /**
     * Set a variable value by array access to the TemplateEngine
     *
     * @param string $offset The variable trying to be set
     * @param misc $value The variable value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->registry->setEntry($offset, $value);
    }
    
    /**
     * Unset a variable value by array access to the TemplateEngine
     *
     * @param string $offset The variable to be unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->registry->setEntry($offset, null);
    }

// ------------------------
// Assets loader
// ------------------------

	/**
	 * Set the object Assets Loader instance
	 *
	 * @param object $loader The instance of Assets\AssetsLoader
	 * @return self Returns `$this` for chainability
	 */
    public function setAssetsLoader(AssetsLoader $loader)
    {
        $this->assets_loader = $loader;
        $assets_db = $this->assets_loader->getAssets();
        if (!empty($assets_db)) {
            $_cluster = Cluster::newClusterFromAssetsLoader($this->assets_loader);
            foreach ($assets_db as $package=>$config) {
                if (!empty($config['views_path'])) {
                    $cluster = clone $_cluster;
                    $cluster->loadClusterFromArray($config);
                    foreach ($cluster->getViewsPaths() as $path) {
                        $full_path = $cluster->getFullPath($path);
                        if (@file_exists($full_path)) {
                            $this->setToView('setIncludePath', $full_path);
                        }
                    }
                }
            }
        }
        return $this;
    }

	/**
	 * Get the object Assets Loader instance
	 *
	 * @return object The instance of Assets\AssetsLoader
	 */
    public function getAssetsLoader()
    {
        return $this->assets_loader;
    }

	/**
	 * Set the object Assets Loader instance and dispatch required template envionement
	 *
	 * @param object $loader The instance of Assets\AssetsLoader
	 * @return self Returns `$this` for chainability
	 */
    public function guessFromAssetsLoader(AssetsLoader $loader)
    {
        $this->setAssetsLoader($loader);
        $this
            ->setLayoutsDir( $this->assets_loader->getAssetsRealPath() )
            ->setToTemplate('setWebRootPath', $this->assets_loader->getAssetsWebPath() )
            ->setToView('addDefaultViewParam', 'assets', $this->assets_loader->getAssetsWebPath() )
            ->setToTemplate('setWebRootPath', $this->assets_loader->getDocumentRoot() )
            ;
        return $this;
    }

// ------------------------
// Special view methods
// ------------------------

	/**
	 * Write a simple error during view rendering
	 *
	 * This will trigger a `E_USER_WARNING`.
	 *
	 * @param string $message The error message
	 * @param string $file The file throwing the error
	 * @param int $line The line of the file throwing the error
	 * @return void
	 */
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

	/**
	 * Try to execute a closure sending an error if necessary
	 *
	 * @param callable $value The closure to call
	 * @param array $arguments The arguments to pass to the closure
	 * @return misc The result of the execution if so
	 */
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

	/**
	 * Get safely a string from any kind of variable
	 *
	 * @param misc $value The value to build the string
	 * @param string $glue The glue used for array `join()` if so
	 * @return string
	 */
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