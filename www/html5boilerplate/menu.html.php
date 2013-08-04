<?php

use Library\Helper\Html as HtmlHelper;

// --------------------------------
// the javascript
$_template->getTemplateObject('JavascriptTag')->set(array(
    "
$(function() {
    initBacklinks();
    activateNavigationMenu();
    $(\"#user_agent\").html( navigator.userAgent );
});
    "
));

// --------------------------------
// the content
if (empty($content)) $content = '<p>Test content</p>';

$public_sources = true;
$url_sources = "http://github.com/atelierspierrot/templatengine";
$host_sources_name = "GitHub";
$host_sources_home = "http://github.com/";

$make_navigation_list_from_content = function(array $ctt, array $attrs)
{
    $str = '';
    foreach ($ctt as $var=>$val) {
        if (is_array($val)) {
            $str .= '<li><a href="#">'.$var.'</a>'
                .$make_navigation_list_from_content($val)
                .'</li>';
        } else {
            $str .= '<li><a href="'.$val.'">'.$var.'</a></li>';
        }
    }
    
    return '<ul'.(!empty($attrs) ? HtmlHelper::parseAttributes($attrs) : '').'>'.$str.'</ul>';
}

?>
<nav>
<?php if (is_array($content)) : ?>
    <?php echo $make_navigation_list_from_content($content, array(
        'id'=>"navigation_menu", 'class'=>"menu", 'role'=>"navigation"
    )); ?>
<?php else : ?>
    <?php echo $content; ?>
<?php endif; ?>
<?php if ($public_sources) : ?>
    <div class="info">
        <p><span class="icon github-icon""></span>&nbsp;<a href="<?php echo $url_sources; ?>">See online on <?php echo $host_sources_name; ?></a></p>
        <p class="comment small">The sources of this package are hosted on <a href="<?php echo $host_sources_home; ?>"><?php echo $host_sources_name; ?></a>. To follow sources updates, report a bug or read opened bug tickets and any other information, please see the link above.</p>
    </div>
<?php endif; ?>
    <p class="credits" id="user_agent"></p>
</nav>

<div class="back_menu" id="short_navigation">
    <a href="#" title="See navigation menu" id="short_menu_handler"><span class="text">Navigation Menu</span></a>
    &nbsp;|&nbsp;
    <a href="#bottom" title="Go to the bottom of the page"><span class="text">Go to bottom&nbsp;</span>&darr;</a>
    &nbsp;|&nbsp;
    <a href="#top" title="Back to the top of the page"><span class="text">Back to top&nbsp;</span>&uarr;</a>
    <ul id="short_menu" class="menu" role="navigation"></ul>
</div>
