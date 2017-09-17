<?php

namespace Center\Model;

use Think\Model;

class Recharge {

    /**
     * 
     * @return type
     */
    public function Userorder(){

            $userData =  new \Center\Model\UsersModel;
            $data = $userData->Userorder();   
            return $data;

    }

    public function Config($where){
       
           $itemsData = M('items')->where($where)->select();
           foreach ($itemsData as $key=>$value) {
               $itemsData[$key]["price"] = json_decode($value["price"]);
           }
           return $itemsData;
    

    }
}
