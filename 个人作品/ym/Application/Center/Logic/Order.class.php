<?php
namespace Center\Logic;
use Think\Model;

class Order{

    public function orderList($array){

      // $where['center_orders.paid'] = '1';
       
       if($array['userid']){
           $where["ywx_users.id"] = $array['userid'];
       }
 
       if($array['orderid']){
           $where["center_orders.orderid"] = $array['orderid'];
       }

       if($array['start'] && $array['end']){
           $start = strtotime($array['start']);
           $end = strtotime($array['end']);
           $where["center_orders.paidtime"] = array('between',"$start,$end");
       }
       
       $data = M('orders','center_')
               ->field('ywx_users.id as uid,center_orders.id,ywx_items.name as itemsName,IFNULL(ywx_users.name,ywx_users.name_user) as name,center_orders.orderid as orderid,center_orders.price,center_orders.paidtime,center_orders.type,center_orders.transaction_id,center_orders.return_code,center_orders.addtime,center_orders.paid,center_orders.desc')
               ->join('ywx_users ON ywx_users.id = center_orders.uid')
               ->join('ywx_items ON ywx_items.id = center_orders.type')
               ->where($where)
               ->select();
       
       foreach ($data as $key => $value) {
           $data[$key]["name"] = base64_decode($value["name"]);
           $data[$key]["addtime"] = date("Y-m-d H:i:s",$value["addtime"]);
           $data[$key]["paidtime"] = date("Y-m-d H:i:s",$value["paidtime"]);
       }
       return $data;
    }
    
    public function findData($param) {
        return  M('orders','center_')->where($param)->find();
    }
 
    public function updataData($where,$param) {
        return  M('orders','center_')->where($where)->save($param);
    }    
}