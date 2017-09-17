<?php

namespace Center\Model;

use Think\Model;

class DbApp {

    protected $connection = array(
        'db_type' => 'mysql',
        'db_user' => 'jeiry',
        'db_pwd' => '1qazXSW@3edc',
        'db_host' => '120.76.221.4',
        'db_port' => '3306',
        'db_name' => 'db_test',
    );

    public function UsersCountAPP() {
        return M('user_info', 't_', $this->connection)->count();
    }

    public function UsersAPP($start, $end) {
        $where['t_user_info.create_time'] = array('between', array($start, $end));
        $userData = M('user_info', 't_', $this->connection)->field(''
                                . 't_user_login.id,'
                                . 't_user_login.id_source,'
                                . 't_user_info.nick as nick,'
                                . 'from_base64(t_user_info.nick) as dc_nick,'
                                . 't_user_info.uid,'
                                . 't_user_info.sex,'
                                . 't_user_info.city,'
                                . 't_user_info.photo_url,'
                                . 't_user_info.role_id,'
                                . 't_user_info.phone_num,'
                                . 't_user_info.wx_id,'
                                . 't_user_info.introduction,'
                                . 't_user_info.interest_ids,'
                                . 't_user_info.language_ids,'
                                . 't_user_info.voice_ids,'
                                . 't_user_info.pics,'
                                . 't_user_info.voice_url,'
                                . 't_user_info.voice_time,'
                                . 't_user_info.check_status,'
                                . 't_user_info.check_msg,'
                                . 't_user_info.real_name,'
                                . 't_user_info.job,'
                                . 't_user_info.completeness,'
                                . 't_user_info.create_time,'
                                . 't_user_info.can_video,'
                                . 't_user_info.can_voice,'
                                . 't_user_info.video_fee,'
                                . 't_user_info.voice_fee,'
                                . 't_user_info.signature,'
                                . 't_user_info.locked')
                        ->join("t_user_login on t_user_login.uid = t_user_info.uid")
                        ->where($where)->select();

        foreach ($userData as $key => $value) {
            $userData[$key]["follow"] = M('attention', 't_', $this->connection)->where(array('uid' => $value['uid'],'initiative'=>'1'))->count();
            $userData[$key]["passive_count"] = M('attention', 't_', $this->connection)->where(array('uid' => $value['uid'],'passive'=>'1'))->count();
            $userData[$key]["article_row"] = M('dynamic', 't_', $this->connection)->where(array('uid' => $value['uid'],'deleted'=>'0'))->count();
        }
        return $userData;
    }

    public function Channel($action) {
        $channelData = M('dynamic_topic', 't_', $this->connection)->select();
        $redis = new \Redis();
        $redis->connect('120.76.221.4', 6611);
        //连接本地的 Redis 服务
        foreach ($channelData as $key => $value) {
            $channelData[$key]["aritcle_count"] = $redis->llen('dynamic_topic_new_' . $value["topic_id"]);
        }
        
        if($action == "tbData"){
            foreach ($channelData as $key => $value) {
                $addData[$key]["id"] = $value["topic_id"];
                $addData[$key]["title"] = $value["name"];
                $addData[$key]["img_url"] = $value["pic"];
                $addData[$key]["article_row"] = $value["aritcle_count"];
                $addData[$key]["mp3"] = $value["mp3"];
                $addData[$key]["type"] = "1";
            }
            if(count($addData)>0){
                M('aritcle_channel')->AddALL($addData);
            }
            exit();
        }
        return $channelData;
    }
    
    public function ArticleLiset($param,$start,$end) {
        $start = strtotime($start);
        $end = strtotime($end);
        
        $redis = new \Redis();
        $redis->connect('120.76.221.4', 6611);
        
        
            $where['create_time'] = array('between', array($start, $end));
            $articeData = M('dynamic','t_', $this->connection)
                ->field('cover,'
                        . 'deleted,'
                        . 'listen_count,'
                        . 'pic_count,'
                        . 'topic_id,'
                        . 'type,'
                        . 'uid,'
                        . 'from_base64(content) as dc_content,'
                        . 'content,create_time,'
                        . 'FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as dc_create_time'
                        )
                    
                ->where($where)->select();
            foreach ($articeData as $key => $value) {
               $articeData[$key]["praise"] = M('dynamic_praise','t_', $this->connection)->where(array('uid'=>$value["uid"],'create_time'=>$value["create_time"]))->count();
               $articeData[$key]["comment"] = M('dynamic_comment','t_', $this->connection)->where(array('uid'=>$value["uid"],'create_time'=>$value["create_time"]))->count();
               $articeData[$key]["listen"] = $redis->hget("dynamic_listen_count_".intval($value["uid"]/100), sprintf("%02d_%d", $value["uid"]%100, $value["create_time"]));
              
               $addData[$key]["praise"] = $articeData[$key]["praise"];
               $addData[$key]["comments"] = $articeData[$key]["comment"];
               $addData[$key]["listen"] = $articeData[$key]["listen"];
               $addData[$key]["log_time"] = $value["dc_create_time"];
               
               $addData[$key]["uid"] =$value["uid"];
               $addData[$key]["type"] =$value["type"];
               $addData[$key]["channel"] =$value["topic_id"];
               $addData[$key]["content"] = $value["content"];
               $addData[$key]["cover"] = $value["cover"];
               $addData[$key]["content"] = $value["content"];
               
               $pic = M("dynamic_pic","t_", $this->connection)->field('pic,voice_time')->where(array('uid'=>$value["uid"],'create_time'=>$value["create_time"]))->select();
               $addData[$key]["pics"] = json_encode($pic);
               
            }
            
        if($param == "tbData"){
               M('aritcle')->AddALL($addData);
        }
        
        return $articeData;
    }
    
    public function Fllow(){
       $follwData = M('attention','t_', $this->connection)->select();
       foreach ($follwData as $key =>$value) {
          if($value["initiative"]=="1"){ //关注人
              $add[$key]["uid"] = $value["uid"];
              $add[$key]["mcs_id"] = $value["f_uid"];
              $add[$key]["log_time"] = date("Y-m-d H:i:s",$value["update_time"]);
          }else{
              $add[$key]["mcs_id"] = $value["uid"];
              $add[$key]["uid"] = $value["f_uid"];
              $add[$key]["log_time"] = date("Y-m-d H:i:s",$value["update_time"]);              
          }
       }
       return M('follow')->addAll($add);
    }
    
    public function message(){
       $commentData = M('dynamic_comment','t_', $this->connection)->select();
     
       foreach ($commentData as $key =>$value) {
           $create_time = date('Y-m-d H:i:s',$value['create_time']);
           $aritcleID = M('aritcle')->where(array('uid'=>$value["uid"],'log_time'=>$create_time))->find();
         
           $addData[$key]['uid']= $value["comment_uid"];
           $addData[$key]['cover_uid']= $value["uid"];
           $addData[$key]['article_id']= $aritcleID["id"]; 
           $addData[$key]['content']= $value["comment_content"]; 
           
           $addData[$key]['audio']= $value["comment_mp3"]; 
           if($value["comment_mp3"]!=""){
               $addData[$key]['type'] = "1";
           }else{
               $addData[$key]['type']= "0";
           }
           $addData[$key]['audio_time']= $value["comment_mp3_time"]; 
           $addData[$key]['log_time']= date('Y-m-d H:i:s',$value["comment_time"]); 

       }
;
        return M('message')->addAll($addData);
    }    
    
    public function praise(){
       $praiseData = M('dynamic_praise','t_', $this->connection)->select();
     
       foreach ($praiseData as $key =>$value) {
           $create_time = date('Y-m-d H:i:s',$value['create_time']);
           $aritcleID = M('aritcle')->where(array('uid'=>$value["uid"],'log_time'=>$create_time))->find();
         
           $addData[$key]['uid']= $value["praise_uid"];
           $addData[$key]['cover_uid']= $value["uid"];
           $addData[$key]['aritcle_id']= $aritcleID["id"]; 

           $addData[$key]['log_time']= date('Y-m-d H:i:s',$value["praise_time"]); 

       }
      // print_r($addData);
        return M('praise')->addAll($addData);
    }
    
    public function userMb() {
        $redis = new \Redis();
        $redis->connect('120.76.221.4', 6611);
        $user = M('users')->select();
        $data = array();
        foreach ($user as $key => $value) {
           $data[$key]["uid"] = $value['id'];
           $data[$key]["deposit"]= intval($redis->hget("user_items_".intval($value['id']/100), sprintf("%02d", $value['id']%100)."_1"));
           $data[$key]["coin"] = intval($redis->hget("user_coin_".intval($value['id']/100), sprintf("%02d", $value['id']%100)));
        }
        return M('user_bank')->AddAll($data);
    }
    
    public function bill() {
       $gifs = M('gifs_sum','t_', $this->connection)->field('uid,create_time,item_id as type,item_count as deposit,source')->select(); // 麦币
       M('user_bill')->addAll($gifs);
        
       $coin = M('coin_sum','t_', $this->connection)->field('uid,create_time,trans_coin as coin,source')->select();
       M('user_bill')->addAll($coin);
        
    }
    
    public function mzcomm() {
       $data = M('comm_comment','t_', $this->connection)->field('uid,comment_uid as commented_uid,comment_content,stars,fluency,profession,next_comm,comm_time,create_time as comment_time')->select();
       M('mz_comment')->addAll($data);
    }

}
