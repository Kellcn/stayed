<?php
// 检测PHP环境
if (version_compare(PHP_VERSION, '7.1.0', '<')) {
    die('require PHP > 7.1.0 !');
}

define('CHARSET', 'utf-8');
/* 项目目录 */
define('DOC_ROOT', str_replace('\\', '/', dirname(__FILE__)).'/');

define('APP_PATH', DOC_ROOT.'app/');

//是否开启debug
define('APP_DEBUG', true);

include APP_PATH.'Base.php';