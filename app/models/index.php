<?php
use vgace\Basic\ModelBasic;

class IndexModel extends ModelBasic
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function _getUserNum($uid = 0)
    {
        
        return intval($uid % 10);
    }
    
    public function getUserInfo($uid = 0){
        return $this->_table('llp_user_'.$this->_getUserNum($uid))->_where(['uid' => $uid])->_selectOne();
    }
}