<?php
namespace vgace\core;

class Registry
{
    public static $_paras = [];
    
    /**
     * @desc set
     * @param string $key
     * @param array $param
     */
    public static function set($key = '', $param = []){
        self::$_paras[$key] =& $param;
        return;
    }
    
    public static function get($key = ''){
        if(!isset(self::$_paras[$key])) return;
        
        return self::$_paras[$key];
    }
}
