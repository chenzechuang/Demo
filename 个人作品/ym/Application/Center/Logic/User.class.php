<?php
namespace Center\Logic;
use Think\Model;
 
class User{

    public function userListData($param){
       $userModel = new \Center\Model\UsersModel();
       $UserDataList = $userModel->userSlect($param);
       foreach ($UserDataList as $key => $value) {
           $UserDataList[$key]['name'] = base64_decode($value['name']);
           $UserDataList[$key]['introduction'] = base64_decode($value['introduction']);
           $UserDataList[$key]["pics"] = htmlspecialchars_decode($value["pics"]);
       } 
       return $UserDataList;
    }
     
    public function changeMz($userid,$param) {
        $where["id"] = $userid;
        $where["password"] =  sha1(md5($param["pwd"]));
        
        $adminModel = new \Center\Model\AdminModel();
        $adminData = $adminModel->findData($where);

        if(count($adminData)>0){
            $userWhere["id"] = $param["userid"];
            $upData["role_id"] = $param["role_id"];
            $upData["recommend"] = $param["rmz_id"];
            $userModel = new \Center\Model\UsersModel();
            $upUser = $userModel->userUpdata($userWhere, $upData);

            $error['state'] = "0";   
        }else {
            $error['state'] = "1";
            $error['data'] = "你没管理权限！";
        }
        return json_encode($error);
    }
}