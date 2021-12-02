<?php
    session_start();
    $_ssid = (isset($_SESSION['s_id']))?$_SESSION['s_id']:"";
    if($_ssid != ""){
        //header("Location: intro.php");
        echo("<script> window.location.href = 'intro';</script>");
        //echo "<meta http-equiv='refresh' content='0 ;url=intro>";
    } else{
        //header("Location: login_fm.html");
        echo("<script> window.location.href = 'login_fm.html';</script>");
        //echo "<meta http-equiv='refresh' content='0 ;url=login_fm.html'>";
    }

?>