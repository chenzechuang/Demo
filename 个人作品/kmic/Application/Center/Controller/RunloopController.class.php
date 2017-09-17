<?php
namespace Center\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
class RunloopController extends Controller {
    public function index(){
        if (isset($_GET['go'])) {
            $hb = new \Think\Wxpay\hongbao\oauth2();
            $hblist = M('deposit_order')->where(array('status'=>0))->field('deposit_order.*,users.openid')->join('users ON users.id = deposit_order.uid')->order('timeline')->find();

            if ($hblist) {
    //            
                $obj = $hb->pay($hblist['openid'],$hblist['money']);
                var_dump($obj);
                if ($obj->return_code=='SUCCESS') {

                    M('deposit_order')->where(array('id'=>$hblist['id']))->save(array('paytimeline'=>time(),'status'=>1));
                    echo 'next';
                }else{
                    M('deposit_order')->where(array('id'=>$hblist['id']))->save(array('status'=>3));
                    echo $obj->return_msg;
                }
            }else{
                echo 'notthing';
            }
            exit();
        }
        
        $this->display();
//        
//        var_dump($obj->return_code);
    }
}