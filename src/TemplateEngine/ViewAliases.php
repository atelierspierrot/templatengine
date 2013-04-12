<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

use TemplateEngine\TemplateEngine;
use Library\Helper\Directory as DirectoryHelper;
use Library\Helper\File as FileHelper;
use Library\Helper\Html as HtmlHelper;
use Library\Helper\Text as TextHelper;
use Library\Helper\Url as UrlHelper;

if (!function_exists('_array')) 
{
	function _array($array)
	{
	    return (is_array($array) ? $array : array($array));
	}	
}

if (!function_exists('_attribute')) 
{
	function _attribute($var, $val)
	{
	    echo HtmlHelper::parseAttributes(array($var => $val));
	}	
}

if (!function_exists('_bit')) 
{
	function _bit($value)
	{
	    echo (true==$value ? 'true' : 'false');
	}	
}

if (!function_exists('_cut')) 
{
	function _cut($str, $length = 120, $end_str = ' ...')
	{
	    echo TextHelper::cut($str, $length, $end_str);
	}	
}

if (!function_exists('_default')) 
{
	function _default($val, $default = '')
	{
	    echo (isset($val) ? $val : $default);
	}	
}

if (!function_exists('_dump')) 
{
	function _dump($array, $only_string_index = false, $mask_item = '<li>%s: %s</li>', $mask_global = '<ul>%s</ul>', $no_index_mask = '<li>%s</li>')
	{
	    $array = _array($array);
	    $list = '';
	    foreach($array as $key=>$value) {
	        if ($only_string_index && !is_string($key)) {
    	        $list .= sprintf($no_index_mask, TemplateEngine::__string($value));
	        } else {
    	        $list .= sprintf($mask_item, $key, TemplateEngine::__string($value));
    	    }
	    }
	    echo sprintf($mask_global, $list);
	}	
}

if (!function_exists('_else')) 
{
	function _else($condition, $if_false = '', $if_true = '', $args)
	{
	    _if($condition, $if_true, $if_false, _array($args));
	}	
}

if (!function_exists('_error')) 
{
	function _error($message = 'View error', $file = null, $line = null)
	{
	    TemplateEngine::__error($message, $file, $line);
	}	
}

if (!function_exists('_escape')) 
{
	function _escape($str)
	{
	    echo htmlentities(_string($str));
	}	
}

if (!function_exists('_filename')) 
{
	function _filename($filename, $lowercase = false, $delimiter = '-')
	{
	    echo FileHelper::formatFilename($filename, $lowercase, $delimiter);
	}	
}

if (!function_exists('_getid')) 
{
	function _getid($reference = null, $base_id = null)
	{
	    echo HtmlHelper::getId($reference, $base_id);
	}	
}

if (!function_exists('_if')) 
{
	function _if($condition, $if_true = '', $if_false = '', $args)
	{
	    array_unshift($args, $condition);
	    echo TemplateEngine::__closurable(
	        (true===$condition ? $if_true : $if_false), _array($args)
	    );
	}	
}

if (!function_exists('_isfalse')) 
{
	function _isfalse($val)
	{
	    return (bool) false==$val;
	}	
}

if (!function_exists('_isnotfalse')) 
{
	function _isnotfalse($val)
	{
	    return (bool) false!=$val;
	}	
}

if (!function_exists('_isnottrue')) 
{
	function _isnottrue($val)
	{
	    return (bool) true!=$val;
	}	
}

if (!function_exists('_isnotnull')) 
{
	function _isnotnull($val)
	{
	    return (bool) null!=$val;
	}	
}

if (!function_exists('_isnull')) 
{
	function _isnull($val)
	{
	    return (bool) null==$val;
	}	
}

if (!function_exists('_istrue')) 
{
	function _istrue($val)
	{
	    return (bool) true==$val;
	}	
}

if (!function_exists('_javascript')) 
{
	function _javascript($str, $protect_quotes = false)
	{
	    echo HtmlHelper::javascriptProtect($str, $protect_quotes);
	}	
}

if (!function_exists('_keys')) 
{
	function _keys($array)
	{
	    return array_keys(_array($array));
	}	
}

if (!function_exists('_loremipsum')) 
{
	function _loremipsum($length = 'short')
	{
	    if ('short'===$length) {
    	    echo "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    	} else {
    	    echo "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.";
    	}
	}	
}

if (!function_exists('_lower')) 
{
	function _lower($value)
	{
	    echo strtolower(TemplateEngine::__string($value));
	}	
}

if (!function_exists('_newid')) 
{
	function _newid($reference = null, $base_id = null)
	{
	    echo HtmlHelper::getNewId($reference, $base_id);
	}	
}

if (!function_exists('_onoff')) 
{
	function _onoff($value)
	{
	    echo (true==$value ? 'on' : 'off');
	}	
}

if (!function_exists('_render')) 
{
	function _render($view, array $args = array())
	{
	    $_this = TemplateEngine::getInstance();
	    $known_view = $_this->getTemplate($view);
	    if (!empty($known_view)) {
    		$_this->render($view, $args, true, false);
    	} else {
    	    _error(sprintf('Unknown view file "%s"!', $view));
    	}
	}	
}

if (!function_exists('_replace')) 
{
	function _replace($search, $replace, $string)
	{
	    echo str_replace($search, $replace, _string($string));
	}	
}

if (!function_exists('_set')) 
{
	function _set($var, $val)
	{
	    extract(array($var=>$val), EXTR_OVERWRITE);
	}	
}

if (!function_exists('_string')) 
{
	function _string($value, $glue = ', ')
	{
	    echo TemplateEngine::__string($value, $glue);
	}	
}

if (!function_exists('_unset')) 
{
	function _unset($var)
	{
	    extract(array($var=>null), EXTR_OVERWRITE);
	}	
}

if (!function_exists('_upper')) 
{
	function _upper($value)
	{
	    echo strtoupper(TemplateEngine::__string($value));
	}	
}

if (!function_exists('_url')) 
{
	function _url($param = null, $value = null, $url = null)
	{
	    return UrlHelper::url($param, $value, $url);
	}	
}

if (!function_exists('_values')) 
{
	function _values($array)
	{
	    return array_values(_array($array));
	}	
}

// Endfile