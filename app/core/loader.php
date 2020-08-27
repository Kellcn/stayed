<?php
namespace vgace\core;

class Loader{
    private $paras;
    public static function &get($_class, $conf = []){
        $name = strtolower($_class);
        if (!file_exists(APP_PATH . 'librarys/' . $name . EXT)) {
            echo '文件不存在';
            exit;
        }
        
        require_once (APP_PATH . 'librarys/' . $name . EXT);
        
        $className = '\Library\\'.ucfirst($name);
        $class = new $className($conf);
        return $class;
    }
    
    public static function &reg($_class, $conf = []){
        $name = strtolower($_class);
        if (!file_exists(APP_PATH . 'plugins/' . $name . EXT)) {
            echo '文件不存在';
            exit;
        }
        
        require_once (APP_PATH . 'plugins/' . $name . EXT);
        
        $className = ucfirst($name);
        $class = new $className($conf);
        return $class;
    }

}
