<?php

use Michelf\Markdown;
?>
<style>

</style>

<div class="help-content markdown">
    <?= Markdown::defaultTransform($content) ?>
</div>

<style>
    .help-content {
        font-size: 14px;
        line-height: 1.6;
    }
</style>