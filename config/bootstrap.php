<?php
$bootstrap = glob(__DIR__ . '/../modules/*/bootstrap.php');
if ($bootstrap) {
    foreach ($bootstrap as $file) {
        include $file;
    }
}
