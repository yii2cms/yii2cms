<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .error-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }

        .error-title {
            font-size: 24px;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .error-message {
            background: #fff;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .error-actions a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1 class="error-title"><?= Html::encode($this->title) ?></h1>

        <div class="error-message">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <div class="error-actions">
            <a href="<?= Yii::$app->homeUrl ?>">返回首页</a>
            <span> | </span>
            <a href="javascript:history.back()">返回上一页</a>
        </div>
    </div>
</body>

</html>

<?php
exit();
?>