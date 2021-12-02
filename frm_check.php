<!doctype html>
<html lang="en">
<head>
	<title>RM-CHECK</title>
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
$show = (isset($_GET['show'])) ? $_GET['show'] : '';
$search = (isset($_GET['Search'])) ? $_GET['Search'] : ((isset($_POST['Search'])) ? $_POST['Search'] : '');

		$_sta = '---';
		$_boxname = '---';
		$_proname = '---';
		$_pkdate = '-';
		$_pkbill = '-';
		$_pkdp = '-';
		$_recdate = '-';
		$_recbill = '-';
		$_recdp = '-';
		$_shelf = '-';
		$_trandate = '-';
		$_tranbill = '-';
		$_trandp = '-';
		$_nroll = 0;
		$_kg = 0;
		$_val = 0;


function DatedmY($date){
	if(strlen($date)>2) {
		$get_date = explode("-",$date);	
		$aa = $get_date['2']."/".$get_date['1']."/".$get_date['0'];
	}else{$aa ='--/--/---- ';}
	return $aa;
}

?>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-primary text-white" align="center"><i class="fas fa-barcode fa-lg" aria-hidden="true"></i> ตรวจสอบข้อมูลวัตถุดิบ</span>
			</div>

			<?php
if($submit =="" or $show =="OK"){?>  <!-- =========================== Show Form ================================== -->
			<div class="col-md-12">
				<form name="fmSearch" id="fmSearch" method="post" action="frm_check.php?show=OK" role='Search'>
					<div class="form-group row">
						<div class="input-group mb-2 col-md-12">
							<input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo($search);?>' onFocus="this.value ='';">
							<div class="input-group-append">
								<button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php

			$c_sql = "SELECT CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),'-',LPAD(box_id,3,'0')) AS boxname, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prodname, pack_date, pack_bill,d1.depart as dp_pack, rec_date, rec_bill,d2.depart AS dp_rec,tran_date,bill_no,d3.depart AS dp_tran, roll_n, prod_kg, val, shelf_name , status
FROM ((((((pro_pack AS pp INNER JOIN depart AS d1 ON from_dp = d1.id_depart
						LEFT JOIN depart AS d2 ON to_dp = d2.id_depart)
					LEFT JOIN depart AS d3 ON tranto_dp = d3.id_depart)
					LEFT JOIN shelf ON pp.shelf_id = shelf.shelf_id)
				INNER JOIN product ON pp.prod_id = product.prod_id)
      INNER JOIN color ON pp.col_id = color.col_id)
	INNER JOIN rm_code ON pp.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pp.roll_id = roll_type.roll_id
 WHERE(CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0')) like '%$search%')";

			$result = mysqli_query($dbconn,$c_sql);

	if ( mysqli_num_rows($result) == 1 ) {
		$row = mysqli_fetch_array($result);
		$_boxname = $row['boxname'];
		$_proname = $row['prodname'];
		$_pkdate = DatedmY($row['pack_date']);
		$_pkbill = $row['pack_bill'];
		$_pkdp = $row['dp_pack'];
		$_recdate = DatedmY($row['rec_date']);
		$_recbill = $row['rec_bill'];
		$_recdp = $row['dp_rec'];
		$_shelf = $row['shelf_name'];
		$_trandate = DatedmY($row['tran_date']);
		$_tranbill = $row['bill_no'];
		$_trandp = $row['dp_tran'];
		$_nroll = $row['roll_n'];
		$_kg = number_format( $row['prod_kg'], 2 );
		$_val = number_format( $row['val'], 2 );

		if($row['status']==0){$_sta ="บรรจุ";}elseif($row['status']==1){$_sta ="รับเข้า";}elseif($row['status']==2){$_sta ="จัดเก็บ";}else{$_sta ="ส่ง";}


	} elseif($search!=''){
		echo ("<script>");
		echo("$(function(){bootbox.alert(' ไม่พบข้อมูล !', function(){});})");
		echo ("</script>");
	}
	mysqli_close($dbconn);
			?>
<!-- ========================================  Form ======================================== -->
		<div class="col-md-12">
			<form name="fmTable" id="fmTable">
				<div class="form-group row">

					<div class="col-md-12">
						<table class="table table-striped">
							<thead>
								<tr>
									<td colspan="2" width="400" height="20" align="center" class="lbg" style="font-size: 24px; font-weight: 600; background-color: yellow;" ><?=$_boxname.' (สถานะ:'.$_sta.')'?></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td width="100" align="center" valign="middle"><strong>วัตถุดิบ</strong></td>
									<td width="300" align="left" valign="middle"><?php echo $_proname;?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>หลอด</strong></td>
									<td width="300" align="left" valign="middle"><?=$_nroll?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>กิโลกรัม</strong></td>
									<td width="300" align="left" valign="middle"><?=$_kg?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>Ind.@</strong></td>
									<td width="300" align="left" valign="middle"><?=$_val.' ( '.number_format( ($_val*$_kg), 2 ).' )'?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>บรรจุ</strong></td>
									<td width="300" align="left" valign="middle"><?=$_pkdate.'-'.$_pkbill.' ('.$_pkdp.')'?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>รับเข้า</strong></td>
									<td width="300" align="left" valign="middle"><?=$_recdate.'-'.$_recbill.' ('.$_recdp.') ==> '.$_shelf?></td>
								</tr>
								<tr>
									<td width="100" align="center" valign="middle"><strong>ส่ง</strong></td>
									<td width="300" align="left" valign="middle"><?=$_trandate.'-'.$_tranbill.' ('.$_trandp.')'?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
		</div>
	</div>
<?php mysqli_close($dbconn); }?>
</body>
</html>
