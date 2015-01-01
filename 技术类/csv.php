<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="user.csv"');
header('Cache-Control: max-age=0');
$conn = mysql_connect("localhost","root","");
!$conn && die('Could not connect: ' . mysql_error());
mysql_select_db("test",$conn);
/*
$sql = "insert into abc(username,password,datetime) values";
for($i=1;$i<=600000;$i++){
	$username = "user".$i;
	$password = md5($username);
	$datetime = time();
	$sql .= "('$username','$password','$datetime'),";
	if($i%10000==0){
		$sql = rtrim($sql,',');
		mysql_query($sql,$conn);
		$sql = "insert into abc(username,password,datetime) values";
	}
}
*/
$query = mysql_query("select * from abc",$conn);
//$fp = fopen('L:/test_abc.csv', 'a'); 
$fp = fopen('php://output', 'a');
$head = array('id', '账号', '密码', '时间');
foreach ($head as $i => $v) {
	$head[$i] = iconv('utf-8', 'gbk', $v);
}
fputcsv($fp, $head);
// 计数器
$cnt = 0;
// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
$limit = 100000;

// 逐行取出数据，不浪费内存
while ($row = mysql_fetch_array($query, MYSQL_NUM)) {
	$row['3']=date('Y-m-d H:i:s',$row['3']);
	$cnt ++;
	if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
		ob_flush();
		flush();
		$cnt = 0;
	}

	foreach ($row as $i => $v) {
		$row[$i] = iconv('utf-8', 'gbk', $v);
	}
	fputcsv($fp, $row);
}
fclose($fp);