<?php

use app\modules\core\widgets\Help;
?>

<?= Help::widget([
    'content' => " 
## 邮件标题
发送邮件时显示的标签

## 邮件内容

发送邮件时显示的内容，支持HTML格式

## 模板名称
仅用于界面中方便识别

## 模板键名
在代码中引用该模板时使用的键名，必须唯一
 
    ",
]) ?>