<?php 
$link=mysql_connect('localhost','root',''); 
if(!$link) echo "失败!"; 
else echo "成功!"; 
mysql_close(); 
?>