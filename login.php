<link rel="stylesheet" href="./css/bootstrap.min.css">
<script src="./js/jquery.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src='./js/bootbox.min.js'></script> 
<?php
    session_start();

    if (isset($_POST['username'])) {
        include_once "dbConn.php";
        $username = $_POST['username'];
        $password = $_POST['password'];
        $passEnc = md5($password);

        $sql = "SELECT * FROM user INNER JOIN depart ON user.id_depart = depart.id_depart
	WHERE username = '$username' AND password = '$passEnc'";
        $result = mysqli_query($dbconn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $_SESSION['s_id'] = $row['id'];
            $_SESSION['s_username'] = $row['username'];
            $_SESSION['s_user'] = $row['firstname'] . " " . $row['lastname'];
            $_SESSION['s_dpid'] = $row['id_depart'];
            $_SESSION['s_depart'] = $row['depart'];
            $_SESSION['s_pack'] = $row['pack'];
            $_SESSION['s_rec'] = $row['rec'];
            $_SESSION['s_keep'] = $row['keep'];
            $_SESSION['s_tran'] = $row['tran'];
            $_SESSION['s_data'] = $row['data'];
            $_SESSION['s_sys'] = $row['sys'];

            $_dpid = $row['id_depart'];
            $rst = mysqli_query($dbconn, "SELECT MIN(shelf_id) AS sh_id FROM shelf WHERE (depart_id ='$_dpid' AND default_shelf = 1 )") or die(mysqli_error($dbconn));
            $rw = mysqli_fetch_array($rst);
            $_SESSION['s_dfid'] = $rw['sh_id'];

            $rsInsert = mysqli_query($dbconn, "INSERT INTO u_log SET id_user='" . $_SESSION['s_id'] . "',session='Login',desc_log='เข้าระบบ'") or die(mysqli_error($dbconn));

            //header("Location: intro");
            //echo "<meta http-equiv='refresh' content='0 ;url=intro'>";
            echo("<script> window.location.href = 'intro';</script>");
            mysqli_close($dbconn);
        } else {
            echo ("<script>");
            echo ("$(function(){bootbox.alert(' คุณระบุ Username หรือ Password ไม่ถูกต้อง !', function(){window.history.back();});})");
            echo ("</script>");
        }
    }

    ?>