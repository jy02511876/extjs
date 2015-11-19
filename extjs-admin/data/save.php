<?php
require_once 'config.php';
require_once 'logger/Logger.php';
$path = realpath(dirname(__FILE__)).'/runtime';
Logger::log(json_encode($_REQUEST),$path);

$id = intval($_REQUEST['id']);
$name = trim($_REQUEST['name']);
$price = floatval($_REQUEST['price']);
$lastChange = $_REQUEST['lastChange'];
if($id == 0){
	$sql = 'insert into company(name,price,last_change) values("'.$name.'",'.$price.',"'.$lastChange.'");';
}else{
	$sql = 'update company set name="'.$name.'",price="'.$price.'",last_change="'.$lastChange.'" where id='.$id;
}
$flag = mysql_query($sql,$db);
if($flag){
	$return = array(
		'success'	=>	true,
		'msg'		=>	'操作成功',
	);
}else{
	$return = array(
		'success'	=>	false,
		'msg'		=>	'操作失败',
	);
}
echo json_encode($return);