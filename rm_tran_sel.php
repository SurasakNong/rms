<!doctype html>
<html lang="en">

<head>
	<title>RM-TRANSFER SELECT</title>
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

	<script>
		var cked = true;
	</script>
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
	$bill_no = (isset($_GET['bill_no'])) ? $_GET['bill_no'] : ((isset($_POST['bill_no'])) ? $_POST['bill_no'] : 1);

	$dp_Acc = (isset($_SESSION['s_dpid'])) ? $_SESSION['s_dpid'] : '';  // รหัสผู้ใช้งาน
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

	?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-success text-white" align="center"><?php echo $_SESSION['s_depart'] ?> <i class="fas fa-truck fa-lg" aria-hidden="true"></i> ส่งวัตถุดิบ -> เลือกรายการส่ง</span>
			</div>

			<?php
			if ($submit == "" or $show == "OK") { ?>
				<!-- =========================== Show Form ================================== -->
				<div class="col-md-12">
					<form name="fmSearch" id="fmSearch" method="post" action="rm_tran_sel.php?show=OK&trandate=<?php echo ($date_tran) ?>&dptran=<?php echo ($tranto_dp) ?>&bill_no=<?php echo ($bill_no) ?>&Group=<?php echo ($Group) ?>" role='Search'>
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
				$limit = '60';
				$tran_sql = "SELECT pack_date, rec_date, box_id,roll_n, prod_kg, d1.depart AS frm_dp, d2.depart AS to_dp, prod_name, col_code, rm_code, roll_type, pr.status, shelf_name, shelf.shelf_id
FROM((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)
    INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE((pr.status BETWEEN 1 AND 2) AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0'),' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' #',LPAD(box_id,3,'0'),' ',d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y')) like '%$search%')";

				$Qtotal = mysqli_query($dbconn, $tran_sql) or die(mysqli_error($dbconn . " Q=" . $tran_sql));
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
				$n = $total_data;
				?>
				<!-- ========================================  Form Table ======================================== -->
				<div class="col-md-12">
					<form name="fmTable" id="fmTable" method="post" action="rm_tran?submit=OK&show=OK&numbox=<?php echo ($n) ?>&trandate=<?php echo ($date_tran) ?>&dptran=<?php echo ($tranto_dp) ?>&bill_no=<?php echo ($bill_no) ?>&Group=<?php echo ($Group) ?>">
						<div class="form-group row">
							<div class="col-md-9">
								<div class='alert alert-success' role='alert' align="center">
									<?php
									echo ($from . "-" . $to);
									echo (" (" . number_format($total_data) . " กล่อง ");
									echo (" -- " . number_format($numRoll) . " หลอด ");
									echo (" -- " . number_format($Kg, 2) . " ก.ก.)");

									?>
								</div>
							</div>
							<div class="col-md-3" align="center" style="padding-bottom: 10px">
								<a name="cfUp" title="ส่งวัตถุดิบที่เลือก" type='submit' class='btn btn-success'>เลือกส่ง</a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a name="cfgoback1" title="กลับหน้าส่งวัตถุดิบ" href="rm_tran?submit=&show=OK&trandate=<?php echo ($date_tran) ?>&dptran=<?php echo ($tranto_dp) ?>&bill_no=<?php echo ($bill_no) ?>&Group=<?php echo ($Group) ?>" class='btn btn-warning'><i class="fa fa-undo" aria-hidden="true"></i></a>


							</div>

							<div class="col-md-12">
								<table class="table table-striped table-responsive">
									<thead>
										<tr>
											<td align='center'><a href="#" class='text-success' onClick="fnCkeckBox();">เลือก</a>
											</td>
											<td align='center'><strong>#กล่อง</strong>
											</td>
											<td align='center' width="250px"><strong>รายการ</strong>
											</td>
											<td align='center'><strong>หลอด</strong>
											</td>
											<td align='center'><strong>น้ำหนัก</strong>
											</td>
											<td align='center'><strong>ช่อง</strong>
											</td>
											<td align='center'><strong>วันที่รับ</strong>
											</td>

										</tr>
									</thead>
									<tbody>
										<?php
										$tran_sql = "SELECT rec_date, pack_date, from_dp ,box_id,roll_n, prod_kg, d1.depart AS frm_dp, d2.depart AS to_dp, prod_name, col_code, rm_code, roll_type, CONCAT(prod_name,' ', col_code,' ', rm_code,' ', roll_type) AS nameProd, pr.status, shelf_name, shelf.shelf_id, CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0') ,LPAD(box_id,3,'0')) AS b_name
FROM((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)
    INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE((pr.status BETWEEN 1 AND 2) AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0'),' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' #',LPAD(box_id,3,'0'),' ',d1.depart,' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y')) like '%$search%') ORDER BY nameProd , rec_date DESC, (box_id *1) LIMIT $start,$limit";

										$query = mysqli_query($dbconn, $tran_sql) or die(mysqli_error($dbconn . " Q=" . $tran_sql));
										$n =  mysqli_num_rows($query);
										$i = 0;
										while ($arr = mysqli_fetch_array($query)) {
											$i++;
										?>
											<tr>
												<td align='center'>
													<input class="form-check-input" type="checkbox" name="ckIn<?php echo ($i); ?>" value="1">
												</td>
												<td align='center'>
													<?php echo $arr['box_id'] ?>
												</td>
												<td align='center'>
													<?php echo $arr['nameProd'] ?>
												</td>
												<td align='right'>
													<?php echo number_format($arr['roll_n']) ?>
												</td>
												<td align='right'>
													<?php echo number_format($arr['prod_kg'], 2) ?>
												</td>
												<td align='center'>
													<?php echo $arr['shelf_name'] ?>
												</td>
												<td align='center'>
													<?php echo DatedmY($arr['rec_date']) ?>
												</td>
											</tr>
											<input type='hidden' name="b_name<?php echo ($i) ?>" value="<?php echo $arr['b_name'] ?>" />
										<?php }	?>

									</tbody>
								</table>
								<nav aria-label="Page navigation">
								<?php $_link = "rm_tran_sel?Search=$search&trandate=$date_tran&bill_no=$bill_no&dptran=$tranto_dp&Group=$Group&page="; ?>
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
					</form>
				</div>
		</div>
	</div>

	<!-- =========================== SCRIPT ======================================== -->
	<script>
		$("a[name='cfUp']").click(function() {
			data = '<?php echo ($n) ?>';
			var numck = 0;
			for (i = 1; i <= data; i++) {
				var ob1 = document.getElementsByName('ckIn' + i);
				if (ob1.item(0).checked) {
					numck++;
				}
			}
			if (numck > 0) {
				$('#fmTable').submit();

			} else {
				$(function() {
					bootbox.alert('กรุณาเลือกรายการที่ต้องการส่ง...!', function() {
						/*window.history.back();*/
					});
				})
			}
		});

		function fnCkeckBox() {
			data = '<?php echo ($n) ?>';
			var st = false;
			var ckstr = "";
			st = cked;
			for (i = 1; i <= data; i++) {
				var ob = document.getElementsByName('ckIn' + i);
				ob.item(0).checked = st;
			}
			cked = !cked;
		}
	</script>

</body>

</html>