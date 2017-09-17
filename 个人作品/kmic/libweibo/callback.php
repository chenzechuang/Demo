<?php
session_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
//        print_r($token);
        
        
        $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
        $ms  = $c->home_timeline(); // done
        $uid_get = $c->get_uid();
        $uid = $uid_get['uid'];
        $user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
//        print_r($user_message);
        
        $mArr = array_merge($token, $user_message); 
//        print_r($mArr);
//        $mArr = json_encode($mArr);
        //
        
        $url = "http://wechatcenter.witheasy.com/?m=Center&a=saveUserInfoFromWeibo";
        $ch = curl_init ();
//        $data['data'] = $mArr;
                // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $mArr );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        var_dump($return);
?>
授权完成,<a href="weibolist.php">进入你的微博列表页面</a><br />
<?php
} else {
?>
授权失败。
<?php
}
?>
