<?php

use Library\Helper\Html as HtmlHelper;

// --------------------------------
// the content
if (empty($content)) $content = 'Test title';
if (!is_array($content)) $content = array($content);

?>
<header id="top" role="banner">
    <hgroup>
        <h1><?php echo isset($content['title']) ? $content['title'] : $content[0]; ?></h1>
    <?php if (isset($content['subheader'])) : ?>
        <h2 class="slogan"><?php echo $content['subheader']; ?></h2>
    <?php endif; ?>
    </hgroup>
    <?php if (isset($content['slogan'])) : ?>
        <div class="hat"><?php echo $content['slogan']; ?></div>
    <?php endif; ?>
</header>
