<?php
$host = '127.0.0.1';
$user = 'root';
$password = '123456';

$db = mysql_connect($host,$user,$password) or die('connect db fail');
mysql_select_db('test',$db) or die('select db fail');

mysql_query('set names "utf8"',$db);
?>