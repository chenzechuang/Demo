<?php
namespace Center\Controller;
use Think\Controller;

class UserController extends AdminController {

    public function Userorder(){
       if(IS_AJAX){
            $userData =  new \Center\Model\UsersModel;
            $data = $userData->Userorder();   
            echo json_encode($data);
            exit();
       }
       $this->display();
    }
    
    public function getRobot() {
        $data = array();
        $userModel = new \Center\Model\UsersModel();
        $data = $userModel->userSelect(array('robot'=>'1'));
        echo json_encode($data);
        exit();
    }
     
  
}