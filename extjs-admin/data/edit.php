<?php
require_once 'config.php';

$id = intval($_REQUEST['id']);
$name = $_REQUEST['name'];
$price = $_REQUEST['price'];
$lastChange = $_REQUEST['lastChange'];

$sql = 'update company set name="'.$name.'",price="'.$price.'",last_change="'.$lastChange.'" where id='.$id;

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