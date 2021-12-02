<!doctype html>
<html lang="en">

<head>
	<title>RM-RECEIVE</title>
	<link rel="shortcut icon" href="image/stockicon128.ico">
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<script src="./js/jquery.min.js"></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src='./js/bootbox.min.js'></script>
	<script src='./js/jquery.datetimepicker.full.min.js'></script>
	<link rel="stylesheet" href="./css/jquery.datetimepicker.min.css">
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<link rel="stylesheet" href="./css/all.min.css"> <!-- font awesome -->
	<link rel="stylesheet" href="./css/menunav.css">

	<?php
	error_reporting(E_ALL ^ E_NOTICE);

	include 'Menu_admin.php';
	include_once 'dbConn.php';
	$show = (isset($_GET['show'])) ? $_GET['show'] : '';
	$submit = (isset($_GET['submit'])) ? $_GET['submit'] : '';
	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
	$search = (isset($_GET['Search'])) ? $_GET['Search'] : ((isset($_POST['Search'])) ? $_POST['Search'] : '');
	$Sel_box = (isset($_GET['selbox'])) ? $_GET['selbox'] : '';
	$Num_Box = (isset($_GET['numbox'])) ? $_GET['numbox'] : '';

	$date_rec = (isset($_GET['recdate'])) ? $_GET['recdate'] : ((isset($_POST['date_rec'])) ? DateYmd($_POST['date_rec']) : date("Y-m-d"));

	$r_bill = (isset($_GET['rec_bill'])) ? $_GET['rec_bill'] : ((isset($_POST['rec_bill'])) ? $_POST['rec_bill'] : 1);

	$dp_Acc = (isset($_SESSION['s_dpid'])) ? $_SESSION['s_dpid'] : '';  // รหัสผู้ใช้งาน
	$df_Acc = (isset($_SESSION['s_dfid'])) ? $_SESSION['s_dfid'] : '';  // ช่องก่อนจัดเก็บของหน่วยงานผู้ใช้งาน

	function DateYmd($date)
	{
		$get_date = explode("/", $date);
		return $get_date['2'] . "-" . $get_date['1'] . "-" . $get_date['0'];
	}

	function DatedmY($date)
	{
		$get_date = explode("-", $date);
		return $get_date['2'] . "/" . $get_date['1'] . "/" . $get_date['0'];
	}

	if ($submit == "OK") { //================== UPDATE ====================
		for ($i = 1; $i <= $Num_Box; $i++) {
			$strStatus = "ckIn" . $i;
			$strStatus = (isset($_POST[$strStatus])) ? $_POST[$strStatus] : '';
			if ($strStatus == "1") {
				$B_name = "b_name" . $i;
				$B_name = $_POST[$B_name];

				$sqlUp = "UPDATE pro_pack SET status='1', rec_date ='$date_rec', rec_bill ='$r_bill', shelf_id='$df_Acc' WHERE(CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) = '$B_name')";
				$rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));

				//===== LOG ==========
				$descLog = "recive" . $date_rec . "_$" . $r_bill . "_dp" . $dp_Acc . "_box" . $B_name;
				saveLog($_SESSION['s_id'], '_Rec', $descLog);
			}
		}
	}

	if ($submit == "DEL") { //============ UPDATE to Null =======================
		$sqldel = "UPDATE pro_pack SET status='0', rec_date = NULL, rec_bill='0', shelf_id= NULL WHERE(CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) = '$Sel_box')";
		$rsDel = mysqli_query($dbconn, $sqldel) or die(mysqli_error($dbconn . " Q=" . $sqldel));

		//===== LOG ==========
		$descLog = "recive" . $date_rec . "_$" . $r_bill . "_dp" . $dp_Acc . "_box" . $Sel_box;
		saveLog($_SESSION['s_id'], '_cancelRec', $descLog);
	}

	?>
</head>

<body>
	<div class="container">
		<div class="row">


			<?php
			if ($submit == "" or $show == "OK") { ?>
				<!-- ======================== Show Form ============================ -->
				<div class="col-md-12" style='margin-bottom: 8px; font-size:18px'>
					<span class="d-block p-2 bg-success text-white" align="center"><?php echo ($_SESSION['s_depart']) ?> <i class="fa fa-arrow-circle-right fa-lg fa-fw" aria-hidden="true"></i> รับเข้าวัตถุดิบ </span>
				</div>
				<div class="col-md-12">
					<form name="fmSearch" id="fmSearch" method="post" action="rm_rec?show=OK&strSearch=Y" role='Search'>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="picker">&nbsp;วันที่&nbsp;&nbsp;&nbsp;</label>
								</div>
								<input name="date_rec" type="text" value='<?php echo (DatedmY($date_rec)) ?>' id="picker" class="form-control" onchange="submitS()">
							</div>

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="rec_bill" style="background-color: aquamarine">เลขบิล</label>
								</div>
								<input name="rec_bill" type="number" id="rec_bill" onchange="submitS()" class="form-control input-no-spinner" value="<?php echo $r_bill ?>" required>
							</div>

							<div class="input-group mb-2 col-md-12">
								<input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo $search ?>' onFocus="this.value ='';">
								<div class="input-group-append">
									<button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php
				$limit = '25';
				$cal_sql = "SELECT pack_date, from_dp, rec_date, rec_bill ,box_id, roll_n, prod_kg, to_dp, depart, prod_name, col_code, rm_code, roll_type, val, status
FROM((((pro_pack AS pp INNER JOIN depart ON from_dp = id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pp.prod_id = product.prod_id)
        INNER JOIN color ON pp.col_id = color.col_id)
    INNER JOIN rm_code ON pp.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pp.roll_id = roll_type.roll_id WHERE(status>0 AND rec_date='$date_rec' AND to_dp='$dp_Acc' AND rec_bill='$r_bill' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0'),IF(status=1,' รับ',IF(status=2,' จัดเก็บ',IF(status=3,' ส่ง',' บรรจุ'))),' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' ',depart,' #',LPAD(box_id,3,'0'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%')";

				$Qtotal = mysqli_query($dbconn, $cal_sql) or die(mysqli_error($dbconn . " Q=" . $cal_sql));
				$total_data = mysqli_num_rows($Qtotal);
				$total_page = ceil($total_data / $limit);
				$numRoll = 0;
				$Kg = 0;
				$maxBoxid = 1;
				while ($resual = mysqli_fetch_array($Qtotal)) {
					$numRoll = $numRoll + $resual['roll_n'];
					$Kg = $Kg + $resual['prod_kg'];
				}

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
					<div class='alert alert-success' role='alert' align="center">
						<?php
						echo ($from . " - " . $to);
						echo (" ( " . number_format($total_data) . " กล่อง ");
						echo (" -- " . number_format($numRoll) . " หลอด ");
						echo (" -- " . number_format($Kg, 2) . " ก.ก. )");

						?>
					</div>
				</div>

				<div class="col-md-12">
					<table class="table table-striped table-responsive">
						<thead>
							<tr>
								<td align='center'><strong>บรรจุ</strong>
								</td>
								<td align='center'><strong>#กล่อง</strong>
								</td>
								<td align='center'><strong>รายการ</strong>
								</td>
								<td align='center'><strong>หลอด</strong>
								</td>
								<td align='center'><strong>จำนวน</strong>
								</td>
								<td align='center'><strong>น้ำหนัก</strong>
								</td>
								<td align='center'><strong>สถานะ</strong>
								</td>
								<td align='center'><strong>Index</strong>
								</td>
								<td align="center"><a title="เพิ่มข้อมูล" href="rm_rec_sel?submit=&recdate=<?php echo ($date_rec) ?>&rec_bill=<?php echo $r_bill ?>&show=" class='btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$show_sql = "SELECT pack_date, pack_bill, rec_date, rec_bill, box_id, from_dp, to_dp, depart, roll_n, prod_kg, prod_name, col_code, rm_code, roll_type, val, status,CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) AS box_name
FROM((((pro_pack AS pp INNER JOIN depart ON from_dp = id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pp.prod_id = product.prod_id)
        INNER JOIN color ON pp.col_id = color.col_id)
    INNER JOIN rm_code ON pp.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pp.roll_id = roll_type.roll_id WHERE(status>0 AND rec_date='$date_rec' AND to_dp='$dp_Acc' AND rec_bill='$r_bill' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0'),IF(status=1,' รับ',IF(status=2,' จัดเก็บ',IF(status=3,' ส่ง',' บรรจุ'))),' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' ',depart,' #',LPAD(box_id,3,'0'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%') ORDER BY pack_date, from_dp, (box_id*1) DESC LIMIT $start,$limit";

							$query = mysqli_query($dbconn, $show_sql) or die(mysqli_error($dbconn . " Q=" . $show_sql));
							while ($arr = mysqli_fetch_array($query)) {
								$autoboxname = $arr['box_name'];
							?>
								<tr>
									<td align='center'>
										<?php echo DatedmY($arr['pack_date']) . " " . $arr['depart'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['box_id'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['prod_name'] . ' ' . $arr['col_code'] . ' ' . $arr['rm_code'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['roll_type'] ?>
									</td>
									<td align='right'>
										<?php echo number_format($arr['roll_n']) ?>
									</td>
									<td align='right'>
										<?php echo number_format($arr['prod_kg'], 2) ?>
									</td>
									<td align='center'>
										<?php if ($arr['status'] == 1) {
											echo "รับ";
										} elseif ($arr['status'] == 2) {
											echo "จัดเก็บ";
										} elseif ($arr['status'] == 3) {
											echo "ส่ง";
										} else {
											echo "[บรรจุ]";
										} ?>
									</td>
									<td align='right'>
										<?php echo number_format($arr['val'], 2) ?>
									</td>
									<td align="center">
										<?php if ($arr['status'] == 1) { ?>
											<a href="#" title='ยกเลิกรับเข้า' class="cf_delete text-danger" data-rdate="<?php echo ($date_rec) ?>" data-rbill="<?php echo $r_bill ?>" data-id="<?php echo $autoboxname ?>" data-show="<?php echo (" กล่อง [ " . $arr['pack_date'] . " " . $arr['depart'] . "-#" . $arr['box_id'] . " ]") ?>"><i class="fas fa-window-close fa-lg" aria-hidden="true"></i></a>
										<?php } else { ?>
											<a style="color:lightgray"><i class="fas fa-window-close fa-lg" aria-hidden="true"></i></a>
										<?php } ?>
									</td>
								</tr>
							<?php } //===แสดงข้อมูลในตาราง
							?>

						</tbody>
					</table>
					<nav aria-label="Page navigation">
					<?php $_link = "rm_rec?Search=$search&recdate=$date_rec&rec_bill=$r_bill&page="; ?>
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

		</div>
	</div>

	<!-- ============================== SCRIPT ==================================== -->
	<script>
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();
		today = yyyy + '-' + mm + '-' + dd;

		jQuery.datetimepicker.setLocale('th');

		$('#picker').datetimepicker({
			timepicker: false,
			datepicker: true,
			format: 'd/m/Y',
			//value:today,
			mask: true
		})

		$(document).on('click', '.cf_delete', function(e) {
			var show = $(this).data('show');
			var id = $(this).data('id');
			var rdate = $(this).data('rdate');
			var rbill = $(this).data('rbill');
			e.preventDefault();

			bootbox.confirm({
				title: 'ยืนยันการยกเลิกรับเข้า !!!',
				//size: 'small',
				message: 'ต้องการยกเลิกรับเข้า << <b>' + show + '</b> >> ?',
				buttons: {
					confirm: {
						label: 'ตกลง',
						className: 'btn-success'
					},
					cancel: {
						label: 'ยกเลิก',
						className: 'btn-danger'
					}
				},
				callback: function(result) {
					if (result) {
						window.location.href = 'rm_rec?submit=DEL&show=OK&recdate=' + rdate + '&rec_bill=' + rbill + '&selbox=' + id;
					}
				}
			});

		});

		function submitS() {
			document.getElementById("fmSearch").submit();
		}
	</script>

</body>

</html>
