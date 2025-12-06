<?php
$modules = [];
$find_modules = glob(__DIR__ . '/../modules/*/Module.php');
foreach ($find_modules as $file) {
    $module = str_replace(__DIR__ . '/../modules/', '', $file);
    $module = str_replace('/Module.php', '', $module);
    $modules[$module] = [
        'class' => 'app\modules\\' . $module . '\Module',
    ];
    $helpers = glob(__DIR__ . '/../modules/' . $module . '/helper.php');
    if ($helpers) {
        foreach ($helpers as $helper) {
            include $helper;
        }
    }
}
return $modules;
