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

	$prod = (isset($_POST['prod'])) ? $_POST['prod'] : '';
	$prodtype = (isset($_POST['prodtype'])) ? $_POST['prodtype'] : '';

if($submit=="OK"){ //================== INSERT OR UPDATE ====================
	if($Sel_id == ""){ //===== INSERT ==========
		$sqlck="SELECT * FROM product WHERE(prod_name = '$prod')";
		$resultCk = mysqli_query($dbconn, $sqlck) or die(mysqli_error($dbconn));
		$numck = mysqli_num_rows($resultCk);
			if($numck >= 1){
				echo ("<script>");
				echo("$(function(){bootbox.alert(' เบอร์ด้าย [".$prod."] มีอยู่แล้วในทะเบียน กรุณาระบุใหม่ !', function(){window.history.back();});})");
				echo ("</script>");
			}else{
				$strsql="INSERT INTO product SET prod_name='$prod',prod_type_id='$prodtype'";
				$rsInsert = mysqli_query($dbconn,$strsql) or die(mysqli_error($dbconn));
				//===== LOG ==========
				$descLog = "DataProd".$prod."_type".$prodtype;
				saveLog($_SESSION['s_id'],'_add',$descLog);
			}
	}else {  		//========= UPDATE ==========
		$sqlUp="UPDATE product SET prod_name='$prod',prod_type_id='$prodtype' WHERE(prod_id = '$Sel_id')";
		$rsUpdate = mysqli_query($dbconn,$sqlUp) or die(mysqli_error($dbconn));
		//===== LOG ==========
		$descLog = "DataProd".$prod."_id".$Sel_id."_type".$prodtype;
		saveLog($_SESSION['s_id'],'_edit',$descLog);
	}
}
if ($submit=="DEL"){ //====== DELETE ==========
		$sqldel="DELETE FROM product WHERE(prod_id = '$Sel_id')";
		$rsDel = mysqli_query($dbconn,$sqldel) or die(mysqli_error($dbconn));
		//===== LOG ==========
		$descLog = "DataProd"."_id".$Sel_id;
		saveLog($_SESSION['s_id'],'_del',$descLog);
}

?>
</head>

<body>
	
	<div class="container">
		<div class="row">
			<div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
				<span class="d-block p-2 bg-info text-white" align="center"><i class="fas fa-bacon" aria-hidden="true"></i> ข้อมูลเบอร์ด้าย</span>
			</div>

			<!-- ================================== Show Form ========================================= -->
			<?php
if($submit =="" or $show =="OK"){?>
			<div class="col-md-12">
				<form name="fmSearch" method="post" action="data_prod.php?show=OK" role='Search'>
					<div class="form-group row">
						<div class="input-group mb-2 col-md-12">
							<input name='Search' id="Search" type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo($search);?>' onFocus="this.value ='';">
							<div class="input-group-append">
								<button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
			$limit = '25';
			$rec_sql = "SELECT prod_id, prod_name, product.prod_type_id, prod_type FROM product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id WHERE(CONCAT(prod_name,' ',prod_type) like '%$search%')
			ORDER by prod_name ";

			$Qtotal = mysqli_query( $dbconn, $rec_sql )or die( mysqli_error($dbconn." Q=".$rec_sql) );
			$total_data = mysqli_num_rows( $Qtotal );
			$total_page = ceil( $total_data / $limit );

			if ( $page >= $total_page )$page = $total_page;
			if ( $page <= 0 or $page == '' ) {
				$start = 0;
				$page = 1;
			}

			$start = ( $page - 1 ) * $limit;

			$from = $start + 1;
			$to = $page * $limit;
			if ( $to > $total_data )$to = $total_data;
			?>
			<div class="col-md-12">
				<div class='alert alert-info' role='alert' align="center">
					<?php
					echo( $from . " - " . $to );
					echo( " ( จำนวน " . number_format( $total_data ) . " รายการ " );
					echo( " -- หน้าที่ " . number_format( $page ) . "/" );
					echo( number_format( $total_page ). " )" );

					?>
				</div>
			</div>

			<div class="col-md-12">
				<table class="table table-striped table-responsive">
					<thead>
						<tr>
							<td align='center'><strong>ID</strong>
							</td>
							<td align='center'><strong>เบอร์ด้าย</strong>
							</td>
							<td align='center'><strong>ประเภท</strong>
							</td>

							<td width="25%" align="center"><a href="data_prod.php?submit=Add&Search=<?php echo($search);?>" class='btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php
						$rec_sql = "SELECT prod_id, prod_name, product.prod_type_id, prod_type FROM product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id
						WHERE(CONCAT(prod_name,' ',prod_type) like '%$search%') ORDER by prod_name  LIMIT $start,$limit";

						$query = mysqli_query( $dbconn, $rec_sql )or die( mysqli_error($dbconn." Q=".$rec_sql) );
						while ( $arr = mysqli_fetch_array( $query ) ) {
							$autoid = $arr[ 'prod_id' ];
							?>
						<tr>
							<td align='center'>
								<?php echo $arr['prod_id'] ?>
							</td>
							<td align='center'>
								<?php echo $arr['prod_name'] ?>
							</td>
							<td align='center'>
								<?php echo $arr['prod_type'] ?>
							</td>

							<td align="center">
								<a href="data_prod.php?submit=Edit&show=&selid=<?php echo($autoid)?>&Search=<?php echo($search);?>" title='แก้ไขข้อมูล' class="text-info" ><i class="fas fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;

							<?php if($_SESSION['s_sys']== 1){?>
								<a href="#" title='ลบข้อมูล' class="cf_delete text-danger" data-id="<?php echo($autoid)?>" data-show="<?php echo(" [ ".$arr['prod_name']." ] ประเภท:".$arr['prod_type'])?>" ><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
							<?php }else{ ?>
								<a title='ไม่อนุญาต !' style="color:#b6b6b6;" ><i class="fas fa-trash-alt"></i></a>
							<?php } ?>								
							</td>
						</tr>
						<?php }//===while($arr... ?>

					</tbody>
				</table>
				<nav aria-label="Page navigation">
					<?php $_link = "data_prod?Search=".$search."&page="; ?>
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
			<?php mysqli_close($dbconn); }?>

			<!-- ================================== Add Form ========================================= -->
			<?php  if($submit=="Add"){?>
			<div class="col-md-12">
				<form name="fmAdd" id="frmAdd" method="post" action="data_prod.php?submit=OK&show=OK&Search=<?php echo($search)?>">

					<div class="form-group row">
						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text " for="prod" style="background-color: aquamarine" >เบอร์ด้าย</label>
							</div>
							<input name="prod" type="text" id="prod" class="form-control" required>
						</div>

						<div class="input-group mb-2 col-md-6">
							<div class="input-group-prepend">
								<label class="input-group-text" for="prodtype" style="background-color: aquamarine">ประเภทด้าย</</label>
							</div>
							<select class="custom-select" id="prodtype" name="prodtype">
								<option selected>---</option>
								<?php
								$rstTemp = mysqli_query( $dbconn, 'SELECT * FROM prod_type' );
								while ( $arr_1 = mysqli_fetch_array( $rstTemp ) ) {	?>
								<option value="<?php echo($arr_1['prod_type_id'])?>">
									<?php echo($arr_1['prod_type'])?>
								</option>
								<?php }?>
							</select>
						</div>
					</div>

					<div class='form-group'>
						<div class='col-md-12' align="center">
							<button type='button' onClick="submitAdd()" class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
							<button type='button' class='btn btn-danger' onClick="document.location.href='data_prod.php?show=OK&Search=<?php echo($search)?>'">ยกเลิก</button>
						</div>
					</div>


				</form>
			</div>
			<?php }?>

			<!-- ================================== Edit Form ========================================= -->
			<?php
			if ( $submit == "Edit" ) {
				$sqlEdit = "SELECT * FROM product WHERE( prod_id = '$Sel_id')";
				$rsEdit = mysqli_query( $dbconn, $sqlEdit )or die( mysqli_error($dbconn." Q=".$rec_sql) );
				$rowEdit = mysqli_fetch_array( $rsEdit );
				?>
			<div class="col-md-12">
				<form name="fmEdit" id="frmEdit" method="post" action="data_prod.php?submit=OK&show=OK&selid=<?php echo($Sel_id)?>&Search=<?php echo($search);?>" >

					<div class="form-group row">

						<div class="input-group mb-2 col-md-2">
							<div class="input-group-prepend">
								<label class="input-group-text " for="prodid" >รหัส</label>
							</div>
							<input name="prodid" type="text" id="prodid" value="<?php echo($rowEdit['prod_id']) ?>" class="form-control"  disabled>
						</div>

						<div class="input-group mb-2 col-md-5">
							<div class="input-group-prepend">
								<label class="input-group-text " for="prod" >เบอร์ด้าย</label>
							</div>
							<input name="prod" type="text" id="prod" value="<?php echo($rowEdit['prod_name']) ?>" class="form-control" >
						</div>

						<div class="input-group mb-2 col-md-5">
							<div class="input-group-prepend">
								<label class="input-group-text" for="prodtype">ประเภท</</label>
							</div>
							<select class="custom-select" id="prodtype" name="prodtype">
								<option  >---</option>
								<?php
								$rstTemp = mysqli_query( $dbconn, 'SELECT * FROM prod_type' );
								while ( $arr_1 = mysqli_fetch_array( $rstTemp ) ) {	?>
								<option value="<?php echo($arr_1['prod_type_id'])?>" <?php if($rowEdit[ 'prod_type_id']==$arr_1[ 'prod_type_id']){echo ' selected';} ?>>
									<?php echo($arr_1['prod_type'])?>
								</option>
								<?php }?>
							</select>
						</div>

					</div>

					<div class='form-group'>
						<div class='col-md-12' align="center">
							<button type='submit' class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
							<button type='button' class='btn btn-danger' onClick="document.location.href='data_prod.php?show=OK&Search=<?php echo($search)?>'">ยกเลิก</button>
						</div>
					</div>
				</form>
			</div>
			<?php mysqli_close($dbconn); }?>

		</div>
	</div>


	<script>

		$( document ).on( 'click', '.cf_delete', function ( e ) {
			var show = $( this ).data( 'show' );
			var id = $( this ).data( 'id' );
			var sea = '<?php echo($search);?>';
			e.preventDefault();

			bootbox.confirm( {
				title: 'ยืนยันการลบข้อมูล !!!',
				//size: 'small',
				message: 'ต้องการลบข้อมูล <b>' + show + '</b> ใช่หรือไม่?',
				buttons: {
					confirm: {
						label: '&nbsp; ใช่ &nbsp;',
						className: 'btn-success'
					},
					cancel: {
						label: '&nbsp; ไม่ &nbsp;',
						className: 'btn-danger'
					}
				},
				callback: function ( result ) {
					if ( result ) {
						window.location.href = 'data_prod.php?submit=DEL&show=OK&selid='+id+'&Search='+sea;
					}
				}
			} );

		} );

function submitAdd() {
	var s1 = document.getElementsByName('prod');
		s1 = s1.item(0).value;
	var s2 = document.getElementsByName('prodtype');
		s2 = s2.item(0).value;

	    if((s1 == '')||(s2 == '---')){

			$(function(){bootbox.alert("โปรดระบุข้อมูลให้ครบถ้วน !", function(){});})
		} else{
			$( '#frmAdd' ) . submit();
		}

}
	</script>

</body>
</html>
