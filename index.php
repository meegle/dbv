<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib/functions.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DBV.php';

spl_autoload_register(function($className) {
    $separator = '\\';

    $arr = explode($separator, ltrim($className, $separator));
    array_shift($arr);

    $className = array_pop($arr);

    array_walk($arr, function (&$item) {
        $item = strtolower($item);
    });

    // 加载类文件
    $file = DBV_ROOT_PATH.DS.'lib'.DS.implode(DS, $arr).DS.$className.'.php';
    if (file_exists($file)) require_once $file;
});

$dbv = DBV::instance();
$dbv->authenticate();
$dbv->dispatch();