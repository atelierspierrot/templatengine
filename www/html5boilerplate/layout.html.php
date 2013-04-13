<?php

use Library\Helper\Html as HtmlHelper;

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
// the Boilerplate assets web accessible directory
if (empty($boilerplate_assets)) {
    $boilerplate_assets = $_template->getAssetsLoader()->findInPath('html5boilerplate', $assets);
}
if (strlen($boilerplate_assets)) {
    $boilerplate_assets = rtrim($boilerplate_assets, '/').'/';
}

// ------------------
// metas
$old_metas = $_template->getTemplateObject('MetaTag')->get();
$_template->getTemplateObject('MetaTag')->reset();

// => charset and others
$_template->getTemplateObject('MetaTag')
	->add('Content-Type', 'text/html; charset=UTF-8', true)
	->add('X-UA-Compatible', 'IE=edge,chrome=1', true)
	->add('viewport', 'width=device-width');

// => description
if (!empty($meta_description))
{
	$_template->getTemplateObject('MetaTag')
		->add('description', $meta_description);
}
// => keywords
if (!empty($meta_keywords))
{
	$_template->getTemplateObject('MetaTag')
		->add('keywords', $meta_keywords);
}
// => author
if (!empty($author))
{
	$_template->getTemplateObject('MetaTag')
		->add('author', $author);
}
// => generator
if (!empty($app_name) && !empty($app_version))
{
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
if (file_exists($assets.'icons/favicon.ico'))
{
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
// => + old ones
$_template->getTemplateObject('LinkTag')->set($old_links);

// ------------------
// TITLE
$old_titles = $_template->getTemplateObject('TitleTag')->get();
$_template->getTemplateObject('TitleTag')->reset();

// => $title
if (!empty($title))
{
	$_template->getTemplateObject('TitleTag')
		->add( $title );
}
// => + old ones
$_template->getTemplateObject('TitleTag')->set($old_titles);
// => meta_title last
if (!empty($meta_title))
{
	$_template->getTemplateObject('TitleTag')
		->add( $meta_title );
}

// --------------------------------
// the content
if (empty($content)) $content = '<p>Test content</p>';

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
<?php
echo
	$_template->getTemplateObject('MetaTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('TitleTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('LinkTag')->write("\n\t\t %s "),
	"\n";
?>
        <link rel="stylesheet" href="<?php echo $boilerplate_assets; ?>css/normalize.css">
        <link rel="stylesheet" href="<?php echo $boilerplate_assets; ?>css/main.css">
        <link rel="stylesheet" href="<?php echo $assets; ?>vendor/blue/style.css">
        <link rel="stylesheet" href="<?php echo $assets; ?>vendor/jquery.highlight.css">
        <link rel="stylesheet" href="<?php echo $assets; ?>css/styles.css">
        <script src="<?php echo $assets; ?>vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
    <div id="page-wrapper">
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

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
        <div id="<?php echo HtmlHelper::getId($item); ?>" class="structure-<?php echo $item; ?>">

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

    </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo $assets; ?>vendor/jquery-1.9.1.min.js"><\/script>')</script>
        <script src="<?php echo $boilerplate_assets; ?>js/plugins.js"></script>
        <script src="<?php echo $assets; ?>vendor/jquery.highlight.js"></script>
        <script src="<?php echo $assets; ?>vendor/jquery.metadata.js"></script>
        <script src="<?php echo $assets; ?>vendor/jquery.tablesorter.min.js"></script>
        <script src="<?php echo $assets; ?>js/scripts.js"></script>
<?php
echo
	$_template->getTemplateObject('JavascriptTag')->write("%s"),
	"\n";
?>

    </body>
</html>
