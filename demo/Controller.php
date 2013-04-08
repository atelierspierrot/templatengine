<?php
/**
 */

use TemplateEngine\TemplateEngine;

use Library\Helper\Html as HtmlHelper;

class Controller
{

    public $template_engine;

	public function __construct()
	{
        $this->template_engine = TemplateEngine::getInstance();
        $this->template_engine
            ->setLayoutsDir(__DIR__.'/../www/')
            ->setToTemplate('setWebRootPath', __DIR__ )
            ->setToTemplate('setCachePath', __DIR__.'/tmp' )
            ->setToTemplate('setAssetsCachePath', __DIR__.'/tmp' )
            ->setToView('setIncludePath', __DIR__.'/views' )
            ->setToView('addDefaultViewParam', 'boilerplate_assets', '../www/html5boilerplate/' )
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
		    if (!is_array($return)) {
                throw new Exception( 
                    sprintf("Action '%s' must return an array!", $action)
                );
		    }
		    return $this->display($return);
		} else {
			throw new Exception( 
				sprintf("Action '%s' can't be found!", $action)
			);
		}
	}

	/**
	 */
	public function display(array $params, $view = null) 
    {
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
            'title'     =>'Home'
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
			'title' => "Hello"
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

}

// Endfile