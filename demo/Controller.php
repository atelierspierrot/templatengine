<?php
/**
 */

use TemplateEngine\TemplateEngine;

use Assets\Loader as AssetsLoader;

use Library\Helper\Html as HtmlHelper;
use Library\Helper\Url as UrlHelper;

class Controller
{

    public $loader;
    public $template_engine;

	public function __construct()
	{
        $this->loader = AssetsLoader::getInstance(__DIR__.'/..', 'www', __DIR__);
/*
echo '<pre>';
var_export($loader);
var_export($loader->getAssetsPath());
var_export($loader->getAssetsWebPath());
exit('yo');
*/

        $this->template_engine = TemplateEngine::getInstance();
        $this->template_engine
            ->guessFromAssetsLoader($this->loader)
            ->setLayoutsDir(__DIR__.'/../www/')
            ->setToTemplate('setCachePath', __DIR__.'/tmp' )
            ->setToTemplate('setAssetsCachePath', __DIR__.'/tmp' )
            ->setToView('setIncludePath', __DIR__.'/views' )
            ;
	}

	/**
	 * Distributes the application actions
	 */
	public function distribute()
	{
	    $action = isset($_GET['page']) ? $_GET['page'] : 'index';
		$action_meth = $action.'Action';
		if (method_exists($this, $action_meth)) {
		    $return = $this->{$action_meth}();
		} else {
		    $return = $this->commonAction($action);
		}
        if (!is_array($return)) {
            throw new Exception( 
                sprintf("Action '%s' must return an array!", $action)
            );
        }
        return $this->display($return);
	}

	/**
	 */
	public function display(array $params, $view = null) 
    {
        // request params settings
        $params = array_merge(array(
            'merge_css' => isset($_GET['merge_css']) ? (bool) $_GET['merge_css'] : false,
            'minify_css' => isset($_GET['minify_css']) ? (bool) $_GET['minify_css'] : false,
            'merge_js' => isset($_GET['merge_js']) ? (bool) $_GET['merge_js'] : false,
            'minify_js' => isset($_GET['minify_js']) ? (bool) $_GET['minify_js'] : false,
        ), $params);

        if (!isset($params['content']) && isset($params['output'])) {
            $params['content'] = $params['output'];
        }

        $title = 'Test of the Template Engine';
        if (isset($params['title'])) {
            $title = $params['title'];
        }
        $this->template_engine
            ->templateFallback('getTemplateObject', array('TitleTag'))
    		->add( $title );

        $title_block = array(
            'title'=> isset($params['title']) ? $params['title'] : $title,
            'subheader'=>isset($params['subheader']) ? $params['subheader'] : "A PHP package to build HTML5 views (based on HTML5 Boilerplate layouts).",
            'slogan'=>isset($params['slogan']) ? $params['slogan'] : "<p>These pages show and demonstrate the use and functionality of the <a href=\"http://github.com/atelierspierrot/templatengine\">atelierspierrot/templatengine</a> PHP package you just downloaded.</p>"
        );
        $params['title'] = $title_block;

        if (empty($params['menu'])) {
            $params['menu'] = array(
                'Home'              => UrlHelper::url(array('page'=>'index')),
                'Simple test'       => UrlHelper::url(array('page'=>'hello')),
                'Functions doc'     => UrlHelper::url(array('page'=>'fcts')),
                'Plugins test'      => UrlHelper::url(array('page'=>'test')),
                'Typographic tests' => UrlHelper::url(array('page'=>'loremipsum')),
                'PHP Assets manager' => UrlHelper::url(array('page'=>'assets')),
            );
        }

        $params['footer'] = array('right'=>'A test of footer');

//var_export($params); exit('yo');

        // this will display the layout on screen and exit
		$this->template_engine->renderLayout($view, $params, true, true);
    }

// ------------------------
// Actions
// ------------------------


    function indexAction()
    {
        return array(
            'content'   =>'YO',
            'title'     =>'Home',
            'subheader' => '',
            'slogan' => '',
        );        
    }

    function helloAction()
    {
        return array(
			'output'=> $this->template_engine->render(
				'hello.htm', array(
				    'name'=>isset($_GET['name']) ? $_GET['name'] : 'Anonymous'
				)
			),
			'title' => "Hello",
            'subheader' => '',
            'slogan' => '',
		);
    }

    function fctsAction()
    {
        return array(
			'output'=> $this->template_engine->render(
				'fcts.htm'
			),
			'title' => "Functions"
		);
    }

    function testAction()
    {
        return array(
			'output'=> $this->template_engine->render(
				'test_plugins.htm'
			),
			'title' => "Test of all plugins"
		);
    }

    function loremipsumAction()
    {
        return array(
			'output'=> $this->template_engine->render(
				'loremipsum.htm'
			),
			'title' => "Test of HTML(5) tags"
		);
    }

    function commonAction($action)
    {
        return array(
			'output'=> $this->template_engine->render(
				$action.'.htm'
			),
			'title' => ucfirst(Library\Helper\Text::getHumanReadable($action))
		);
    }

}

// Endfile