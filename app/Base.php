<?php

define('CORE_PATH', APP_PATH.'core/');
define('LIBS_PATH', APP_PATH.'librarys/');
define('CONT_PATH', APP_PATH.'controllers/');
define('MODS_PATH', APP_PATH.'models/');

define('EXT', '.php');

/* 入口文件 */
define('__APP__', $_SERVER['SCRIPT_NAME']);
/* 安装目录 */
define('__ROOT__', str_replace(basename(__APP__), "", __APP__));

session_start();

//autoload core files
foreach(scandir(CORE_PATH) as $file){
    if(is_file(CORE_PATH . $file)){
        require_once(CORE_PATH . $file);
    }
}

//autoload librarys files
foreach(scandir(LIBS_PATH) as $file){
    if(is_file(LIBS_PATH . $file)){
        require_once(LIBS_PATH . $file);
    }
}

//autoload models files
foreach(scandir(MODS_PATH) as $file){
    if(is_file(MODS_PATH . $file)){
        require_once(MODS_PATH . $file);
    }
}

\vgace\Init::run();