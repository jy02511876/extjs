<?php
//var_dump($_POST);
sleep(3);
$rnd = rand(0, 1);
if($rnd == 1){
	$data = array(
			'success'	=>	true,
	);
}else{
	$data = array(
		'success'	=>	false,
		'msg'		=>	'用户名或密码错误',
	);
}
echo json_encode($data);