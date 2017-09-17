<?php
namespace Center\Model;

use Think\Model;

class MessageModel extends Model
{
    /**
     * 
     * @return type
     */
    public function selectALL(){
        $data = $this->field(''
                . 'IFNULL(ywx_users.name_user,ywx_users.name) as name'
                . ',ywx_aritcle.content as a_content,'
                . 'ywx_message.id,'
                . 'ywx_message.uid,'
                . 'ywx_message.type,'
                . 'ywx_message.content,'
                . 'ywx_message.cover_uid,'
                . 'ywx_message.audio,'
                . 'ywx_message.log_time')
                ->join('ywx_users ON ywx_users.id = ywx_message.uid')
                ->join('ywx_aritcle ON ywx_aritcle.id = ywx_message.article_id')
                ->select();
        return $data;
    }
    
    public function deleteData($param) {
        return $this->where($param)->delete();
    }

}
