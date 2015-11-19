<?php
require_once 'config.php';

$id = intval($_POST['id']);

$sql = 'select * from company where id='.$id;

$result = mysql_query($sql,$db);
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$data = array(
	'success'	=>	true,
	'data'		=>	$row,
);
echo json_encode($data);