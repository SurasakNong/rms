<?php
  // If the user is logged in, delete the session vars to log them out
  session_start();
  include_once "dbConn.php";
  $_id = (isset($_GET['id'])) ? $_GET['id'] : '';
  $_ss = (isset($_GET['ss'])) ? $_GET['ss'] : '';
  $_de = (isset($_GET['de'])) ? $_GET['de'] : '';
  if($_id != ''){
      $rs_log = mysqli_query($dbconn, "INSERT INTO u_log SET id_user='$_id',session='$_ss',desc_log='$_de'") or die(mysqli_error($dbconn . " Q=" . $rs_log));          
  }
?>
