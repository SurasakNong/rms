<?php
$dbconn = mysqli_connect('localhost','root','nong420631','rm_net',3392) or die('ไม่สามารถเชื่อมต่อฐานข้อมูลได้ :'.mysqli_error($dbconn));
mysqli_set_charset($dbconn,'utf8');
?>
