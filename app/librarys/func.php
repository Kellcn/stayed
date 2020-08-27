<?php
namespace Local;

class Func
{
    
    public static function filterStr($str = ''){
        if(!$str) return $str;
        
        return self::daddslashes($str);
    }
    
    /**
     * 序列化
     * @param mixed $string 原始信息
     * @param intval $force
     * @return mixed
     */
    public static function daddslashes($string, $force = 1) {
        if(is_array($string)) {
            $keys = array_keys($string);
            foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = self::daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }
    
    public static function dstripslashes($string) {
        if(empty($string)) return $string;
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::dstripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }
    
    public static function dhtmlspecialchars($string, $flags = null) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::dhtmlspecialchars($val, $flags);
            }
        } else {
            if($flags === null) {
                $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
                if(strpos($string, '&amp;#') !== false) {
                    $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
                }
            } else {
                if(strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
        return $string;
    }
    
    
    
    
    
}
