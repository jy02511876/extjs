<?php
require_once 'config.php';

$id = intval($_POST['id']);
$sql = 'delete from company where id='.$id;
$result = mysql_query($sql,$db);

$data = array(
	'success'	=>	true,
);
echo json_encode($data);