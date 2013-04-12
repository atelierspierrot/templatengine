<?php

use Library\Helper\Html as HtmlHelper;

// --------------------------------
// the javascript
$_template->getTemplateObject('JavascriptTag')->set(array(
    "
$(function() {
    addCSSValidatorLink('<?php echo $assets; ?>css/styles.css');
    addHTMLValidatorLink();
});
    "
));

// --------------------------------
// the content
if (empty($content)) $content = 'Test footer';
if (!is_array($content)) $content = array($content);

?>
<div class="credits float-left">
    This page is <a href="" title="Check now online" id="html_validation">HTML5</a> & <a href="" title="Check now online" id="css_validation">CSS3</a> valid.
    <br />
    <?php echo isset($content['left']) ? $content['left'] : $content[0]; ?>
</div>
<?php if (isset($content['right'])) : ?>
<div class="credits float-right">
    <?php echo $content['right']; ?>
</div>
<?php endif; ?>
