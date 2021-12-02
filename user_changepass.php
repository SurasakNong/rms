<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
	<title>Password Change</title>
	<link rel="shortcut icon" href="image/stockicon128.ico">
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<script src="./js/jquery.min.js"></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src='./js/bootbox.min.js'></script>

	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<link rel="stylesheet" href="./css/all.min.css"> <!-- font awesome -->
	<link rel="stylesheet" href="./css/menunav.css">

	<?php
	error_reporting(E_ALL ^ E_NOTICE);
	include 'Menu_admin.php';
	include_once 'dbConn.php';
	$submit = (isset($_GET['submit'])) ? $_GET['submit'] : '';

	$user_Acc = $_SESSION['s_username']; // ผู้ใช้งานระบบ
	$user_nam = $_SESSION['s_user'];
	$_oldpass = (isset($_POST['oldpass'])) ? $_POST['oldpass'] : '';
	$_newpass = (isset($_POST['newpass'])) ? $_POST['newpass'] : '';
	$_newpass2 = (isset($_POST['newpass2'])) ? $_POST['newpass2'] : '';

	$passEnc = md5($_oldpass);
	$passnew = md5($_newpass);

	if ($submit == "OK") { //================== UPDATE ====================

		$sqlck = "SELECT * FROM user WHERE(password = '$passEnc')";
		$resultCk = mysqli_query($dbconn, $sqlck) or die(mysqli_error($dbconn . " Q=" . $sqlck));
		$numck = mysqli_num_rows($resultCk);
		if ($numck >= 1) {
			$sqlUp = "UPDATE user SET password='$passnew' WHERE(username = '$user_Acc')";
			$rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));

			//===== LOG ==========
			$descLog = "DataUser" . $user_Acc . " Change Password";
			saveLog($_SESSION['s_id'], '_edit', $descLog);
			echo ("<script>window.location='intro';</script>");
			mysqli_close($dbconn);
		} else {
			echo ("<script>");
			echo ("$(function(){bootbox.alert(' UserName [" . $user_Acc . "] รหัสเดิมไม่ถูกต้อง !', function(){window.history.back();});})");
			echo ("</script>");
		}
	}
	?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-primary text-white" align="center"><?php echo $user_nam; ?> <i class="fas fa-lock fa-lg" aria-hidden="true"></i> เปลี่ยนรหัสผ่าน</span>
			</div>
			<!-- ================================== Show Form ========================================= -->
			<div class="col-md-12">
				<form name="frmchange" id="frmchange" method="post" action="user_changepass?submit=OK">
					<div class="form-group row">

						<div class="input-group mb-2 col-md-12">
							<div class="input-group-prepend">
								<label class="input-group-text " for="oldpass">รหัส เดิม</label>
							</div>
							<input name="oldpass" type="password" id="oldpass" class="form-control" required="required">
						</div>

					</div>
					<div class="form-group row">
						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text " for="newpass" style="background-color: aquamarine; width:120px;">รหัสใหม่</label>
							</div>
							<input name="newpass" type="password" id="newpass" class="form-control" required="required">
						</div>

						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text " for="newpass2" style="background-color: aquamarine; width:120px;">รหัสใหม่อีกครั้ง</label>
							</div>
							<input name="newpass2" type="password" id="newpass2" class="form-control" required="required">
						</div>

					</div>


					<div class='form-group'>
						<div class='col-md-12' align="center">
							<button type='button' onClick="submitAdd()" class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
							<button type='button' class='btn btn-danger' onClick="document.location.href='intro'">ยกเลิก</button>
						</div>
					</div>

				</form>
			</div>

		</div>
	</div>


	<script>
		function submitAdd() {
			var s1 = document.getElementsByName('oldpass');
			s1 = s1.item(0).value;
			var s2 = document.getElementsByName('newpass');
			s2 = s2.item(0).value;
			var s3 = document.getElementsByName('newpass2');
			s3 = s3.item(0).value;
			if (s1 == "") {
				$(function() {
					bootbox.alert("กรุณาระบุรหัสเดิม !", function() {});
				})
			} else if (s2 != s3) {
				$(function() {
					bootbox.alert("กรุณาระบุรหัสใหม่ให้ตรงกันทั้งสองครั้ง !", function() {});
				})

			} else {
				$('#frmchange').submit();
			}

		}
	</script>

</body>

</html>
