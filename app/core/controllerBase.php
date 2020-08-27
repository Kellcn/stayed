<?php
namespace vgace\Basic;

use Local\Func;

class controllerBasic
{
	public function __construct(){
	    
	}
	
	protected function get($key, $filter = true)
	{
	    if ($filter) {
	        return Func::filterStr($_GET[$key] ?? null);
	    } else {
	        return $_GET[$key] ?? null;
	    }
	}
	
	protected function post($key, $filter = true)
	{
	    if ($filter) {
	        return Func::filterStr($_POST[$key] ?? null);
	    } else {
	        return $_POST[$key] ?? null;
	    }
	}
	
	protected function getPost($key, $filter = true)
	{
	    $data = $this->get($key, $filter);
	    if ($data !== NULL || $data) {
	        return $data;
	    }
	    
	    return $this->post($key, $filter);
	}
	
	protected function postGet($key, $filter = true)
	{
	    $data = $this->post($key, $filter);
	    if ($data !== NULL || $data) {
	        return $data;
	    }
	    
	    return $this->get($key, $filter);
	}
	
	
	
}
