<?php

use app\assets\AdminAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\modules\core\classes\User;
use app\modules\core\classes\ShopMenu as Menu;
use app\modules\core\widgets\Language;

AdminAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// 预加载 Font Awesome 字体，减少页面切换时图标闪烁
$this->registerLinkTag([
    'rel' => 'preload',
    'as' => 'font',
    'href' => Yii::getAlias('@web/lib/fontawesome-free-7.0.1-web/webfonts/fa-solid-900.woff2'),
    'type' => 'font/woff2',
    'crossorigin' => 'anonymous'
]);
$this->registerLinkTag([
    'rel' => 'preload',
    'as' => 'font',
    'href' => Yii::getAlias('@web/lib/fontawesome-free-7.0.1-web/webfonts/fa-regular-400.woff2'),
    'type' => 'font/woff2',
    'crossorigin' => 'anonymous'
]);
$this->registerLinkTag([
    'rel' => 'preload',
    'as' => 'font',
    'href' => Yii::getAlias('@web/lib/fontawesome-free-7.0.1-web/webfonts/fa-brands-400.woff2'),
    'type' => 'font/woff2',
    'crossorigin' => 'anonymous'
]);

$user = User::getCurrent('shop');

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <!-- 顶部导航栏 -->
    <header id="header">
        <?php
        NavBar::begin([
            'brandLabel' => Yii::t('app', '商家控制台'),
            'brandUrl' => '/core/shop/index',
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top'],
            'renderInnerContainer' => false,
        ]);

        // 左侧菜单切换按钮（移动端）
        echo '<button class="navbar-toggler sidebar-toggle me-2" type="button">
            <span class="navbar-toggler-icon"></span>
          </button>';
        $role = 'shop';
        // 右侧用户菜单
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => [
                Yii::$app->$role->isGuest
                    ? ['label' => Yii::t('app', '登录'), 'url' => ['/core/login']]
                    : [
                        'label' => '<img src="' . ($user->avatarReset) . '" class="rounded-circle me-2" width="32" height="32" alt="' . Yii::t('app', '头像') . '">' . ($user->name ?? Yii::t('app', '用户')),
                        'encode' => false,
                        'items' => [
                            ['label' => Yii::t('app', '个人资料'), 'url' => '/core/user-setting'],
                            ['label' => Yii::t('app', '退出登录'), 'url' => '/core/logout'],
                        ]
                    ]
            ]
        ]);
        // 语言选择器
        echo Language::widget();
        NavBar::end();
        ?>
    </header>

    <!-- 主要内容区域 -->
    <div class="container-fluid" style="margin-top: 56px;">
        <div class="row">
            <!-- 左侧菜单 -->
            <nav class="sidebar">
                <div class="nav flex-column">
                    <?php
                    $menuItems = Menu::get();
                    foreach ($menuItems as $item):
                        if (isset($item['items']) && !empty($item['items'])):
                            // 检查子菜单是否有激活项
                            $hasActiveSubItem = false;
                            foreach ($item['items'] as $subItem) {
                                if (isset($subItem['active']) && $subItem['active']) {
                                    $hasActiveSubItem = true;
                                    break;
                                }
                            }

                            // 根据是否有激活子项决定父菜单的展开状态
                            $dropdownToggleClass = 'nav-link dropdown-toggle';
                            $dropdownMenuClass = 'dropdown-menu';
                            if ($hasActiveSubItem) {
                                $dropdownToggleClass .= ' active'; // 父菜单也激活
                                $dropdownMenuClass .= ' show'; // 展开下拉菜单
                            } else {
                                $dropdownToggleClass .= ' collapsed';
                            }
                    ?>
                            <!-- 带下拉菜单的项目 -->
                            <div class="dropdown">
                                <?= Html::a(
                                    '<i class="' . $item['icon'] . ' me-2"></i>' . lang($item['label']) . ' <i class="fas fa-chevron-down ms-auto"></i>',
                                    $item['url'],
                                    array_merge(['class' => $dropdownToggleClass], $item['data'] ? ['data-' . key($item['data']) => current($item['data'])] : [])
                                ) ?>
                                <div class="<?= $dropdownMenuClass ?>">
                                    <?php foreach ($item['items'] as $subItem): ?>
                                        <?php
                                        $dropdownItemClass = 'dropdown-item';
                                        if (isset($subItem['active']) && $subItem['active']) {
                                            $dropdownItemClass .= ' active';
                                        }
                                        ?>
                                        <?= Html::a(
                                            lang($subItem['label']),
                                            $subItem['url'],
                                            array_merge(['class' => $dropdownItemClass], $subItem['data'] ? ['data-' . key($subItem['data']) => current($subItem['data'])] : [])
                                        ) ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- 单个菜单项 -->
                            <?php
                            $navLinkClass = 'nav-link';
                            if (isset($item['active']) && $item['active']) {
                                $navLinkClass .= ' active';
                            }
                            ?>
                            <?= Html::a(
                                '<i class="' . $item['icon'] . ' me-2"></i>' . $item['label'],
                                $item['url'],
                                array_merge(['class' => $navLinkClass], $item['data'] ? ['data-' . key($item['data']) => current($item['data'])] : [])
                            ) ?>
                    <?php endif;
                    endforeach; ?>
                </div>
            </nav>

            <!-- 右侧主要内容区域 -->
            <main class="main-content">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget([
                        'homeLink' => ['label' => Yii::t('app', '首页'), 'url' => '/core/shop'],
                        'links' => $this->params['breadcrumbs'],
                        'options' => ['class' => 'breadcrumb mb-3']
                    ]) ?>
                <?php endif ?>

                <div style="margin-left: 20px;margin-right:25px;">
                    <?= Alert::widget() ?>
                </div>

                <div class="content-wrapper">
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>