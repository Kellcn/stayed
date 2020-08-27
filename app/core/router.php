<?php
namespace vgace\core;

class Router {
    private $controller;
    private $func;

    public function __construct() {
        $url = $_SERVER["REQUEST_URI"];
        $url = ltrim($url, 'index.php');
        $url = substr(self::_clear_url($url), strlen(__ROOT__));
        //$url = ltrim($url, __APP__ . '/');
        
        if ($url == 'favicon.ico') exit();

        if ($url) {
            $_url = $url;
            if (strpos($url, "?") !== false) {
                $_url = substr($url, 0, strpos($url, "?"));
            }
            
            $routeUrl         = explode("/", $_url);
            $this->controller = isset($routeUrl[0]) ? $routeUrl[0] : "index";
            $this->func       = isset($routeUrl[1]) ? $routeUrl[1] : "index";
        }
    }
    
    public static function _clear_url($_url){
        if (strpos($_url, "?") !== false) {
            $_url = substr($_url, 0, strpos($_url, "?"));
        }
        /* 替换'//'为'/' */
        $_url = preg_replace('/\/\//','/', $_url);
        return $_url;
    }
    
    public function getController() {
        if (empty($this->controller))
            $this->controller = "index";
        return $this->controller;
    }

    public function getFunc() {
        if (empty($this->func))
            $this->func = "index";
        return $this->func;
    }
    
}
