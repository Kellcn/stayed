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
        $user = $this->indexModel->getUserInfo(125163);
        print_r($user);
    }
    
}