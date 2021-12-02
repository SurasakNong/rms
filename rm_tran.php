<!doctype html>
<html lang="en">

<head>
	<title>RM-TRANSFER</title>
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
	$Num_Box = (isset($_GET['numbox'])) ? $_GET['numbox'] : '';
	$Sel_box = (isset($_GET['selbox'])) ? $_GET['selbox'] : '';

	$tranto_dp = (isset($_GET['dptran'])) ? $_GET['dptran'] : ((isset($_POST['dptran'])) ? $_POST['dptran'] : '');
	$date_tran = (isset($_GET['trandate'])) ? $_GET['trandate'] : ((isset($_POST['date_tran'])) ? DateYmd($_POST['date_tran']) : date("Y-m-d"));


	$dp_Acc = (isset($_SESSION['s_dpid'])) ? $_SESSION['s_dpid'] : '';  // รหัสผู้ใช้งาน

	$bill_no = (isset($_GET['bill_no'])) ? $_GET['bill_no'] : ((isset($_POST['bill_no'])) ? $_POST['bill_no'] : 1);
	$pdid = (isset($_GET['prodid'])) ? $_GET['prodid'] : '';
	$coid = (isset($_GET['colid'])) ? $_GET['colid'] : '';
	$rid = (isset($_GET['rmid'])) ? $_GET['rmid'] : '';
	$roid = (isset($_GET['rollid'])) ? $_GET['rollid'] : '';
	$Group = (isset($_GET['Group'])) ? $_GET['Group'] : 'NO';

	$n = 0;

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

	if ($submit == "CAN") { //===== Cancel Transfer List ==========
		if ($Group == "NO") {
			$sqlUp = "UPDATE pro_pack SET tran_date= NULL ,bill_no='0',tranto_dp= NULL,status='2' WHERE(status='3' AND to_dp='$dp_Acc' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) = '$Sel_box')";
			$descLog = "tranfer" . $date_tran . "_$" . $bill_no . "_dp" . $tranto_dp . "_box" . $Sel_box; //==== description to log

		} else if ($Group == "YES") {
			$sqlUp = "UPDATE pro_pack SET tran_date= NULL ,bill_no='0',tranto_dp= NULL,status='2' WHERE(status='3' AND tran_date='$date_tran' AND tranto_dp='$tranto_dp' AND to_dp='$dp_Acc' AND bill_no='$bill_no' AND prod_id = '$pdid' AND col_id = '$coid' AND rm_id = '$rid' AND roll_id = '$roid')";

			$descLog = "tranfer" . $date_tran . "_$" . $bill_no . "_dp" . $tranto_dp . "_pdid" . $pdid . "_coid" . $coid . "_rid" . $rid . "_roid" . $roid; //==== description to log
		}
		$rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));

		//===== LOG ==========
		saveLog($_SESSION['s_id'], '_cancel', $descLog);
	}

	if ($submit == "OK") { //==================  UPDATE  ====================
		for ($i = 1; $i <= $Num_Box; $i++) {
			$strStatus = "ckIn" . $i;
			$strStatus = (isset($_POST[$strStatus])) ? $_POST[$strStatus] : '';
			if ($strStatus == "1") {
				$B_name = "b_name" . $i;
				$B_name = $_POST[$B_name];

				$sqlUp = "UPDATE pro_pack SET tran_date = '$date_tran', bill_no = '$bill_no', tranto_dp= '$tranto_dp', status='3'  WHERE(CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) = '$B_name')";
				$rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));

				//===== LOG ==========
				$descLog = "transfer" . $date_tran . " $" . $bill_no . " dp" . $tranto_dp . " #" . $B_name;
				saveLog($_SESSION['s_id'], '_add', $descLog);
			}
		}
	}

	?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-success text-white" align="center"><?php echo $_SESSION['s_depart'] ?> <i class="fas fa-truck fa-lg" aria-hidden="true"></i> ส่งวัตถุดิบ</span>
			</div>


			<!-- ================================== Show Form GROUP = NO======================================== -->
			<?php
			if (($submit == "" or $show == "OK") and $Group == "NO") { ?>
				<div class="col-md-12">
					<form name="fmSearch" id="fmSearch" method="post" action="rm_tran?show=OK&strSearch=Y&Group=NO" role='Search'>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " style="width: 80px;" for="picker">วันที่ส่ง</< /label>
								</div>
								<input name="date_tran" type="text" value='<?php echo (DatedmY($date_tran)) ?>' id="picker" onchange="submitS()" class="form-control" required>
							</div>

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text " for="bill" style="background-color: aquamarine; width: 80px;">เลขที่บิล</< /label>
								</div>
								<input name="bill_no" type="number" id="bill_no" value="<?php echo ($bill_no) ?>" onchange="submitS()" class="form-control input-no-spinner" required>
							</div>

							<div class="input-group mb-2 col-md-6">
								<div class="input-group-prepend">
									<label class="input-group-text" style="width: 80px;" for="dptran">ผู้รับ</< /label>
								</div>
								<select class="custom-select" id="dptran" name="dptran" onchange="submitS()">
									<option selected>---</option>
									<?php
									$rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
									while ($arr_2 = mysqli_fetch_array($rstTemp)) {
									?>
										<option value="<?php echo ($arr_2['id_depart']) ?>" <?php if ($tranto_dp == $arr_2['id_depart']) {
																								echo ('selected');
																							} ?>>
											<?php echo ($arr_2['depart']) ?>
										</option>
									<?php } ?>
								</select>
							</div>

							<div class="input-group mb-2 col-md-6">
								<input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo ($search); ?>' onFocus="this.value ='';">
								<div class="input-group-append">
									<button class="btn btn-primary" type='submit'><i class="fas fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php
				$limit = '30';
				$rec_sql = "SELECT tran_date, bill_no, tranto_dp, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prod, box_id, roll_n, prod_kg, rec_date
FROM ((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
	INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE(pr.status = 3 AND tran_date = '$date_tran' AND bill_no = '$bill_no' AND tranto_dp = '$tranto_dp' AND to_dp='$dp_Acc'  AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0'),' #',LPAD(box_id,3,'0'),' ',d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y')) like '%$search%')";

				$Qtotal = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
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
								<td align='center'><strong>#กล่อง</strong>
								</td>
								<td align='center' width="300px"><a href="rm_tran?submit=&trandate=<?php echo ($date_tran) ?>&dptran=<?php echo ($tranto_dp) ?>&bill_no=<?php echo ($bill_no) ?>&Group=YES&show=">รายการ</a>
								</td>
								<td align='center'><strong>หลอด</strong>
								</td>
								<td align='center'><strong>จำนวน</strong>
								</td>
								<td align='center'><strong>น้ำหนัก</strong>
								</td>
								<td align='center'><strong>วันรับเข้า</strong>
								</td>
								<td width="18%" align="center"><a title="เพิ่มรายการจัดส่ง" href="#" data-group="<?php echo ($Group) ?>" class='cf_add btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$rec_sql = "SELECT pack_date, from_dp, tran_date, bill_no, tranto_dp, CONCAT(prod_name,' ',col_code,' ',rm_code) AS prod, box_id,roll_type, roll_n, prod_kg, rec_date, CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) AS b_name
						FROM ((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
	INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE(pr.status = 3 AND tran_date = '$date_tran' AND bill_no = '$bill_no' AND tranto_dp = '$tranto_dp' AND to_dp='$dp_Acc' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0'),' #',LPAD(box_id,3,'0'),' ',d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y')) like '%$search%')
ORDER BY prod,(box_id *1) LIMIT $start,$limit";

							$query = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
							while ($arr = mysqli_fetch_array($query)) {

								$autoboxname = $arr['b_name']
							?>
								<tr>
									<td align='center'>
										<?php echo $arr['box_id'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['prod'] ?>
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
										<?php echo DatedmY($arr['rec_date']) ?>
									</td>
									<td align="center">
										<a href="#" title='ยกเลิกจัดส่ง' class="cf_delete text-danger" data-id="<?php echo $autoboxname ?>" data-show="<?php echo (" กล่อง [ #" . $arr['box_id'] . "]" . $arr['prod'] . " ของวันที่ " . datedmY($date_tran)) ?>"><i class="fas fa-window-close fa-lg" aria-hidden="true"></i></a>
									</td>
								</tr>
							<?php } //===while($arr...
							?>

						</tbody>
					</table>
					<nav aria-label="Page navigation">
					<?php $_link = "rm_tran?Search=$search&trandate=$date_tran&bill_no=$bill_no&dptran=$tranto_dp&Group=$Group&page="; ?>
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
			<?php mysqli_close($dbconn); } else { ?>

				<!-- ================================== Show Form Group = YES======================================== -->
				<?php
				if (($submit == "" or $show == "OK") and $Group == "YES") { ?>
					<div class="col-md-12">
						<form name="fmSearch" id="fmSearch" method="post" action="rm_tran?show=OK&Group=YES" role='Search'>
							<div class="form-group row">
								<div class="input-group mb-2 col-md-6">
									<div class="input-group-prepend">
										<label class="input-group-text" style="width: 80px;" for="picker">วันที่ส่ง</< /label>
									</div>
									<input name="date_tran" type="text" value='<?php echo (DatedmY($date_tran)) ?>' onchange="submitS()" id="picker" class="form-control" required>
								</div>

								<div class="input-group mb-2 col-md-6">
									<div class="input-group-prepend">
										<label class="input-group-text " for="bill" style="background-color: aquamarine; width: 80px;">เลขที่บิล</< /label>
									</div>
									<input name="bill_no" type="number" id="bill_no" value="<?php echo ($bill_no) ?>" onchange="submitS()" class="form-control input-no-spinner" required>
								</div>

								<div class="input-group mb-2 col-md-6">
									<div class="input-group-prepend">
										<label class="input-group-text" style="width: 80px;" for="dptran">ผู้รับ</< /label>
									</div>
									<select class="custom-select" id="dptran" name="dptran" onchange="submitS()">
										<option selected>---</option>
										<?php
										$rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
										while ($arr_2 = mysqli_fetch_array($rstTemp)) {
										?>
											<option value="<?php echo ($arr_2['id_depart']) ?>" <?php if ($tranto_dp == $arr_2['id_depart']) {
																									echo ('selected');
																								} ?>>
												<?php echo ($arr_2['depart']) ?>
											</option>
										<?php } ?>
									</select>
								</div>

								<div class="input-group mb-2 col-md-6">
									<input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo ($search); ?>' onFocus="this.value ='';">
									<div class="input-group-append">
										<button class="btn btn-primary" type='submit'><i class="fas fa-search" aria-hidden="true"></i></button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<?php
					$limit = '30';
					$rec_sql = "SELECT tran_date, bill_no, tranto_dp, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prod, count(box_id) as box_n, sum(roll_n) as roll_n, sum(prod_kg) as prod_kg, pr.prod_id, pr.col_id, pr.rm_id, pr.roll_id
FROM ((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
           INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
	INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE(pr.status = 3 AND tran_date = '$date_tran' AND bill_no = '$bill_no' AND tranto_dp = '$tranto_dp' AND to_dp='$dp_Acc' AND CONCAT(d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type) like '%$search%')
GROUP BY prod
ORDER BY prod";

					$Qtotal = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
					$total_data = mysqli_num_rows($Qtotal);
					$total_page = ceil($total_data / $limit);
					$numRoll = 0;
					$Kg = 0;
					$BoxN = 0;
					$maxBoxid = 1;
					while ($resual = mysqli_fetch_array($Qtotal)) {
						$numRoll = $numRoll + $resual['roll_n'];
						$Kg = $Kg + $resual['prod_kg'];
						$BoxN = $BoxN + $resual['box_n'];
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
						<div class='alert alert-warning' role='alert' align="center">
							<?php
							echo ($from . " - " . $to);
							echo (" ( " . number_format($BoxN) . " กล่อง ");
							echo (" -- " . number_format($numRoll) . " หลอด ");
							echo (" -- " . number_format($Kg, 2) . " ก.ก. )");

							?>
						</div>
					</div>

					<div class="col-md-12">
						<table class="table table-striped table-responsive">
							<thead>
								<tr>
									<td align='center' width="300px"><strong>รายการ</strong>
									</td>
									<td align='center'><a href="rm_tran?submit=&trandate=<?php echo ($date_tran) ?>&dptran=<?php echo ($tranto_dp) ?>&bill_no=<?php echo ($bill_no) ?>&Group=NO&show=">กล่อง</a>
									</td>
									<td align='center'><strong>หลอด</strong>
									</td>
									<td align='center'><strong>น้ำหนัก</strong>
									</td>
									<td width="18%" align="center"><a title="เพิ่มรายการจัดส่ง" href="#" data-group="<?php echo ($Group) ?>" class='cf_add btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
									</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$rec_sql = "SELECT tran_date, bill_no, tranto_dp, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prod, count(box_id) as box_n, sum(roll_n) as roll_n, sum(prod_kg) as prod_kg, pr.prod_id, pr.col_id, pr.rm_id, pr.roll_id
FROM ((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
          INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
	INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE(pr.status = 3 AND tran_date = '$date_tran' AND bill_no = '$bill_no' AND tranto_dp = '$tranto_dp' AND to_dp='$dp_Acc' AND CONCAT(d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type) like '%$search%')
GROUP BY prod
ORDER BY prod LIMIT $start,$limit";

								$query = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
								while ($arr = mysqli_fetch_array($query)) {
									$autoprod = $arr['prod_id'];
									$autocol = $arr['col_id'];
									$autorm = $arr['rm_id'];
									$autoroll = $arr['roll_id'];
								?>
									<tr>
										<td align='center'>
											<?php echo $arr['prod'] ?>
										</td>
										<td align='center'>
											<?php echo number_format($arr['box_n']) ?>
										</td>
										<td align='right'>
											<?php echo number_format($arr['roll_n']) ?>
										</td>
										<td align='right'>
											<?php echo number_format($arr['prod_kg'], 2) ?>
										</td>
										<td align="center">
											<a href="#" title='ยกเลิกจัดส่ง' class="cf_delete2 text-danger" data-pid="<?php echo $autoprod ?>" data-cid="<?php echo $autocol ?>" data-rid="<?php echo $autorm ?>" data-roid="<?php echo $autoroll ?>" data-show="<?php echo (" " . $arr['prod']) ?>"><i class="fas fa-window-close fa-lg" aria-hidden="true"></i></a>
										</td>
									</tr>
								<?php } //===while($arr...
								?>

							</tbody>
						</table>
						<nav aria-label="Page navigation">
						<?php $_link = "rm_tran?Search=$search&trandate=$date_tran&bill_no=$bill_no&dptran=$tranto_dp&Group=$Group&page="; ?>
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
			<?php } ?>

		</div>
	</div>


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

		$(document).on('click', '.cf_add', function(e) {
			var group = $(this).data('group');
			var tdate = '';
			var s2 = document.getElementsByName('date_tran');
			s2 = s2.item(0).value;
			var tdate = s2.split('/');
			tdate = tdate[2] + '-' + tdate[1] + '-' + tdate[0];

			var tbill = "";
			var tbill = document.getElementsByName('bill_no');
			tbill = tbill.item(0).value;

			var tdp = "---"
			var s1 = document.getElementsByName('dptran');
			tdp = s1.item(0).value;

			var sch = '';
			var s3 = document.getElementsByName('Search');
			sch = s3.item(0).value;
			e.preventDefault();

			if ((tdp == "---") || (tbill == "")) { 
				$(function() {
					bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนเพิ่มข้อมูล !!",
						function() {});
				})

			} else {
				$(function() {
					window.location.href = 'rm_tran_sel?submit=&trandate=' + tdate + '&dptran=' + tdp + '&bill_no=' + tbill + '&Group=' + group + '&show=&Search=' + sch;
				})
			}

		});


		$(document).on('click', '.cf_delete', function(e) {
			var show = $(this).data('show');
			var id = $(this).data('id');

			var tdate = '';
			var s2 = document.getElementsByName('date_tran');
			s2 = s2.item(0).value;
			var tdate = s2.split('/');
			tdate = tdate[2] + '-' + tdate[1] + '-' + tdate[0];

			var tbill = "";
			var tbill = document.getElementsByName('bill_no');
			tbill = tbill.item(0).value;

			var tdp = "---"
			var s1 = document.getElementsByName('dptran');
			tdp = s1.item(0).value;

			var sch = '';
			var s3 = document.getElementsByName('Search');
			sch = s3.item(0).value;
			e.preventDefault();

			if ((tdp == "---") || (tbill == "")) {
				$(function() {
					bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนลบข้อมูล !!",
						function() {});
				})

			} else {

				$(function() {
					bootbox.confirm({
						title: 'ยืนยันการยกเลิกส่งวัตถุดิบ !!!',
						//size: 'small',
						message: 'ต้องการยกเลิก << <b>' + show + '</b> >> ?',
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
								window.location.href = 'rm_tran?submit=CAN&show=OK&Group=NO&trandate=' + tdate + '&dptran=' + tdp + '&bill_no=' + tbill + '&selbox=' + id + '&Search=' + sch;
							}
						}
					});
				})
			}

		});

		$(document).on('click', '.cf_delete2', function(e) {

			var pid = $(this).data('pid');
			var cid = $(this).data('cid');
			var rid = $(this).data('rid');
			var roid = $(this).data('roid');
			var show = $(this).data('show');

			var tdate = '';
			var s2 = document.getElementsByName('date_tran');
			s2 = s2.item(0).value;
			var tdate = s2.split('/');
			tdate = tdate[2] + '-' + tdate[1] + '-' + tdate[0];

			var tbill = "";
			var tbill = document.getElementsByName('bill_no');
			tbill = tbill.item(0).value;

			var tdp = "---"
			var s1 = document.getElementsByName('dptran');
			tdp = s1.item(0).value;

			var sch = '';
			var s3 = document.getElementsByName('Search');
			sch = s3.item(0).value;
			e.preventDefault();

			if ((tdp == "---") || (tbill == "")) {
				$(function() {
					bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนลบข้อมูล !!",
						function() {});
				})
			} else {
				$(function() {
					bootbox.confirm({
						title: 'ยืนยันการยกเลิกส่งวัตถุดิบ !!!',
						//size: 'small',
						message: 'ต้องการยกเลิก << <b>' + show + '</b> >> ?',
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
								window.location.href = 'rm_tran?submit=CAN&show=OK&Group=YES&trandate=' + tdate + '&dptran=' + tdp + '&bill_no=' + tbill + '&prodid=' + pid + '&colid=' + cid + '&rmid=' + rid + '&rollid=' + roid + '&Search=' + sch;
							}
						}
					});
				})
			}

		});

		function submitS() {
			$('#fmSearch').submit();
		}
	</script>

</body>

</html>