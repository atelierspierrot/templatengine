<?php
/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 */

/**
 * This file defines some default functions to facilitate views writting
 *
 * All of these functions are prefixed by an underscore `_`.
 *
 * Many of these functions directly echoes their result by default ; in this case, you
 * can avoid this behavior setting the `$return` parmeter (*the last function parameter
 * most of the time*) on `true`.
 *
 */
use TemplateEngine\TemplateEngine;
use Library\Helper, Library\Tool;

if (!function_exists('_array')) 
{
    /**
     * Always return an array
     *
     * @param misc $array A value of any type
     * @return array An array built from the value passed
     */
	function _array($array)
	{
	    return (is_array($array) ? $array : array($array));
	}	
}

if (!function_exists('_attribute')) 
{
    /**
     * Build an HTML attribute string
     *
     * @param string $var The name of the attribute
     * @param string $val The value of the attribute
     * @return string A string representing the attribute/value couple ready to write as HTML attribute
     */
	function _attribute($var, $val)
	{
	    return Helper\Html::parseAttributes(array($var => $val));
	}	
}

if (!function_exists('_bit')) 
{
    /**
     * Build a text representation of a bit value as `true` or `false`
     *
     * @param bool $value The boolean value to represent
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _bit($value, $return = false)
	{
	    return _echo(true==$value ? 'true' : 'false', $return);
	}	
}

if (!function_exists('_cut')) 
{
    /**
     * Cut a string at a certain characters count adding it a suffix string
     *
     * @param string $str The original string to cut
     * @param int $length The length to use before cutting the string
     * @param string $end_str A suffix string added if the original string is cutted
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see Library\Helper\Text::cut()
     */
	function _cut($str, $length = 120, $end_str = ' ...', $return = false)
	{
        return _echo(Helper\Text::cut($str, $length, $end_str), $return);
	}	
}

if (!function_exists('_default')) 
{
    /**
     * Get a variable value if it is defined, or a default value otherwise
     *
     * @param string $val The variable to search
     * @param misc $default The default value returned if the variable was not set
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _default($val, $default = '', $return = false)
	{
	    return _echo(isset($val) ? $val : $default, $return);
	}	
}

if (!function_exists('_dump')) 
{
    /**
     * Dump an array
     *
     * @param array $array The original array to dump
     * @param bool $only_string_index Show the indexes only if it is a string (`false` by default)
     * @param srting $mask_item HTML mask used to build each item dump, parsed with `sprintf($mask, $index, $value)`
     * @param srting $mask_global HTML mask used to build the full dump string, parsed with `sprintf($mask, $items_dump)`
     * @param srting $no_index_mask_item HTML mask used to build each item dump without string index, parsed with `sprintf($mask, $value)`
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _dump($array, $only_string_index = false, $mask_item = '<li>%s: %s</li>', $mask_global = '<ul>%s</ul>', $no_index_mask_item = '<li>%s</li>', $return = false)
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
	    return _echo(sprintf($mask_global, $list), $return);
	}	
}

if (!function_exists('_echo')) 
{
    /**
     * Echo or return a value
     *
     * This is one of the most important methods of the TemplateEngine views ; it allows to
     * set up a flag in a function to choose between directly printing the result or returning it:
     *
     *     return _echo( $my_value, $bool_flag )
     *
     * which will result in:
     *
     *     // if $bool_flag = false
     *     echo $my_value; return true;
     *     // if $bool_flag = true
     *     return $my_value;
     *
     * In other views methods, this flag is identify as:
     *
     *     $return Return/Echo flag (default is to echo result)
     *
     * The best practice is to use this method once a function may `echo` its result but when
     * this result could be returned otherwise.
     *
     * @param string $what The original string to echo or return
     * @param bool $return Return or print the string (default is `false` : string is printed)
     * @return misc The result of the `_echo` function (string or bool)
     */
	function _echo($what, $return = false)
	{
	    if (true===$return) {
    	    return $what;
	    } else {
    	    echo $what;
    	    return true;
	    }
	}	
}

if (!function_exists('_else')) 
{
    /**
     * Emulate a "if ... else ..."
     *
     * @param bool $condition A condition to test
     * @param string/callable $if_false A string or a closure to use if `$condition` is `false`
     * @param string/callable $if_true A string or a closure to use if `$condition` is `true`
     * @param array $args An optional array of arguments to pass to the true or false closure
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _if()
     */
	function _else($condition, $if_false = '', $if_true = '', $args = null, $return = false)
	{
	    _if($condition, $if_true, $if_false, _array($args), $return);
	}	
}

if (!function_exists('_error')) 
{
    /**
     * Throws a TemplateEngine error
     *
     * @param string $message The message of the error
     * @param string $file The filename throwing the error
     * @param int $line The line of the file throwing the error
     * @return void The error string is printed
     * @see TemplateEngine\TemplateEngine::__error()
     */
	function _error($message = 'View error', $file = null, $line = null)
	{
	    TemplateEngine::__error($message, $file, $line);
	}	
}

if (!function_exists('_escape')) 
{
    /**
     * Protect a string with `htmlentities`
     *
     * @param string $str The string to protect
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _escape($str, $return = false)
	{
	    return _echo(htmlentities(_string($str)), $return);
	}	
}

if (!function_exists('_eval')) 
{
    /**
     * Evaluation of code
     *
     * @param string $fct The string to evauate
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _eval($fct, $return = false)
	{
        ob_start();
        eval($fct);
        $output = ob_get_contents();
        ob_end_clean();
        return _echo($output, $return);
	}	
}

if (!function_exists('_filename')) 
{
    /**
     * Get a formated filename
     *
     * @param string $string The filename to format
     * @param boolean $lowercase Should we return the name un lowercase (FALSE by default)
     * @param string $delimiter The demiliter used for special chars substitution
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see Library\Helper\File::formatFilename()
     */
	function _filename($filename, $lowercase = false, $delimiter = '-', $return = false)
	{
	    return _echo(Helper\File::formatFilename($filename, $lowercase, $delimiter), $return);
	}	
}

if (!function_exists('_getid')) 
{
    /**
     * Get a uniq DOM ID for an element
     *
     * @param string $reference A reference used to store the ID (and retrieve it - by default, a uniqid)
     * @param string|bool $base_id A string that will be used to construct the ID, if set to `true`, the reference will be used as `$base_id`)
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see Library\Helper\Html::getId()
     */
	function _getid($reference = null, $base_id = null, $return = false)
	{
	    return _echo(Helper\Html::getId($reference, $base_id), $return);
	}	
}

if (!function_exists('_if')) 
{
    /**
     * Emulation of "if ... else ..."
     *
     * The `$if_false` and `$is_true` parameters can be a callable closure that will be evaluated
     * passing it the `$condition`and the `$args` array as argument.
     *
     * @param bool $condition A condition to test
     * @param string/callable $if_true A string or a closure to use if `$condition` is `true`
     * @param string/callable $if_false A string or a closure to use if `$condition` is `false`
     * @param array $args An optional array of arguments to pass to the true or false closure
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see TemplateEngine\TemplateEngine::__closurable()
     */
	function _if($condition, $if_true = '', $if_false = '', $args = null, $return = false)
	{
	    array_unshift(_array($args), $condition);
	    return _echo(TemplateEngine::__closurable(
	        (true===$condition ? $if_true : $if_false), _array($args)
	    ), $return);
	}	
}

if (!function_exists('_isfalse')) 
{
    /**
     * Test if a value is evaluated as `false`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _isfalse($val)
	{
	    return (bool) false==$val;
	}	
}

if (!function_exists('_isnotfalse')) 
{
    /**
     * Test if a value is NOT evaluated as `false`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _isnotfalse($val)
	{
	    return (bool) false!=$val;
	}	
}

if (!function_exists('_isnottrue')) 
{
    /**
     * Test if a value is NOT evaluated as `true`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _isnottrue($val)
	{
	    return (bool) true!=$val;
	}	
}

if (!function_exists('_isnotnull')) 
{
    /**
     * Test if a value is NOT evaluated as `null`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _isnotnull($val)
	{
	    return (bool) null!=$val;
	}	
}

if (!function_exists('_isnull')) 
{
    /**
     * Test if a value is evaluated as `null`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _isnull($val)
	{
	    return (bool) null==$val;
	}	
}

if (!function_exists('_istrue')) 
{
    /**
     * Test if a value is evaluated as `true`
     *
     * @param misc $val The value to test
     * @return bool The result of the test
     */
	function _istrue($val)
	{
	    return (bool) true==$val;
	}	
}

if (!function_exists('_javascript')) 
{
    /**
     * Protect a string for javascript usage
     *
     * @param string $str The HTML string to protect
     * @param bool $protect_quotes Protect all quotes (simple and double) with a slash
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see Library\Helper\Html::javascriptProtect()
     */
	function _javascript($str, $protect_quotes = false, $return = false)
	{
	    return _echo(Helper\Html::javascriptProtect($str, $protect_quotes), $return);
	}	
}

if (!function_exists('_keys')) 
{
    /**
     * Get the keys of an array
     *
     * @param array $array The array to work on
     * @return array The keys of the original array
     */
	function _keys($array)
	{
	    return array_keys(_array($array));
	}	
}

if (!function_exists('_loremipsum')) 
{
    /**
     * Get a fake "lorem ipsum" string 
     *
     * @param string $length A flag for the fake string length (default is `short`)
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _loremipsum($length = 'short', $return = false)
	{
	    if ('short'===$length) {
    	    $str = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    	} else {
    	    $str = "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.";
    	}
    	return _echo($str, $return);
	}	
}

if (!function_exists('_lower')) 
{
    /**
     * Get a string in lowercase
     *
     * @param string $value The string to transform
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _lower($value, $return = false)
	{
	    return _echo(strtolower(TemplateEngine::__string($value)), $return);
	}	
}

if (!function_exists('_newid')) 
{
    /**
     * Get a uniq DOM ID for an element
     *
     * @param string $reference A reference used to store the ID (and retrieve it - by default, a uniqid)
     * @param string|bool $base_id A string that will be used to construct the ID, if set to `true`, the reference will be used as `$base_id`)
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     * @see Library\Helper\Html::getNewId()
     */
	function _newid($reference = null, $base_id = null, $return = false)
	{
	    return _echo(Helper\Html::getNewId($reference, $base_id), $return);
	}	
}

if (!function_exists('_onoff')) 
{
    /**
     * Build a text representation of a bit value as `on` or `off`
     *
     * @param bool $value The boolean value to represent
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _onoff($value, $return = false)
	{
	    return _echo((true==$value ? 'on' : 'off'), $return);
	}	
}

if (!function_exists('_p')) 
{
    /**
     * Build an HTML paragraph
     *
     * @param string $str The content of the tag
     * @param array $attrs An array of `name => value` pairs for the tag HTML attributes
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _tag()
     */
	function _p($str, array $attrs = array(), $return = false)
	{
	    return _echo(_tag('p', $str, $attrs, true), $return);
	}	
}

if (!function_exists('_render')) 
{
    /**
     * Render a template view
     *
     * @param string $view The view filename to render
     * @param array $args An array of parameters to add in environment
     * @return void The rendering is printed
     * @throws Throws an error if the view file is not found
     */
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
    /**
     * Alias of `str_replace`
     *
     * @param string $search The string to search and replace
     * @param string $replace The replacement string
     * @param string $string The global string where to process the replacement
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see str_replace()
     * @see _echo()
     */
	function _replace($search, $replace, $string, $return = false)
	{
	    return _echo(str_replace($search, $replace, _string($string)), $return);
	}	
}

if (!function_exists('_set')) 
{
    /**
     * Set a variable in the views environement
     *
     * @param string $var The variable name to set
     * @return void
     */
	function _set($var, $val)
	{
	    extract(array($var=>$val), EXTR_OVERWRITE);
	}	
}

if (!function_exists('_string')) 
{
    /**
     * Always return a string
     *
     * @param misc $value The value to build the string
     * @param string $glue A "glue" used to join array items if `$value` is an array
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see TemplateEngine\TemplateEngine::__string()
     * @see _echo()
     */
	function _string($value, $glue = ', ', $return = false)
	{
	    return _echo(TemplateEngine::__string($value, $glue), $return);
	}	
}

if (!function_exists('_tag')) 
{
    /**
     * Build an HTML tag block
     *
     * @param string $type The name of the tag to build
     * @param string $content The content of the tag
     * @param array $attrs An array of `name => value` pairs for the tag HTML attributes
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _tag($type, $content, array $attrs = array(), $return = false)
	{
	    $attr_str = '';
	    if (!empty($attrs)) {
	        foreach($attrs as $var=>$val) {
	            $attr_str .= ' '._attribute($var, $val);
	        }
	    }
	    return _echo('<'.$type.$attr_str.'>'.$content.'</'.$type.'>', $return);
	}	
}

if (!function_exists('_unset')) 
{
    /**
     * Unset a variable in the views environement
     *
     * @param string $var The variable name to unset
     * @return void
     */
	function _unset($var)
	{
	    extract(array($var=>null), EXTR_OVERWRITE);
	}	
}

if (!function_exists('_upper')) 
{
    /**
     * Get a string in uppercase
     *
     * @param string $value The string to transform
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see _echo()
     */
	function _upper($value, $return = false)
	{
	    return _echo(strtoupper(TemplateEngine::__string($value)), $return);
	}	
}

if (!function_exists('_url')) 
{
    /**
     * URL manager
     *
     * @param string/array/null $param A parameter to set, or an array like `param => value` to set in URL
     * @param string/null $value The value of the `$param` argument (if it is a string)
     * @param string/null $url The URL to work on (`self::getRequestUrl()` by default)
     * @param bool $return Return/Echo flag (default is to echo result)
     * @return misc The result of the `_echo` function (string or bool)
     * @see Library\Helper\Url::url()
     * @see _echo()
     */
	function _url($param = null, $value = null, $url = null, $return = false)
	{
	    return _echo(Helper\Url::url($param, $value, $url), $return);
	}	
}

if (!function_exists('_use')) 
{
    /**
     * Assets packages automatic inclusion
     *
     * @param string $package_name The assets package name to include
     * @return void
     */
	function _use($package_name = null)
	{
	    if (empty($package_name)) return;
	    TemplateEngine::getInstance()->useAssetsPackage($package_name);
	}	
}

if (!function_exists('_values')) 
{
    /**
     * Get the values of an array
     *
     * @param array $array The array to work on
     * @return array The values of the original array
     */
	function _values($array)
	{
	    return array_values(_array($array));
	}	
}

// Endfile