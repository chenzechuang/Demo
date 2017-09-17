<?php

namespace Center\Model;

use Think\Model;

class UserDetailModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getUserDataInspect($uid){
         $data = $this->field('city,stature,price,organization,school,honour,timeline')->where(array('uid'=>$uid))->order('id desc')->find();
     
        if($data['timeline']==""){  //上线时间，不加入判断数据中
           $data['timeline'] = "1"; 
        }
        
        foreach ($data as $key => $value) {
            if($data[$key] == "" or  $value=="[]"){
                $result['state'] = "0";
                $result['userData']=$key;
                break;
            }else{
                $result['state'] = "1";
                $result['userData'] = $data;
            }
        }
        return $result;
    }
    
     public function updataTimeLine($uid,$TimeLine){ 
         $time = time() - $TimeLine;
         if($time > 60*60*24){
             $result = $this->where(array('uid'=>$uid))->save(array('timeline'=>time(),'isshow'=>'0'));  
         }else{
             $result = '0';
         }
         return $result;
     }
     
     public function updataData($uid,$arry){ 
         $result = $this->where(array('uid'=>$uid))->save($arry);  
         return $result;
     }
    

    
}
