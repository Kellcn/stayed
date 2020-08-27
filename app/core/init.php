<?php
namespace vgace;
use vgace\core\Input;
use vgace\core\Registry;
use Local\Func;

class Init{
    private $startTime;
    private $endTime;
    private $_configInit;
    
    public function __construct() {
        $this->_init_config();
        $this->_init_log();
        $this->_init_input();
        $this->_init_router();
        $this->_init_ComposerAutoload();
    }
    
    public static function run() {
        static $object;
        if(empty($object)) {
            $object = new self();
        }
        return $object;
    }
    
    /**
     * @desc 配置
     */
    public function _init_config(){
        if(APP_DEBUG){
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        }else{
            error_reporting(0);
        }
        list($usec, $sec) = explode(" ", microtime());
        $this->startTime = ((float)$usec + (float)$sec);
        
        define('MAGIC_QUOTES_GPC',  function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('REQUEST_METHOD',    $_SERVER['REQUEST_METHOD']);
        define('IS_GET',            REQUEST_METHOD == 'GET' ? TRUE : FALSE);
        define('IS_POST',           REQUEST_METHOD == 'POST' ? TRUE : FALSE);
        define('IS_PUT',            REQUEST_METHOD == 'PUT' ? TRUE : FALSE);
        define('IS_DELETE',         REQUEST_METHOD == 'DELETE' ? TRUE : FALSE);
        
        date_default_timezone_set('Asia/Shanghai');
        @header('Content-Type: text/html; charset='.CHARSET);
        
        $this->_configInit = require_once DOC_ROOT . '/config/init.php';
    }
    
    public function _init_input(){
        if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            echo '未知的参数类型';
            exit;
        }
        
        include_once(LIBS_PATH.'/Logfile/Ip.php');
        
        self::_xss_check();
        $_GET     = Input::get();
        $_POST    = Input::post();
        $_COOKIE  = Input::cookie();
        $_SESSION = Input::session();
        
        if(MAGIC_QUOTES_GPC) {
            $_GET     = Func::dstripslashes($_GET);
            $_POST    = Func::dstripslashes($_POST);
            $_COOKIE  = Func::dstripslashes($_COOKIE);
            $_SESSION = Func::dstripslashes($_SESSION);
        }
        
        $G['configInit'] = $this->_configInit;
        
        if (isset($_GET['__id']) && $_GET['__id']) {
            $G['__id'] = (int)$_GET['__id'];
        } else {
            $G['__id'] = ($this->startTime * 10000) . '' . rand(1000, 9999);
        }
        \Local\Log::pushNotice('__id', $G['__id']);
        \Local\Log::pushNotice('url', $_SERVER['REQUEST_URI']);
        
        $clientip = isset($_GET['clientip']) && $_GET['clientip'] ? $_GET['clientip'] : '';
        if (!$clientip) {
            $clientip = \Local\Logfile\Ip::getConnectIp();
        }
        \Local\Log::pushNotice('ip', $clientip);

        if (strstr($_SERVER['REQUEST_URI'], '?')) {
            $arr = explode('?', $_SERVER['REQUEST_URI']);
            $path = $arr[0];
            unset($arr);
        } else {
            $path = $_SERVER['REQUEST_URI'];
        }
        $G['path'] = $path;
        
        \Local\Log::pushNotice('path', $path);
        \Local\Log::pushNotice('host', $_SERVER['HTTP_HOST']);
        
        Registry::set('G', $G);
        
    }
    
    
    /**
     * @desc 路由处理
     * @return unknown
     */
    public function _init_router(){
        $R = new \vgace\core\Router();
        
        $control_name = $R->getController();
        $func_name    = $R->getFunc();
        
        if(!file_exists(CONT_PATH.$control_name.EXT)){
            echo '文件不存在'.CONT_PATH.$control_name.EXT;
            exit;
        }else{
            include_once(CONT_PATH.$control_name.EXT);
        }
        
        $f_name    = ucfirst($control_name).'Controller';
        $func_name = $func_name.'Action';
        return (new $f_name())->$func_name();
    }
    
    /**
     * @desc Composer
     * @return unknown
     */
    public function _init_ComposerAutoload(){
        $autoload = DOC_ROOT . 'vendor/autoload.php';
        if (file_exists($autoload)) {
            return include_once($autoload);
        }
    }
    
    private static function _xss_check() {
        static $check = array('"', '>', '<', '\'', '(', ')', 'CONTENT-TRANSFER-ENCODING');
        
        $temp = $_SERVER['REQUEST_URI'];
        if(!empty($temp)) {
            $temp = strtoupper(urldecode(urldecode($temp)));
            foreach ($check as $str) {
                if(strpos($temp, $str) !== false) {
                    echo '参数中含有非法字符，已经被系统拒绝';
                    exit;
                }
            }
        }
        return true;
    }
    
    public function _init_log(){
        \Local\Log::init([
            'LOG_UI' => [
                'file'  => DOC_ROOT . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'api.' . date('Ymd', time()) . '.log',
                'level' => 0x04
            ],
            'LOG_DAL' => [
                'file'  => DOC_ROOT . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'log.' . date('Ymd', time()) . '.log',
                'level' => 0x07
            ],
            'LOG_DPL' => [
                'file'  => DOC_ROOT . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'db.' . date('Ymd', time()) . '.log',
                'level' => 0x07
            ], // 出问题时，改成 0x07 可查看调试日志 否则使用 0x03 只打印警告和错误
            'TIME_LOG' => [
                'file'  => DOC_ROOT . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'time.' .  date('Ymd', time()) . '.log',
                'level' => 0x07
            ]
        ], 'LOG_UI');
    }
    
    public function __destruct() {
        list($usec, $sec) = explode(" ", microtime());
        $this->endTime = ((float)$usec + (float)$sec);
        
        $timeStamp = floor(($this->endTime - $this->startTime) * 1000);
        
        \Local\Log::pushNotice('totaltime', $timeStamp);
        \Local\Log::pushNotice('finishLog', 'ok');
        \Local\Log::buildNotice();
    }
    
}
