<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    Public function _initialize()  
    {
        
    }
    
   
    public function index(){
        $this->display();
    }
}