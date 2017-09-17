<?php
namespace Center\Model;

use Think\Model;

class MzCommentModel extends Model
{
    /**
     * 
     * @return type
     */
    public function selectALL(){
        $data = $this->field(''
                . 'IFNULL(ywx_users.name_user,ywx_users.name) as name,'
                . 'ywx_mz_comment.uid,'
                . 'ywx_mz_comment.commented_uid,'
                . 'ywx_mz_comment.comment_content,'
                . 'ywx_mz_comment.stars,'
                . 'ywx_mz_comment.fluency,'
                . 'ywx_mz_comment.profession,'
                . 'ywx_mz_comment.next_comm,'
                . 'ywx_mz_comment.comment_time')
                ->join('ywx_users ON ywx_users.id = ywx_mz_comment.commented_uid')->select();
        return $data;
    }
    
    public function deleteData($param) {
        return $this->where($param)->delete();
    }

}
