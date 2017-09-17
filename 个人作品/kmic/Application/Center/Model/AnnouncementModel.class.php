<?php

namespace Center\Model;

use Think\Model;

class AnnouncementModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getData(){
        $data = $this->field('kwx_users.name,kwx_users.level,kwx_users.id as uid,kwx_announcement.id,kwx_announcement.title,kwx_announcement.original_desc,kwx_announcement.create_time')
                ->join('left join kwx_users on kwx_announcement.signup = kwx_users.id')
                ->order('kwx_announcement.id desc')
                ->select();
        return $data;
    }
    
    public function DeleteData($id){
        $data = $this->where(array('id'=>$id))->delete();
        return $data;
    }     
    
    public function countData($where){
        $data = $this->where($where)->count();
       // $data = $this->getLastSql();
        return $data;
    }
}
