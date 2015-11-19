<?php
require_once 'config.php';

$page = intval(@$_GET['page'])-1;
if($page < 0) $page = 0;
$limit = 20;
$offset = $page * $limit;

$name = trim(@$_GET['name']);
$price = trim(@$_GET['price']);

$cond = ' where 1=1';
if($name != ''){
	$cond .= ' and name like "%'.$name.'%"';
}
if($price != ''){
	$cond .= ' and price='.$price;
}
$data = array();

$sql = 'select count(*) as c from company'.$cond;
$result = mysql_query($sql,$db);
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$count = $row['c'];

$sql = 'select * from company '.$cond.' order by id desc limit '.$offset.','.$limit.';';
$result = mysql_query($sql,$db);
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$data[] = array(
			'id'	=>	$row['id'],
			'name'	=>	$row['name'],
			'price'	=>	$row['price'],
			'lastChange'	=>	$row['last_change'],
		);
}

$data = array(
	'success'	=>	true,
	'recordCount'	=>	$count,
	'data'		=>	$data
	);

echo json_encode($data);