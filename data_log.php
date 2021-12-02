<?php
session_start();
?>
<!doctype html>
<html lang="en">

<head>
	<title>Data Management</title>
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

	if ($submit == "DEL") { //====== DELETE ==========
		$sqldel = "DELETE FROM u_log WHERE(dt_stamp = '$Sel_id')";
		$rsDel = mysqli_query($dbconn, $sqldel) or die(mysqli_error($dbconn));
		//===== LOG ==========
		$descLog = "DataLog" . $Sel_id;
		saveLog($_SESSION['s_id'], '_del', $descLog);
	}

	?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-warning text-white" align="center"><i class="fas fa-bookmark" aria-hidden="true"></i> บันทึกเหตุการณ์</span>
			</div>

			<!-- ================================== Show Form ========================================= -->
			<?php
			if ($submit == "" or $show == "OK") { ?>
				<div class="col-md-12">
					<form name="fmSearch" method="post" action="data_log.php?show=OK" role='Search'>
						<div class="form-group row">
							<div class="input-group mb-2 col-md-12">
								<input name='Search' id='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo ($search); ?>' onFocus="this.value ='';">
								<div class="input-group-append">
									<button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php
				$limit = '25';
				$log_sql = "SELECT dt_stamp,id_user,concat(firstname,' ',lastname) as u_name,depart,session,desc_log FROM (u_log INNER JOIN (user INNER JOIN depart ON user.id_depart = depart.id_depart ) on id_user = id) WHERE(CONCAT('d',dt_stamp,' ',firstname,' ',lastname,' ',id_user,' ',session,' ',desc_log) like '%$search%') ORDER by dt_stamp DESC ";

				$Qtotal = mysqli_query($dbconn, $log_sql) or die(mysqli_error($dbconn . " Q=" . $log_sql));
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
						echo (" -- หน้าที่ " . number_format($page) . "/");
						echo (number_format($total_page) . " )");

						?>
					</div>
				</div>

				<div class="col-md-12">
					<table class="table table-striped table-responsive">
						<thead>
							<tr>
								<td align='center'><strong>เวลา</strong>
								</td>
								<td align='center'><strong>id</strong>
								</td>
                                <td align='center'><strong>ชื่อ</strong>
								</td>
                                <td align='center'><strong>หน่วยงาน</strong>
								</td>
                                <td align='center'><strong>กิจกรรม</strong>
								</td>
                                <td align='center'><strong>รายละเอียด</strong>
								</td>
                                <td align='center'><strong>ลบข้อมูล</strong>
								</td>

							</tr>
						</thead>
						<tbody>
							<?php
							$rec_sql = "SELECT dt_stamp,id_user,concat(firstname,' ',lastname) as u_name,depart,session,desc_log FROM (u_log INNER JOIN (user INNER JOIN depart ON user.id_depart = depart.id_depart ) on id_user = id) WHERE(CONCAT('d',dt_stamp,' ',firstname,' ',lastname,' ',id_user,' ',session,' ',desc_log) like '%$search%') ORDER by dt_stamp DESC LIMIT $start,$limit";

							$query = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn . " Q=" . $rec_sql));
							while ($arr = mysqli_fetch_array($query)) {
								$autoid = $arr['dt_stamp'];
							?>
								<tr>
									<td align='center'>
										<?php echo $arr['dt_stamp'] ?>
									</td>
									<td align='center'>
										<?php echo $arr['id_user'] ?>
									</td>
                                    <td align='center'>
										<?php echo $arr['u_name'] ?>
									</td>
                                    <td align='center'>
										<?php echo $arr['depart'] ?>
									</td>
                                    <td align='center'>
										<?php echo $arr['session'] ?>
									</td>
                                    <td align='center'>
										<?php echo $arr['desc_log'] ?>
									</td>

									<td align="center">
										
									<?php if ($_SESSION['s_sys'] == 1) { ?>
										<a href="#" title='ลบข้อมูล' class="cf_delete text-danger" data-id="<?php echo ($autoid) ?>" data-show="<?php echo (" [ " . $arr['dt_stamp'] . " ] " . $arr['session']) ?>" data-Search="<?php echo ($search) ?>"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
									<?php } else { ?>
										<a title='ไม่อนุญาต !' style="color:#b6b6b6;"><i class="fas fa-trash-alt"></i></a>
									<?php } ?>										
									</td>
								</tr>
							<?php } //===while($arr... 
							?>

						</tbody>
					</table>
					<nav aria-label="Page navigation">
					<?php $_link = "data_log?Search=$search&page="; ?>
						<ul class='pagination justify-content-center pagination-sm'>
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


	<script>
		$(document).on('click', '.cf_delete', function(e) {
			var show = $(this).data('show');
			var id = $(this).data('id');
			var sea = document.getElementById('Search').value;
			e.preventDefault();

			bootbox.confirm({
				title: 'ยืนยันการลบข้อมูล !!!',
				//size: 'small',
				message: 'ต้องการลบข้อมูล <b>' + show + '</b> ใช่หรือไม่?',
				buttons: {
					confirm: {
						label: '&nbsp; ใช่ &nbsp; ',
						className: 'btn-success'
					},
					cancel: {
						label: '&nbsp; ไม่ &nbsp;',
						className: 'btn-danger'
					}
				},
				callback: function(result) {
					if (result) {
						window.location.href = 'data_log.php?submit=DEL&show=OK&selid=' + id+'&Search='+ sea;
					}
				}
			});

		});

	</script>

</body>

</html>