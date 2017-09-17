<?php

namespace Center\Model;

use Think\Model;

class AritcleModel extends Model {

    /**
     * 
     * @return type
     */
    public function sort($id) {
        $MaxSort = $this->max('sort');
        $MaxSort = $MaxSort + 1;
        $data = $this->where(array('id' => $id))->save(array('sort' => $MaxSort));
        return $data;
    }

    public function updataData($id, $arry) {
        $data = $this->where(array('id' => $id))->save($arry);
        return $data;
    }

    public function acticleData($id) {
        if ($id) {
            $data = $this->where(array('id' => $id))->find();
        } else {
            $data = $this->select();
        }
        return $data;
    }

    public function getActicleData($array) {
        if($array){
            $where['ywx_aritcle.type'] = $array['type'];
        }
        $data = $this->field('ywx_aritcle.*,ywx_aritcle_channel.title,IFNULL(ywx_users.name,ywx_users.name_user) as name,IFNULL(ywx_users.headimgurl,ywx_users.headimgurl_user) as headimgurl')
                ->join('ywx_aritcle_channel ON ywx_aritcle_channel.id = ywx_aritcle.channel')
                ->join('ywx_users ON ywx_users.id= ywx_aritcle.uid')
                ->where($where)
                ->order('ywx_aritcle.log_time desc')->select();

        foreach ($data as $key => $value) {
            $data[$key]["artic_create_time"] = strtotime($value["log_time"]);
            $data[$key]["name"] = base64_decode($value["name"]);
            $data[$key]["content"] = base64_decode($value["content"]);
            $data[$key]["pics"] = htmlspecialchars_decode($data[$key]["pics"]);
            
        }
        return $data;
    }

    public function audioT($id) {
        $returnData = array();
        $transformationCount = $this->where(array('transformation' => '2'))->count();
        if($transformationCount > 0){
            return json_encode(array('error'=>'2','msg'=>'在转码中'));
        }
        
        if($id != "-1"){
          $data = $this->where(array('id' => $id))->find();  
        } else {
          $data = $this->where(array('transformation'=>'0','type'=>'1'))->order('id desc')->find(); 
          $redata["id"] = $data["id"];
        }

        if($data["type"] != "1"){
            return json_encode(array('error'=>'4','msg'=>'这不是音频动态！'));
        }
        
        $data['pics']  = htmlspecialchars_decode($data['pics']);
        $audio = json_decode($data['pics'],TRUE);
        
        if(count($audio)==0){
            return json_encode(array('error'=>'3','msg'=>'没音频的'));
        }

        $url = "http://tym.yuemai168.com/?m=api&c=convert";
        $callback = "http://".$_SERVER["HTTP_HOST"]."/index.php?m=Center&c=Admin&a=audioCallback&api=1";
    
        $post_data = array("aid" => $data["id"], "url" => urlencode($audio[0]['pic']),"callback"=>urlencode($callback));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $redata['data'] = json_decode($output,TRUE);
        return json_encode($redata);

    }
    
    public function praise($where,$row) {
       return M('aritcle')->where($where)->setInc('praise',$row);       
    }
    
    public function listen($where,$row) {
       return M('aritcle')->where($where)->setInc('listen',$row); 
    }    
}
