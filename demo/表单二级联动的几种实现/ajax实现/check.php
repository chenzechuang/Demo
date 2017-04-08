<?php
  // 用于处理客户端请求二级联动的数据
  // 1. 接收客户端发送的省份信息
  $province = $_POST['province'];
  // 2. 判断当前的省份信息,提供不同的城市信息
  switch ($province){
    case '山东省':
    echo '青岛市,济南市,威海市,日照市,德州市';
    break;
    case '辽宁省':
    echo '沈阳市,大连市,铁岭市,丹东市,锦州市';
    break;
    case '吉林省':
    echo '长春市,松原市,吉林市,通化市,四平市';
    break;
    default : 
    echo '请选择';
  }
  
 // 服务器端响应的是字符串
 
?> 