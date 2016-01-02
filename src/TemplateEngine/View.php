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

namespace TemplateEngine;

use \Library\Helper\Directory as DirectoryHelper;
use \Library\Helper\Html as HtmlHelper;
use \Patterns\Abstracts\AbstractView;

/**
 * The global view builder class
 *
 * Construct the views passing them arguments
 *
 * @author  piwi <me@e-piwi.fr>
 */
class View
    extends AbstractView
{

    /**
     * Constructor
     *
     * @param string $view The view filename
     * @param array $params An array of the parameters passed for the view parsing
     * @param bool $display Must the rendering be displayed directly (default is `false`)
     * @param bool $exit Must the system exists after the rendering (default is `false`)
     */
    public function __construct($view = null, $params = null, $display = false, $exit = false)
    {
        if (!empty($view)) {
            $this->render($view, $params, $display, $exit);
        }
    }

    /**
     * Building of a view content including a view file passing it parameters
     *
     * The view file will be included "as-is" so:
     *
     * - it may exists,
     * - it can be either some full HTML, some CSS, some JS containing PHP scripts
     *
     * The parameters will be merged with the object `$default_view_params` and exported
     * in the global context of the view file. For example, if you define a parameter named
     * `param` on a certain value, writing ths following in your view file :
     *
     *     <?php echo $param; ?>
     *
     * will render the value.
     *
     * The best practice is to NOT use the small php tags `<?= ... ?>`.
     *
     * @param string $view The view filename (which must exist)
     * @param array $params An array of the parameters passed for the view parsing
     * @throws \TemplateEngine\TemplateEngineException if the file view can't be found
     * @return string Returns the view file content rendering
     */
    public function render($view, array $params = array())
    {
        $this->setView($view);
        $this->setParams($params);

        $_params = $this->getParams();
        $_view = $this->getTemplate($this->getView());
        $_template = TemplateEngine::getInstance();
        if ($_view && @file_exists($_view)) {
            /*
echo 'Loading view file "'.$_view.'" passing arguments:';
echo '<pre>';
var_dump($_params);
echo '</pre>';
*/
            if (!empty($_params)) {
                extract($_params, EXTR_OVERWRITE);
            }
            ob_start();
            $_template->includePackagesViewsFunctions();
            include $_view;
            $this->setOutput(ob_get_contents());
            ob_end_clean();
        } else {
            throw new TemplateEngineException(
                sprintf('View "%s" can\'t be found (searched in "%s")!', $view, implode(', ', $this->getIncludePath()))
            );
        }

        return $this->getOutput();
    }

// ------------------------------
// Default parameters management
// ------------------------------

    /**
     * The table of the default parameters loaded in each view
     */
    protected $default_view_params = array();

    /**
     * Reset the default parameters for all views to an empty array
     *
     * @return self 
     */
    public function resetDefaultViewParams()
    {
        $this->default_view_params = array();
        return $this;
    }

    /**
     * Set an array of the default parameters for all views
     *
     * @param array $params The array of default parameters
     * @return self
     */
    public function setDefaultViewParams(array $params)
    {
        $this->default_view_params = array_merge($this->default_view_params, $params);
        return $this;
    }

    /**
     * Add an entry of default parameters for all views
     *
     * @param string $name The name of the parameter
     * @param mixed $val The value to set for the parameter
     * @return self 
     */
    public function addDefaultViewParam($name, $val)
    {
        $this->default_view_params[$name] = $val;
        return $this;
    }

    /**
     * Get the default parameters for all views
     *
     * @return array The array of default parameters
     */
    public function getDefaultViewParams()
    {
        return $this->default_view_params;
    }

    /**
     * Get a value of the default parameters for all views
     *
     * @param   string $name The name of the parameter to get
     * @param   mixed $default The default value returns if no parameter is defined for `$name`
     * @return  mixed The parameter value if found, `$default` otherwise
     */
    public function getDefaultViewParam($name, $default = null)
    {
        return isset($this->default_view_params[$name]) ? $this->default_view_params[$name] : $default;
    }

// ------------------------------
// View files management
// ------------------------------

    /**
     * An array of paths to search view files
     */
    protected $include_paths = array();

    /**
     * Define a new include path for view files
     *
     * The new path will be added to the existing paths.
     *
     * @param   string $path The path to add
     * @return  self
     * @throws \TemplateEngine\TemplateEngineException
     */
    public function setIncludePath($path)
    {
        if (file_exists($path)) {
            array_push($this->include_paths, $path);
            return $this;
        } else {
            throw new TemplateEngineException(
                sprintf('Directory "%s" defined as views path can\'t be found!', $path)
            );
        }
    }

    /**
     * Get the view files paths
     *
     * @return array The include paths to search views
     */
    public function getIncludePath()
    {
        return $this->include_paths;
    }

    /**
     * Search a view file in the current file system
     *
     * @parame  string $path The file path to search
     * @return  string The path of the file found
     */
    public function getTemplate($path)
    {
        if (file_exists($path)) {
            return $path;
        } elseif (false!==$path_rp = realpath($path)) {
            return $path_rp;
        } else {
            foreach ($this->getIncludePath() as $_path) {
                $_f = DirectoryHelper::slashDirname($_path).$path;
                if (file_exists($_f)) {
                    return $_f;
                }
            }
        }
        return null;
    }
}
