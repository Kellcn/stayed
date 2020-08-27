<?php
use vgace\Basic\controllerBasic;

class IndexController extends controllerBasic
{
    public function __construct()
    {
        parent::__construct();
        $this->indexModel = new IndexModel();
    }
    
    public function indexAction(){

        echo 'Hello world!';
    }
    
    public function testAction(){
        //$G = \vgace\core\Registry::get('G');
        //print_r($G);
        $user = $this->indexModel->getUserInfo(125163);
        //\Local\Log::pushNotice('user', json_encode($user, JSON_UNESCAPED_UNICODE));
        //print_r($user);
    }
    
    
    
    public function aaAction(){
        for ($pi = 0; $pi < 500; $pi++){
            $this->curl_get("http://localhost/stayed/index/test");
        }
    }
    
    
    
    function curl_get($url){
        
        $header = array(
            'Accept: application/json',
        );
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 超时设置,以秒为单位
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        
        // 设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $data = curl_exec($curl);
        
        // 显示错误信息
        if (curl_error($curl)) {
            print "Error: " . curl_error($curl);
        } else {
            // 打印返回的内容
            //var_dump($data);
            curl_close($curl);
        }
    }
    
}