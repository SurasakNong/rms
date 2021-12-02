<!doctype html>
<html lang="en">
<head>
	<title>RM-REPORT</title>
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
<style>
	.fmsel option{
		height:28px; 
		margin-top: 0px; 
		left:10px;
		font: normal 400 14px/10px var(--prompt);
	}
</style>
	

<?php
error_reporting(E_ALL ^ E_NOTICE);
include 'Menu_admin.php';
include_once 'dbConn.php';

$date_now = date("Y-m-d");
$search = (isset($_GET['Search'])) ? $_GET['Search'] : ((isset($_POST['Search'])) ? $_POST['Search'] : '');
$dp_Acc = (isset($_SESSION['s_dpid']))? $_SESSION['s_dpid']:'';  // รหัสผู้ใช้งาน

function DateYmd($date){
$get_date = explode("/",$date);
return $get_date['2']."-".$get_date['1']."-".$get_date['0'];
}

function DatedmY($date){
$get_date = explode("-",$date);
return $get_date['2']."/".$get_date['1']."/".$get_date['0'];
}


?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px'> 
				<span class="d-block p-2 bg-primary text-white rounded-lg"  align="center"><i class="far fa-newspaper fa-lg" aria-hidden="true"></i> รายงาน</span>
			</div>

			<!-- ================================== Report Form ========================================= -->

			<div class="col-md-12">
				<form name="frmrep" id="frmrep" method="post" action='' target="_blank" role='report'>
					<div class="form-group row">

						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text " style="width: 90px;" for="picker">วันที่</label>
							</div>
							<input name="datefm" type="text" value='<?php echo(DatedmY($date_now))?>' id="picker" class="form-control">
						</div>

						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text " style="width: 90px;" for="picker2">ถึง</label>
							</div>
							<input name="dateto" type="text" value='<?php echo(DatedmY($date_now))?>' id="picker2" class="form-control">
						</div>

						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text" style="width: 90px;" for="dp">หน่วยงาน</</label>
							</div>
							<select class="custom-select" id="dp" name="dp">
								<option value="0" selected>---</option>
								<?php
								$rstTemp = mysqli_query( $dbconn, 'SELECT * FROM depart' );
								while ( $arr_dp = mysqli_fetch_array( $rstTemp ) ) {
									?>
								<option value="<?php echo($arr_dp['id_depart'])?>" <?php if($dp_Acc == $arr_dp['id_depart']){echo('selected');}?> >
									<?php echo($arr_dp['depart'])?>
								</option>
								<?php }?>
							</select>
						</div>


						<div class="input-group mb-2 col-md-6">
							<input name='search' type="text" class="form-control" placeholder="คำค้นหา...$บิล..#กล่อง..#sช่อง..pวันบรรจุ..rวันรับ..tวันจ่าย." value='' onFocus="this.value ='';">
						</div>						

						<div class="input-group mb-2 col-md-12">
							<label for="selrep">เลือกรายงาน :</label>
							<select class="fmsel form-control mb-2" size="10" id="selrep" name="selrep">
								<option value="rep_rmpackdetail">1.) รายงานบรรจุวัตถุดิบ</option>
								<option value="rep_rmpackgroup">2.) รายงานบรรจุวัตถุดิบ-สรุป</option>
								<option value="rep_rmrecdetail">3.) รายงานรับเข้า</option>
								<option value="rep_rmrecgroup">4.) รายงานรับเข้า-สรุป</option>
								<option value="rep_rmstkdetail">5.) รายงานคงเหลือ</option>
								<option value="rep_rmstkgroup">6.) รายงานคงเหลือ-สรุป</option>
								<option value="rep_rmtrandetail">7.) รายงานจ่ายโอน</option>
								<option value="rep_rmtrangroup">8.) รายงานจ่ายโอน-สรุป</option>
								<option value="rep_rmpackbarcode">9.) พิมพ์ใบข้างกล่อง</option>
							</select>
						</div>

						<div class='col-md-12' align="center">
							<button type='button' title="แสดงรายงาน" style="width: 80px; margin-right:20px;" onClick="submitRep()" class='btn btn-success'>รายงาน</button>
							<button type='button' title="กลับหน้าหลัก" style="width: 80px;" class='btn btn-warning' onClick="document.location.href='intro'">กลับ</button>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>


	<script>
		var today = new Date();
		var dd = String( today.getDate() ).padStart( 2, '0' );
		var mm = String( today.getMonth() + 1 ).padStart( 2, '0' ); //January is 0!
		var yyyy = today.getFullYear();
		today = yyyy + '-' + mm + '-' + dd;

		jQuery.datetimepicker.setLocale( 'th' );

		$( '#picker' ).datetimepicker( {
			timepicker: false,
			datepicker: true,
			format: 'd/m/Y',
			//value:today,
			mask: true
		} )

		$( '#picker2' ).datetimepicker( {
			timepicker: false,
			datepicker: true,
			format: 'd/m/Y',
			//value:today,
			mask: true
		} )

function submitRep() {
	var s1 = document.getElementsByName('selrep');
		s1 = s1.item(0).value;
	var nnn = 0;
	    if(s1 == ""){nnn++;}
		if(nnn > 0){
			$(function(){bootbox.alert("กรุณาเลือกรายงานที่ต้องการ !", function(){});})
		} else{
			document.getElementById('frmrep').action = s1;
			$('#frmrep').attr('target', '_blank');
			$( '#frmrep' ) . submit();
		}
}
	</script>

</body>
</html>
