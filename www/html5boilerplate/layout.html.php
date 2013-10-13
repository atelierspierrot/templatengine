<?php

if (!isset($merge_css)) $merge_css = false;
if (!isset($minify_css)) $minify_css = false;
if (!isset($merge_js)) $merge_js = false;
if (!isset($minify_js)) $minify_js = false;

// --------------------------------
// the "classic" assets web accessible directory
if (empty($assets)) {
    $assets = $_template->getAssetsLoader()->getAssetsWebPath();
    //trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__.'/../'), '/');
}
if (strlen($assets)) {
    $assets = rtrim($assets, '/').'/';
}

// --------------------------------
// the "template engine" assets web accessible directory
if (empty($tple_assets)) {
    $tple_assets = $_template->getAssetsLoader()->findInPackage('', 'atelierspierrot/templatengine');
}
if (empty($tple_assets)) {
    $tple_assets = $assets;
}
if (strlen($tple_assets)) {
    $tple_assets = rtrim($tple_assets, '/').'/';
}

// --------------------------------
// the Boilerplate assets web accessible directory
if (empty($boilerplate_assets)) {
    $boilerplate_assets = $_template->getAssetsLoader()->findInPackage('html5boilerplate', 'atelierspierrot/templatengine');
}
if (empty($boilerplate_assets)) {
    $boilerplate_assets = $_template->getAssetsLoader()->findInPath('html5boilerplate', $assets);
}
if (strlen($boilerplate_assets)) {
    $boilerplate_assets = rtrim($boilerplate_assets, '/').'/';
}

// ------------------
// METAS
$old_metas = $_template->getTemplateObject('MetaTag')->get();
$_template->getTemplateObject('MetaTag')->reset();

// => charset and others
$_template->getTemplateObject('MetaTag')
	->add('Content-Type', 'text/html; charset=UTF-8', true)
	->add('X-UA-Compatible', 'IE=edge,chrome=1', true, 'IE')
	->add('viewport', 'width=device-width');

// => description
if (!empty($meta_description)) {
	$_template->getTemplateObject('MetaTag')
		->add('description', $meta_description);
}

// => keywords
if (!empty($meta_keywords)) {
	$_template->getTemplateObject('MetaTag')
		->add('keywords', $meta_keywords);
}

// => author
if (!empty($author)) {
	$_template->getTemplateObject('MetaTag')
		->add('author', $author);
}

// => generator
if (!empty($app_name) && !empty($app_version)) {
	$_template->getTemplateObject('MetaTag')
		->add('generator', $app_name.(!empty($app_version) ? ' '.$app_version : ''));
}

// => + old ones
$_template->getTemplateObject('MetaTag')->set($old_metas);

// ------------------
// LINKS
$old_links = $_template->getTemplateObject('LinkTag')->get();
$_template->getTemplateObject('LinkTag')->reset();

// => favicon.ico
if (file_exists($assets.'icons/favicon.ico')) {
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'icon',
			'href'=>$assets.'icons/favicon.ico',
			'type'=>'image/x-icon'
		) )
		->add( array(
			'rel'=>'shortcut icon',
			'href'=>$assets.'icons/favicon.ico',
			'type'=>'image/x-icon'
		) );
}

// the followings are taken from <http://mathiasbynens.be/notes/touch-icons>
// => For third-generation iPad with high-resolution Retina display: apple-touch-icon-144x144-precomposed.png
if (file_exists($assets.'icons/apple-touch-icon-144x144-precomposed.png')) {
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$assets.'icons/apple-touch-icon-144x144-precomposed.png',
			'sizes'=>'144x144'
		) );
}
// => For iPhone with high-resolution Retina display: apple-touch-icon-114x114-precomposed.png
if (file_exists($assets.'icons/apple-touch-icon-114x114-precomposed.png')) {
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$assets.'icons/apple-touch-icon-114x114-precomposed.png',
			'sizes'=>'114x114'
		) );
}
// => For first- and second-generation iPad: apple-touch-icon-72x72-precomposed.png
if (file_exists($assets.'icons/apple-touch-icon-72x72-precomposed.png')) {
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$assets.'icons/apple-touch-icon-72x72-precomposed.png',
			'sizes'=>'72x72'
		) );
}
// => For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: apple-touch-icon-precomposed.png
if (file_exists($assets.'icons/apple-touch-icon-precomposed.png')) {
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$assets.'icons/apple-touch-icon-precomposed.png'
		) );
}

// => + old ones
$_template->getTemplateObject('LinkTag')->set($old_links);

// ------------------
// TITLE
$old_titles = $_template->getTemplateObject('TitleTag')->get();
$_template->getTemplateObject('TitleTag')->reset();

// => $title
if (!empty($title)) {
	$_template->getTemplateObject('TitleTag')
		->add( $title );
}
// => + old ones
$_template->getTemplateObject('TitleTag')->set($old_titles);
// => meta_title last
if (!empty($meta_title)) {
	$_template->getTemplateObject('TitleTag')
		->add( $meta_title );
}

// ------------------
// CSS
$old_css = $_template->getTemplateObject('CssFile')->get();
$_template->getTemplateObject('CssFile')->reset();

$_template->getTemplateObject('CssFile')
	->add($boilerplate_assets.'css/normalize.css')
	->add($boilerplate_assets.'css/main.css')
	->add($tple_assets.'css/styles.css')
	->add($tple_assets.'css/styles_ie_gte5.5.css', 'screen', '>=5.5')
	->add($tple_assets.'css/styles_ie_lt5.5.css', 'screen', '<5.5')
	// => + old ones
	->set($old_css);

// ------------------
// JS in header
$old_header_js = $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->get();
$_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->reset();

$_template->getTemplateObject('JavascriptFile', 'jsfiles_header')
	->addMinified($tple_assets.'vendor_assets/modernizr-2.6.2.min.js')
	// => + old ones
	->set($old_header_js);

// ------------------
// JS in footer
$old_footer_js = $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->get();
$_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->reset();

$_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')
	->addMinified($tple_assets.'vendor_assets/jquery-last.min.js')
	->add($boilerplate_assets.'js/plugins.js')
	->add($tple_assets.'js/scripts.js')
	// => + old ones
	->set($old_footer_js);

// --------------------------------
// the content
if (empty($content)) $content = '<p>Test content</p>';

//echo '<pre>';var_dump($_template);exit('yo');

?><!DOCTYPE html>
<?php
_iecc(
    '<html class="no-js lt-ie9 lt-ie8 lt-ie7">', '<7'
);
_echo("\n");
_iecc(
    '<html class="no-js lt-ie9 lt-ie8">', '7'
);
_echo("\n");
_iecc(
    '<html class="no-js lt-ie9">', '8'
);
_echo("\n");
_iecc(
    '<html class="no-js">', '>8', null, true
);
_echo("\n");
?>
    <head>
<?php
echo
	$_template->getTemplateObject('MetaTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('TitleTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('LinkTag')->write("\n\t\t %s ");

if (true===$minify_css)
	echo $_template->getTemplateObject('CssFile')->minify()->writeMinified("\n\t\t %s ");
elseif (true===$merge_css)
	echo $_template->getTemplateObject('CssFile')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('CssFile')->write("\n\t\t %s ");

if (true===$minify_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->minify()->writeMinified("\n\t\t %s ");
elseif (true===$merge_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->write("\n\t\t %s ");

_echo("\n");
?>
    </head>
    <body>
    <div id="page-wrapper">
<?php
_iecc(
    "\n".'<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>'."\n",
    '<7'
);
_echo("\n");
?>
        <a id="top"></a>

<?php foreach($_template->getPageStructure() as $item) : ?>
    <?php if (isset($$item)) : ?>

        <?php
            $item_layout = $item.'.html.php';
            $item_template = $_template->getTemplate($item_layout);
            if (empty($item_template)) {
                $item_template = $_template->getTemplate('html5boilerplate/'.$item_layout);
            }

            if (!empty($item_template)) :
                _render($item_template, array(
                    'content'=>$$item
                ));
            else :
        ?>
        <div id="<?php _getid($item); ?>" class="structure-<?php echo $item; ?>">

            <?php if (is_array($$item)) : ?>
            <ul>
                <?php foreach($$item as $var=>$val) : ?>
                <li><a href="<?php echo $var; ?>"><?php echo $val; ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php else : ?>
                <?php echo $$item; ?>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>

        <a id="bottom"></a>
    </div>

<?php
if (true===$minify_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->minify()->writeMinified("\n\t %s ");
elseif (true===$merge_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->write("\n\t %s ");

echo
	$_template->getTemplateObject('JavascriptTag')->write("%s"),
	$_template->getTemplateObject('CssTag')->write("%s"),
	"\n";
?>

    </body>
</html>
