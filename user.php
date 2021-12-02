<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
	<title>User Management</title>
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
	$show = (isset($_GET['show'])) ? $_GET['show'] : '';
	$submit = (isset($_GET['submit'])) ? $_GET['submit'] : '';
	$Sel_id = (isset($_GET['selid'])) ? $_GET['selid'] : '';
	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
	$search = (isset($_GET['Search'])) ? $_GET['Search'] : ((isset($_POST['Search'])) ? $_POST['Search'] : '');

	$depart = (isset($_POST['depart'])) ? $_POST['depart'] : '';
	$dpto = 2; //===== รหัสสต๊อกวัตถุดิบ
	$username = (isset($_POST['username'])) ? $_POST['username'] : '';
	$firstname = (isset($_POST['firstname'])) ? $_POST['firstname'] : '';
	$lastname = (isset($_POST['lastname'])) ? $_POST['lastname'] : '';
	$pass = (isset($_POST['pass'])) ? $_POST['pass'] : '';
	$pack = (isset($_POST['pack'])) ? $_POST['pack'] : '0';
	$rec = (isset($_POST['rec'])) ? $_POST['rec'] : '0';
	$keep = (isset($_POST['keep'])) ? $_POST['keep'] : '0';
	$tran = (isset($_POST['tran'])) ? $_POST['tran'] : '0';
	$_data = (isset($_POST['_data'])) ? $_POST['_data'] : '0';
	$sys = (isset($_POST['sys'])) ? $_POST['sys'] : '0';


	if ($submit == "OK") { //================== INSERT OR UPDATE ====================
		if ($Sel_id == "") { //===== INSERT ==========
			$sqlck = "SELECT * FROM user WHERE(username = '$username')";
			$resultCk = mysqli_query($dbconn, $sqlck) or die(mysqli_error($dbconn . " Q=" . $sqlck));
			$numck = mysqli_num_rows($resultCk);
			if ($numck >= 1) {
				echo ("<script>");
				echo ("$(function(){bootbox.alert(' UserName [" . $username . "] มีอยู่แล้วในทะเบียนผู้ใช้งาน  !', function(){window.history.back();});})");
				echo ("</script>");
			} else {
				$passEnc = md5($pass);
				$strsql = "INSERT INTO user SET username='$username',firstname='$firstname',lastname='$lastname',id_depart='$depart',pack='$pack',rec='$rec',keep='$keep',tran='$tran',data='$_data',sys='$sys',password='$passEnc'";
				$rsInsert = mysqli_query($dbconn, $strsql) or die(mysqli_error($dbconn . " Q=" . $strsql));

				//===== LOG ==========
				$descLog = "DataUser" . $username . "_name" . $firstname . "_dp" . $depart . "_fn:P" . $pack . "_R" . $rec . "_K" . $keep . "_T" . $tran . "_D" . $_data . "_S" . $sys;
				saveLog($_SESSION['s_id'], '_edit', $descLog);
			}
		} else {  		//========= UPDATE ==========
			$sqlUp = "UPDATE user SET firstname='$firstname',lastname='$lastname',id_depart='$depart',pack='$pack',rec='$rec',keep='$keep',tran='$tran',data='$_data',sys='$sys'  WHERE(id = '$Sel_id')";
			$rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));
			//===== LOG ==========
			$descLog = "DataUser" . $Sel_id . "_name" . $firstname . "_dp" . $depart . "_fn:P" . $pack . "_R" . $rec . "_K" . $keep . "_T" . $tran . "_D" . $_data . "_S" . $sys;
			saveLog($_SESSION['s_id'], '_edit', $descLog);
		}
	}else if ($submit == "DEL") { //====== DELETE ==========
		$sqldel = "DELETE FROM user WHERE(id = '$Sel_id')";
		$rsDel = mysqli_query($dbconn, $sqldel) or die(mysqli_error($dbconn . " Q=" . $sqldel));
		//===== LOG ==========
		$descLog = "DataUser" . $Sel_id;
		saveLog($_SESSION['s_id'], '_del', $descLog);

	}else if ($submit == "RES") { //====== RESET Password to 123456 ==========
		$passEnc = md5('123456');
		$sqlres = "UPDATE user SET password='$passEnc' WHERE(id = '$Sel_id')";
		$rsRes = mysqli_query($dbconn, $sqlres) or die(mysqli_error($dbconn . " Q=" . $sqlres));
		//===== LOG ==========
		$descLog = "DataUser" . $Sel_id. " ResetPassword";
		saveLog($_SESSION['s_id'], '_edit', $descLog);
	}

	?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-info text-white" align="center"><i class="fas fa-user fa-lg" aria-hidden="true"></i> จัดการข้อมูลผู้ใช้งาน</span>
			</div>

			<!-- ================================== Show Form ========================================= -->
			<?php
			if ($submit == "" or $show == "OK") { ?>
				<div class="col-md-12">
					<form name="fmSearch" method="post" action="user.php?show=OK" role='Search'>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-12">
								<input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo ($search); ?>' onFocus="this.value ='';">
								<div class="input-group-append">
									<button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php
				$limit = '25';
				$rec_sql = "SELECT username, firstname, lastname, depart.depart, pack, rec, keep, tran, data, sys FROM user INNER JOIN depart ON user.id_depart = depart.id_depart WHERE(CONCAT(username,' ',firstname,' ',lastname,' ',depart.depart,' #',LPAD(pack,1,'0'),LPAD(rec,1,'0'),LPAD(keep,1,'0'),LPAD(tran,1,'0'),LPAD(data,1,'0'),LPAD(sys,1,'0')) like '%$search%')
			ORDER by firstname ";

				$Qtotal = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
				$total_data = mysqli_num_rows($Qtotal);
				$total_page = ceil($total_data / $limit);

				if ($page >= $total_page) $page = $total_page;
				if ($page <= 0 or $page == '') {
					$start = 0;
					$page = 1;
				}

				$start = ($page - 1) * $limit;

				$from = $start + 1;
				$to = $page * $limit;
				if ($to > $total_data) $to = $total_data;
				?>
				<div class="col-md-12">
					<div class='alert alert-info' role='alert' align="center">
						<?php
						echo ($from . " - " . $to);
						echo (" ( จำนวน " . number_format($total_data) . " รายการ ");
						echo (" --- หน้าที่ " . number_format($page) . "/");
						echo (number_format($total_page) . " )");

						?>
					</div>
				</div>

				<div class="col-md-12">
					<table class="table table-striped table-responsive">
						<thead>
							<tr>
								<td align='center'><strong>UserName</strong>
								</td>
								<td align='center'><strong>ชื่อ-สกุล</strong>
								</td>
								<td align='center'><strong>หน่วยงาน</strong>
								</td>
								<td align='center'><strong>บรรจุ</strong>
								</td>
								<td align='center'><strong>รับเข้า</strong>
								</td>
								<td align='center'><strong>จัดเก็บ</strong>
								</td>
								<td align='center'><strong>ส่ง</strong>
								</td>
								<td align='center'><strong>ข้อมูล</strong>
								</td>
								<td align='center'><strong>ระบบ</strong>
								</td>
								<td width="18%" align="center"><a title="เพิ่มข้อมูล" href="user.php?submit=Add&Search=<?php echo($search)?>" class='btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$rec_sql = "SELECT id, username, firstname, lastname, depart.depart, pack, rec, keep, tran, data, sys
						FROM user INNER JOIN depart ON user.id_depart = depart.id_depart
						WHERE(CONCAT(username,' ',firstname,' ',lastname,' ',depart.depart,' #',LPAD(pack,1,'0'),LPAD(rec,1,'0'),LPAD(keep,1,'0'),LPAD(tran,1,'0'),LPAD(data,1,'0'),LPAD(sys,1,'0')) like '%$search%') ORDER by firstname  LIMIT $start,$limit";

							$query = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
							while ($arr = mysqli_fetch_array($query)) {
								$autoid = $arr['id'];
							?>
								<tr>
									<td align='center'>
										<?php echo $arr['username'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['firstname'] . ' ' . $arr['lastname'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['depart'] ?>
									</td>
									<td align='center'><i class="far <?php if ($arr['pack'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>
									<td align='center'><i class="far <?php if ($arr['rec'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>
									<td align='center'><i class="far <?php if ($arr['keep'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>
									<td align='center'><i class="far <?php if ($arr['tran'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>
									<td align='center'><i class="far <?php if ($arr['data'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>
									</td>
									<td align='center'><i class="far <?php if ($arr['sys'] == 1) {
																			echo (' fa-check-square');
																		} else {
																			echo ('fa-square');
																		} ?>" aria-hidden="true"></i>
									</td>

									<td align="center">
										<a title="แก้ไขข้อมูล" href="user.php?submit=Edit&show=&selid=<?php echo ($autoid) ?>&Search=<?php echo($search)?>" class="text-info"><i class="fas fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;

										<a href="#" title='คืนค่ารหัส' class="cf_reset text-warning" data-id="<?php echo ($autoid) ?>" data-show="<?php echo ("[ " . $arr['username'] . " ] ชื่อ:" . $arr['firstname'] . ' ' . $arr['lastname']) ?>"><i class="fas fa-sync-alt" aria-hidden="true"></i></a> &nbsp;&nbsp;

										<a href="#" title='ลบข้อมูล' class="cf_delete text-danger" data-id="<?php echo ($autoid) ?>" data-show="<?php echo (" UserName [ " . $arr['username'] . " ] ชื่อ:" . $arr['firstname'] . ' ' . $arr['lastname']) ?>"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
									</td>
								</tr>
							<?php } //===while($arr...
							?>

						</tbody>
					</table>
					<nav aria-label="Page navigation">
					<?php $_link = "user?Search=$search&page="; ?>
						<ul class='pagination justify-content-center'>
							<li class="page-item <?php if ($page == 1) echo ('disabled') ?>"><a class="page-link" href='<?php echo ($_link.$page - 1) ?>' aria-label='ก่อนหน้า'><span aria-hidden='true'>&laquo;</span></a>
							</li>
							<?php 	for ($i = 1; $i <= $total_page; $i++) {

								if ($page - 2 >= 2 and ($i > 2 and $i < $page - 2)) {
									$laq = round($page/2);
									echo "<li class='page-item'><a class='page-link' href='".$_link.$laq."'>...&nbsp;</a></li>";
									$i = $page - 2;
								}

								if ($page + 5 <= $total_page and ($i >= $page + 3 and $i <= $total_page - 2)) {
									$raq = round(($total_page+$page)/2);
									echo "<li class='page-item'><a class='page-link' href='".$_link.$raq."'>...&nbsp;</a></li>";
									$i = $total_page - 1;
								}
							?>
								<li class="page-item <?php if ($page == $i) echo ('active'); ?>">
									<a class="page-link" href='<?php echo ($_link.$i) ?>'><?php echo $i ?></a>
								</li>
							<?php } ?>
							<li class="page-item <?php if ($page == $total_page) echo ('disabled'); ?>"><a class="page-link" href='<?php echo ($_link.$page + 1) ?>' aria-label='ถัดไป'><span aria-hidden='true'>&raquo;</span></a>
							</li>
						</ul>
					</nav>
				</div>
			<?php mysqli_close($dbconn); } ?>

			<!-- ================================== Add Form ========================================= -->
			<?php if ($submit == "Add") { ?>
				<div class="col-md-12">
					<form name="fmAdd" id="frmAdd" method="post" action="user.php?submit=OK&show=OK&Search=<?php echo($search)?>">
						<div class="form-group row">

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="username">User Name</label>
								</div>
								<input name="username" type="text" id="username" class="form-control" required>
							</div>

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text" for="depart">หน่วยงาน</< /label>
								</div>
								<select class="custom-select" id="depart" name="depart">
									<option selected>---</option>
									<?php
									$rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
									while ($arr_1 = mysqli_fetch_array($rstTemp)) {	?>
										<option value="<?php echo ($arr_1['id_depart']) ?>">
											<?php echo ($arr_1['depart']) ?>
										</option>
									<?php } ?>
								</select>
							</div>

						</div>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="firstname">ชื่อ</label>
								</div>
								<input name="firstname" type="text" id="firstname" class="form-control" required="required">
							</div>
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="lastname">นามสกุล</label>
								</div>
								<input name="lastname" type="text" id="lastname" class="form-control" required="required">
							</div>
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="pass" style="background-color: aquamarine">Password</label>
								</div>
								<input name="pass" type="password" id="pass" class="form-control" required="required">
							</div>

						</div>



						<div class="form-group row ">
							<div class="col-md-4 offset-md-1 border border-warning" style="padding:10px 0px 10px 40px;">
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="pack" value="1" id="pack">
									<label class="form-check-label" for="pack">งานบรรจุ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="rec" value="1" id="rec">
									<label class="form-check-label" for="rec">งานรับเข้า</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="keep" value="1" id="keep">
									<label class="form-check-label" for="keep">งานจัดเก็บ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="tran" value="1" id="tran">
									<label class="form-check-label" for="tran">งานส่ง</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="_data" value="1" id="_data">
									<label class="form-check-label" for="_data">ข้อมูลระบบ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="sys" value="1" id="sys">
									<label class="form-check-label" for="sys">ข้อมูลผู้ใช้</label>
								</div>
							</div>
						</div>

						<div class='form-group'>
							<div class='col-md-12' align="center">
								<button type='button' onClick="submitAdd()" class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
								<button type='button' class='btn btn-danger' onClick="document.location.href='user.php?show=OK&Search=<?php echo ($search) ?>'">ยกเลิก</button>
							</div>
						</div>


					</form>
				</div>
			<?php } ?>

			<!-- ================================== Edit Form ========================================= -->
			<?php
			if ($submit == "Edit") {
				$sqlEdit = "SELECT * FROM user WHERE( id = '$Sel_id')";
				$rsEdit = mysqli_query($dbconn, $sqlEdit) or die(mysqli_error($dbconn . " Q=" . $sqlEdit));
				$rowEdit = mysqli_fetch_array($rsEdit);
			?>
				<div class="col-md-12">
					<form name="fmEdit" id="frmEdit" method="post" action="user.php?submit=OK&show=OK&selid=<?php echo ($Sel_id) ?>&Search=<?php echo($search)?>">
						<div class="form-group row">

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="username">User Name</label>
								</div>
								<input name="username" type="text" id="username" value="<?php echo ($rowEdit['username']) ?>" class="form-control" required disabled>
							</div>

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text" for="depart">หน่วยงาน</< /label>
								</div>
								<select class="custom-select" id="depart" name="depart">
									<option>---</option>
									<?php
									$rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
									while ($arr_1 = mysqli_fetch_array($rstTemp)) {	?>
										<option value="<?php echo ($arr_1['id_depart']) ?>" <?php if ($rowEdit['id_depart'] == $arr_1['id_depart']) {
																								echo ' selected';
																							} ?>>
											<?php echo ($arr_1['depart']) ?>
										</option>
									<?php } ?>
								</select>
							</div>

						</div>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="firstname">ชื่อ</label>
								</div>
								<input name="firstname" type="text" id="firstname" value="<?php echo ($rowEdit['firstname']) ?>" class="form-control" required>
							</div>
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="lastname">นามสกุล</label>
								</div>
								<input name="lastname" type="text" id="lastname" value="<?php echo ($rowEdit['lastname']) ?>" class="form-control" required>
							</div>

						</div>

						<div class="form-group row">
							<div class="col-md-4 offset-md-1  border border-warning" style="padding:10px 0px 10px 40px;">
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="pack" value="1" id="pack" <?php if ($rowEdit['pack'] == 1) {
																														echo ('checked');
																													} ?>>
									<label class="form-check-label" for="pack">งานบรรจุ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="rec" value="1" id="rec" <?php if ($rowEdit['rec'] == 1) {
																														echo ('checked');
																													} ?>>
									<label class="form-check-label" for="rec">งานรับเข้า</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="keep" value="1" id="keep" <?php if ($rowEdit['keep'] == 1) {
																														echo ('checked');
																													} ?>>
									<label class="form-check-label" for="keep">งานจัดเก็บ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="tran" value="1" id="tran" <?php if ($rowEdit['tran'] == 1) {
																														echo ('checked');
																													} ?>>
									<label class="form-check-label" for="tran">งานจ่ายโอน</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="_data" value="1" id="_data" <?php if ($rowEdit['data'] == 1) {
																															echo ('checked');
																														} ?>>
									<label class="form-check-label" for="_data">ข้อมูลระบบ</label>
								</div>
								<div class="form-check col-md-12">
									<input class="form-check-input" type="checkbox" name="sys" value="1" id="sys" <?php if ($rowEdit['sys'] == 1) {
																														echo ('checked');
																													} ?>>
									<label class="form-check-label" for="sys">ข้อมูลผู้ใช้</label>
								</div>
							</div>
						</div>

						<div class='form-group'>
							<div class='col-md-12' align="center">
								<button type='submit' class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
								<button type='button' class='btn btn-danger' onClick="document.location.href='user.php?show=OK&Search=<?php echo ($search) ?>'">ยกเลิก</button>
							</div>
						</div>
					</form>
				</div>
			<?php mysqli_close($dbconn); } ?>

		</div>
	</div>


	<script>
		$(document).on('click', '.cf_delete', function(e) {
			var show = $(this).data('show');
			var id = $(this).data('id');
			var sea = '<?php echo($search);?>';
			e.preventDefault();

			bootbox.confirm({
				title: 'ยืนยันการลบข้อมูล !!!',
				//size: 'small',
				message: 'ต้องการลบข้อมูล <b>' + show + '</b> ใช่หรือไม่?',
				buttons: {
					confirm: {
						label: 'ใช่',
						className: 'btn-success'
					},
					cancel: {
						label: 'ไม่',
						className: 'btn-danger'
					}
				},
				callback: function(result) {
					if (result) {
						window.location.href = 'user.php?submit=DEL&show=OK&selid=' + id+'&Search='+sea;
					}
				}
			});

		});

		$(document).on('click', '.cf_reset', function(e) {
			var show = $(this).data('show');
			var id = $(this).data('id');
			var sea = '<?php echo($search);?>';
			e.preventDefault();

			bootbox.confirm({
				title: 'ยืนยัน !!!',
				//size: 'small',
				message: 'ต้องการคืนค่ารหัสผ่าน <b>' + show + '</b> เป็น 123456 ใช่หรือไม่?',
				buttons: {
					confirm: {
						label: 'ใช่',
						className: 'btn-success'
					},
					cancel: {
						label: 'ไม่',
						className: 'btn-danger'
					}
				},
				callback: function(result) {
					if (result) {
						window.location.href = 'user.php?submit=RES&show=OK&selid=' + id+'&Search='+sea;
					}
				}
			});

		});

		function submitAdd() {
			var s1 = document.getElementsByName('depart');
			s1 = s1.item(0).value;
			var s2 = document.getElementsByName('username');
			s2 = s2.item(0).value;
			var s3 = document.getElementsByName('firstname');
			s3 = s3.item(0).value;
			var s4 = document.getElementsByName('lastname');
			s4 = s4.item(0).value;
			var s5 = document.getElementsByName('pass');
			s5 = s5.item(0).value;

			if ((s1 == "---") || (s2 == '') || (s3 == '') || (s4 == '') || (s5 == '')) {

				$(function() {
					bootbox.alert("โปรดระบุข้อมูลให้ครบถ้วน !", function() {});
				})
			} else {
				$('#frmAdd').submit();
			}

		}
	</script>

</body>

</html>