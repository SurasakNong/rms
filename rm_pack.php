<!doctype html>
<html lang="en">
<head>
    <title>RM-PACK</title>
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
    $Sel_Boxid = (isset($_GET['selboxid'])) ? $_GET['selboxid'] : '';
    $date_pack = (isset($_GET['packdate'])) ? $_GET['packdate'] : ((isset($_POST['date_pack'])) ? DateYmd($_POST['date_pack']) : date("Y-m-d"));
    $p_bill = (isset($_GET['pack_bill'])) ? $_GET['pack_bill'] : ((isset($_POST['pack_bill'])) ? $_POST['pack_bill'] : 1);
    $to_dp = (isset($_GET['to_dp'])) ? $_GET['to_dp'] : ((isset($_POST['to_dp'])) ? $_POST['to_dp'] : '---');
    if ($to_dp != '---') {
        $_where = "AND to_dp ='$to_dp'";
    } else {
        $_where = '';
    }
    $dp_frm = (isset($_SESSION['s_dpid'])) ? $_SESSION['s_dpid'] : '';  // รหัสผู้ใช้งาน
    $boxid = (isset($_POST['boxid'])) ? $_POST['boxid'] : '';
    $prod = (isset($_POST['prod'])) ? $_POST['prod'] : '';
    $colid = (isset($_POST['colid'])) ? $_POST['colid'] : '';
    $rm = (isset($_POST['rm'])) ? $_POST['rm'] : '';
    $roll = (isset($_POST['roll'])) ? $_POST['roll'] : '';
    $nroll = (isset($_POST['numbox'])) ? $_POST['numbox'] : '';
    $prodkg = (isset($_POST['kg'])) ? $_POST['kg'] : '';
    $in_dex = (isset($_POST['in_dex'])) ? $_POST['in_dex'] : '';
    $maxBoxid = (isset($_GET['mbid'])) ? $_GET['mbid'] : '';
    $_prod = (isset($_SESSION['ss_prod'])) ? $_SESSION['ss_prod'] : '';
    $_col = (isset($_SESSION['ss_col'])) ? $_SESSION['ss_col'] : '';
    $_rm = (isset($_SESSION['ss_rm'])) ? $_SESSION['ss_rm'] : '';
    $_roll = (isset($_SESSION['ss_roll'])) ? $_SESSION['ss_roll'] : '';
    $_ind = (isset($_SESSION['ss_ind'])) ? $_SESSION['ss_ind'] : '';
    $box_id_old = (isset($_GET['bo'])) ? $_GET['bo'] : '0';
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
    function id_ok($pd, $dp, $id, $dbc)
    {
        $sqlck = "SELECT * FROM pro_pack WHERE(pack_date='$pd' AND from_dp='$dp' AND box_id='$id')";
        $resultCk = mysqli_query($dbc, $sqlck) or die(mysqli_error($dbc . " Q=" . $sqlck));
        $numck = mysqli_num_rows($resultCk);
        return $numck;
    }
    if ($submit == "OK") { //================== INSERT OR UPDATE ====================
        if ($Sel_Boxid == "") { //===== INSERT ==========
            $yy = id_ok($date_pack, $dp_frm, $boxid, $dbconn);
            if ($yy >= 1) {
                echo ("<script>");
                echo ("$(function(){bootbox.alert(' กล่อง [#" . $boxid . "] ของวันที่ " . DatedmY($date_pack) . "  มีอยู่แล้ว  กรุณาเพิ่มใหม่อีกครั้ง !', function(){window.history.back();});})");
                echo ("</script>");
            } else {
                $strsql = "INSERT INTO pro_pack SET pack_date='$date_pack',from_dp='$dp_frm', to_dp='$to_dp', pack_bill='$p_bill', box_id='$boxid', prod_id='$prod', col_id='$colid', rm_id='$rm', roll_id='$roll', roll_n='$nroll', prod_kg='$prodkg', val='$in_dex',status='0'";
                $rsInsert = mysqli_query($dbconn, $strsql) or die(mysqli_error($dbconn . " Q=" . $strsql));
                $_SESSION['ss_prod'] = $prod;
                $_SESSION['ss_col'] = $colid;
                $_SESSION['ss_rm'] = $rm;
                $_SESSION['ss_roll'] = $roll;
                $_SESSION['ss_ind'] = $in_dex;
                //===== LOG ==========
                $descLog = "pack" . $date_pack . "_$" . $p_bill . "_dp" . $dp_frm . "_box" . $boxid . "_to" . $to_dp;
                saveLog($_SESSION['s_id'], '_add', $descLog);
            }
        } else {        //===== UPDATE ==========
            if ($box_id_old == $boxid) {  //===== ตรวจสอบเปลี่ยนข้อมูลกล่องเดิมหรือไม่ (ใช่)
                $sqlUp = "UPDATE pro_pack SET box_id='$boxid',prod_id='$prod',col_id='$colid',rm_id='$rm',roll_id='$roll',roll_n='$nroll',prod_kg='$prodkg',val='$in_dex',status='0', to_dp='$to_dp'  WHERE(pack_date='$date_pack' AND pack_bill='$p_bill' AND from_dp='$dp_frm' AND box_id='$Sel_Boxid')";
                $rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));
                //===== LOG ==========
                $descLog = "pack" . $date_pack . "_$" . $p_bill . "_dp" . $dp_frm . "_box" . $boxid;
                saveLog($_SESSION['s_id'], '_edit', $descLog);
            } else { //===== ตรวจสอบเปลี่ยนข้อมูลกล่องเดิมหรือไม่ (ไม่)
                $yy = id_ok($date_pack, $dp_frm, $boxid, $dbconn);
                if ($yy >= 1) {    //===== ตรวจสอบมีเลขกล่องซ้ำหรือไม่ (ซ้ำ)
                    echo ("<script>");
                    echo ("$(function(){bootbox.alert(' กล่อง [#" . $boxid . "] ของวันที่ " . DatedmY($date_pack) . "  มีอยู่แล้ว  กรุณาเพิ่มใหม่อีกครั้ง !', function(){window.history.back();});})");
                    echo ("</script>");
                } else { //===== ตรวจสอบมีเลขกล่องซ้ำหรือไม่ (ไม่ซ้ำ)
                    $sqlUp = "UPDATE pro_pack SET box_id='$boxid',prod_id='$prod',col_id='$colid',rm_id='$rm',roll_id='$roll',roll_n='$nroll',prod_kg='$prodkg',val='$in_dex',status='0', to_dp='$to_dp'  WHERE(pack_date='$date_pack' AND pack_bill='$p_bill' AND from_dp='$dp_frm' AND box_id='$Sel_Boxid')";
                    $rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn . " Q=" . $sqlUp));
                    //===== LOG ==========
                    $descLog = "pack" . $date_pack . "_$" . $p_bill . "_dp" . $dp_frm . "_box" . $boxid;
                    saveLog($_SESSION['s_id'], '_edit', $descLog);
                }
            }
        }
    }
    if ($submit == "DEL") { //===== DELETE ==========
        $sqldel = "DELETE FROM pro_pack WHERE(pack_date='$date_pack' AND pack_bill='$p_bill' AND from_dp='$dp_frm' AND box_id = '$Sel_Boxid')";
        $rsDel = mysqli_query($dbconn, $sqldel) or die(mysqli_error($dbconn . " Q=" . $sqldel));
        //===== LOG ==========
        $descLog = "pack" . $date_pack . "_$" . $p_bill . "_dp" . $dp_frm . "_box" . $Sel_Boxid;
        saveLog($_SESSION['s_id'], '_del', $descLog);
    }
    ?>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style='margin-bottom: 8px; font-size:18px'>
                <span class="d-block p-2 bg-success text-white" align="center"><?php echo $_SESSION['s_depart'] ?> <i class="fa fa-cube fa-lg" aria-hidden="true"></i> บรรจุวัตถุดิบ </span>
            </div>
            <!-- ================================== Show Form ========================================= -->
            <?php
            if ($submit == "" or $show == "OK") { ?>
                <div class="col-md-12">
                    <form name="fmSearch" id="fmSearch" method="post" action="rm_pack?show=OK&strSearch=Y" role='Search'>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width: 70px;" for="picker">วันที่</label>
                                </div>
                                <input name="date_pack" type="text" value='<?php echo (DatedmY($date_pack)) ?>' id="picker" class="form-control" tabIndex="1" onchange="submitS()">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="pack_bill" style="background-color: aquamarine; width: 70px;">เลขบิล</label>
                                </div>
                                <input name="pack_bill" type="number" id="pack_bill" onchange="submitS()" class="form-control input-no-spinner" value="<?php echo $p_bill ?>" required tabIndex="2">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="to_dp" style="width: 70px;">ผู้รับ</< /label>
                                </div>
                                <select class="custom-select" id="to_dp" name="to_dp" onchange="submitS()" tabIndex="3">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
                                    while ($arr_2 = mysqli_fetch_array($rstTemp)) {
                                    ?>
                                        <option value="<?php echo ($arr_2['id_depart']) ?>" <?php if ($to_dp == $arr_2['id_depart']) {
                                                                                                echo ('selected');
                                                                                            } ?>>
                                            <?php echo ($arr_2['depart']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <input name='Search' type="text" class="form-control" placeholder="คำค้นหา..." value='<?php echo ($search) ?>' onFocus="this.value ='';">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type='submit'><i class="fa fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
                $limit = '60';
                //===== เลขกล่องวัตถุดิบล่าสุด ========
                $max_sql = "SELECT * FROM pro_pack  WHERE(pack_date='$date_pack' AND from_dp='$dp_frm')";
                $Qtotal = mysqli_query($dbconn, $max_sql) or die(mysqli_error($dbconn . " Q=" . $max_sql));
                $maxBoxid = 0;
                while ($max_resual = mysqli_fetch_array($Qtotal)) {
                    if ($maxBoxid <= $max_resual['box_id']) {
                        $maxBoxid = $max_resual['box_id'];
                    }
                }
                $cal_sql = "SELECT pack_date, pack_bill ,box_id, roll_n, prod_kg, from_dp, prod_name, col_code, rm_code, roll_type, val, pp.status, depart
                    FROM((((pro_pack AS pp INNER JOIN depart ON to_dp = id_depart)
                    INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pp.prod_id = product.prod_id)
                    INNER JOIN color ON pp.col_id = color.col_id)
                    INNER JOIN rm_code ON pp.rm_id = rm_code.rm_id)
                    INNER JOIN roll_type ON pp.roll_id = roll_type.roll_id WHERE(pack_date='$date_pack' AND from_dp='$dp_frm' AND pack_bill='$p_bill' " . $_where . " AND CONCAT('#',LPAD(box_id,3,'0'),' ',IF(status = 0,'+n','+y'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%')";
                $Qtotal = mysqli_query($dbconn, $cal_sql) or die(mysqli_error($dbconn . " Q=" . $cal_sql));
                $total_data = mysqli_num_rows($Qtotal);
                $total_page = ceil($total_data / $limit);
                $numRoll = 0;
                $Kg = 0;
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
                                <td align='center'><strong>ผู้รับ</strong>
                                </td>
                                <td align='center'><a title='พิมพ์บาร์โคด' class="cf_barcode" href="#"><i class="fas fa-tags fa-lg" aria-hidden="true"></i></a>
                                </td>
                                <td align='center'><strong>รายการ</strong>
                                </td>
                                <td align='center'><strong>หลอด</strong>
                                </td>
                                <td align='center'><strong>จำนวน</strong>
                                </td>
                                <td align='center'><strong>น้ำหนัก</strong>
                                </td>
                                <td align='center'><strong>Index</strong>
                                </td>
                                <td width="20%" align="center"><a title='เพิ่มข้อมูล' href="#" data-mbox="<?php echo ($maxBoxid + 1) ?>" data-ss="<?php echo ($search) ?>" class='cf_add btn btn-success btn-sm' role='button'>&nbsp;เพิ่ม&nbsp;</a>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $show_sql = "SELECT pack_date, pack_bill, box_id, from_dp, pp.prod_id, pp.col_id, pp.rm_id, pp.roll_id, roll_n, prod_kg, prod_name, col_code, rm_code, roll_type, val, pp.status, depart
                        FROM((((pro_pack AS pp INNER JOIN depart ON to_dp = id_depart)
                        INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pp.prod_id = product.prod_id)
                        INNER JOIN color ON pp.col_id = color.col_id)
                        INNER JOIN rm_code ON pp.rm_id = rm_code.rm_id)
                        INNER JOIN roll_type ON pp.roll_id = roll_type.roll_id WHERE(pack_date='$date_pack' AND from_dp='$dp_frm' AND pack_bill='$p_bill' " . $_where . " AND CONCAT('#',LPAD(box_id,3,'0'),' ',IF(status = 0,'+n','+y'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%') ORDER BY (box_id*1) DESC LIMIT $start,$limit";
                            $query = mysqli_query($dbconn, $show_sql) or die(mysqli_error($dbconn . " Q=" . $show_sql));
                            while ($arr = mysqli_fetch_array($query)) {
                                $autoBoxid = $arr['box_id'];
                            ?>
                                <tr>
                                    <td align='center'>
                                        <?php echo $arr['depart'] ?>
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
                                    <td align='right'>
                                        <?php echo number_format($arr['val'], 2) ?>
                                    </td>
                                    <td align="center">
                                        <?php if ($arr['status'] == 0) { ?>
                                            <a href="#" title='แก้ไขข้อมูล' class="cf_edit text-info" data-id="<?php echo ($autoBoxid) ?>"><i class="fas fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;

                                            <a href="#" title='ลบข้อมูล' class="cf_delete text-danger" data-id="<?php echo ($autoBoxid) ?>" data-show="<?php echo (" กล่อง [ #" . $arr['box_id'] . "] ของวันที่ " . datedmY($date_pack)) ?>"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
                                        <?php } else { ?>

                                            <a style="color:lightgray"><i class="fas fa-edit" aria-hidden="true"></i>&nbsp;&nbsp;<i class="fas fa-trash-alt" aria-hidden="true"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } //===แสดงข้อมูลในตาราง
                            ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation">
					<?php $_link = "rm_pack?Search=$search&packdate=$date_pack&pack_bill=$p_bill&to_dp=$to_dp&page="; ?>
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
                    <form name="fmAdd" id="frmAdd" method="post" action="rm_pack?submit=OK&show=OK&packdate=<?php echo ($date_pack) ?>&pack_bill=<?php echo ($p_bill) ?>&to_dp=<?php echo ($to_dp) ?>&Search=<?php echo ($search) ?>">
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="boxid" style="background-color: aquamarine; width:90px;">เลขกล่อง</label>
                                </div>
                                <input name="boxid" type="number" value="<?php echo ($maxBoxid) ?>" id="boxid" class="form-control input-no-spinner" step="1" required="required" tabIndex="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="prod">เบอร์ด้าย</label>
                                </div>
                                <select class="custom-select" id="prod" name="prod" tabIndex="2">
                                    <option <?php if ($_prod != '') {
                                                echo (' selected');
                                            } ?>>---</option>
                                    <?php
                                    $rstTemp3 = mysqli_query($dbconn, 'SELECT * FROM product ORDER BY prod_name');
                                    while ($arr_3 = mysqli_fetch_array($rstTemp3)) {
                                    ?>
                                        <option value="<?php echo ($arr_3['prod_id']) ?>" <?php if ($_prod == $arr_3['prod_id']) {
                                                                                                echo (' selected');
                                                                                            } ?>>
                                            <?php echo ($arr_3['prod_name']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="colid">สี</label>
                                </div>
                                <select class="custom-select" id="colid" name="colid" tabIndex="3">
                                    <option <?php if ($_col != '') {
                                                echo (' selected');
                                            } ?>>---</option>
                                    <?php
                                    $rstTemp4 = mysqli_query($dbconn, 'SELECT * FROM color ORDER BY col_code');
                                    while ($arr_4 = mysqli_fetch_array($rstTemp4)) {
                                    ?>
                                        <option value="<?php echo $arr_4['col_id'] ?>" <?php if ($_col == $arr_4['col_id']) {
                                                                                            echo (' selected');
                                                                                        } ?>>
                                            <?php echo $arr_4['col_code'] . " " . $arr_4['col_name'] . " " . $arr_4['col_desc'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="rm">วัตถุดิบ</< /label>
                                </div>
                                <select class="custom-select" id="rm" name="rm" tabIndex="4">
                                    <option <?php if ($_rm != '') {
                                                echo (' selected');
                                            } ?>>---</option>
                                    <?php
                                    $rstTemp5 = mysqli_query($dbconn, 'SELECT * FROM rm_code ORDER BY rm_code');
                                    while ($arr_5 = mysqli_fetch_array($rstTemp5)) {
                                    ?>
                                        <option value="<?php echo $arr_5['rm_id'] ?>" <?php if ($_rm == $arr_5['rm_id']) {
                                                                                            echo (' selected');
                                                                                        } ?>>
                                            <?php echo $arr_5['rm_code'] . " " . $arr_5['rm_desc'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="roll">ชนิดหลอด</label>
                                </div>
                                <select class="custom-select" id="roll" name="roll" tabIndex="5">
                                    <option <?php if ($_roll != '') {
                                                echo (' selected');
                                            } ?>>---</option>
                                    <?php
                                    $rstTemp6 = mysqli_query($dbconn, 'SELECT * FROM roll_type ORDER BY roll_type');
                                    while ($arr_6 = mysqli_fetch_array($rstTemp6)) {
                                    ?>
                                        <option value="<?php echo $arr_6['roll_id'] ?>" <?php if ($_roll == $arr_6['roll_id']) {
                                                                                            echo (' selected');
                                                                                        } ?>>
                                            <?php echo $arr_6['roll_type'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="numbox" style="background-color: aquamarine; width:110px;">จำนวนหลอด</label>
                                </div>
                                <input name="numbox" type="number" id="numbox" value="" class="form-control" placeholder="0" step="1" tabIndex="6">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="kg" style="background-color: aquamarine; width:110px;">น้ำหนัก(ก.ก.)</label>
                                </div>
                                <input name="kg" type="number" value="" id="kg" class="form-control" step="0.01" placeholder="0.00" tabIndex="7">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="in_dex" style="background-color: aquamarine; width:110px;">Index @</label>
                                </div>
                                <input name="in_dex" type="number" value="<?php echo $_ind ?>" id="in_dex" class="form-control" step="0.01" placeholder="0.00" tabIndex="8">
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class='col-md-12' align="center">
                                <button type='button' onClick="submitAdd()" class='btn btn-success'>บันทีก</button>
                                &nbsp;&nbsp;
                                <button type='button' class='btn btn-danger' onClick="document.location.href='rm_pack?show=OK&packdate=<?php echo ($date_pack) ?>&pack_bill=<?php echo ($p_bill) ?>&to_dp=<?php echo ($to_dp) ?>&Search=<?php echo ($search) ?>'">ยกเลิก</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
            <!-- ================================== Edit Form ========================================= -->
            <?php
            if ($submit == "Edit") {
                $sqlEdit = "SELECT * FROM pro_pack WHERE(pack_date = '$date_pack' AND from_dp='$dp_frm' AND pack_bill='$p_bill' AND box_id = '$Sel_Boxid')";
                $rsEdit = mysqli_query($dbconn, $sqlEdit) or die(mysqli_error($dbconn . " Q=" . $sqlEdit));
                $rowEdit = mysqli_fetch_array($rsEdit);
                $box_id_old = $rowEdit['box_id'];
            ?>
                <div class="col-md-12">
                    <form name="fmEdit" id="frmEdit" method="post" action="rm_pack?submit=OK&show=OK&selboxid=<?php echo ($Sel_Boxid) ?>&packdate=<?php echo ($date_pack) ?>&pack_bill=<?php echo ($p_bill) ?>&bo=<?php echo ($box_id_old) ?>&Search=<?php echo ($search) ?>">
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="boxid" style="background-color: aquamarine; width:90px;">เลขกล่อง</< /label>
                                </div>
                                <input name="boxid" type="number" value="<?php echo ($rowEdit['box_id']) ?>" id="boxid" class="form-control input-no-spinner" step="1" required="required">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="to_dp">ผู้รับ</< /label>
                                </div>
                                <select class="custom-select" id="to_dp" name="to_dp">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp = mysqli_query($dbconn, 'SELECT * FROM depart');
                                    while ($arr_2 = mysqli_fetch_array($rstTemp)) {
                                    ?>
                                        <option value="<?php echo ($arr_2['id_depart']) ?>" <?php if ($to_dp == $arr_2['id_depart']) {
                                                                                                echo ('selected');
                                                                                            } ?>>
                                            <?php echo ($arr_2['depart']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="prod">เบอร์ด้าย</< /label>
                                </div>
                                <select class="custom-select" id="prod" name="prod">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp3 = mysqli_query($dbconn, 'SELECT * FROM product ORDER BY prod_name');
                                    while ($arr_3 = mysqli_fetch_array($rstTemp3)) {
                                    ?>
                                        <option value="<?php echo ($arr_3['prod_id']) ?>" <?php if ($rowEdit['prod_id'] == $arr_3['prod_id']) echo 'selected'; ?>>
                                            <?php echo ($arr_3['prod_name']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="colid">สี</label>
                                </div>
                                <select class="custom-select" id="colid" name="colid">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp4 = mysqli_query($dbconn, 'SELECT * FROM color ORDER BY col_code');
                                    while ($arr_4 = mysqli_fetch_array($rstTemp4)) {
                                    ?>
                                        <option value="<?php echo ($arr_4['col_id']) ?>" <?php if ($rowEdit['col_id'] == $arr_4['col_id']) echo 'selected'; ?>>
                                            <?php echo $arr_4['col_code'] . " " . $arr_4['col_name'] . " " . $arr_4['col_desc'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="rm">วัตถุดิบ</< /label>
                                </div>
                                <select class="custom-select" id="rm" name="rm">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp5 = mysqli_query($dbconn, 'SELECT * FROM rm_code ORDER BY rm_code');
                                    while ($arr_5 = mysqli_fetch_array($rstTemp5)) {
                                    ?>
                                        <option value="<?php echo ($arr_5['rm_id']) ?>" <?php if ($rowEdit['rm_id'] == $arr_5['rm_id']) echo 'selected'; ?>>
                                            <?php echo $arr_5['rm_code'] . " " . $arr_5['rm_desc'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" style="width:90px;" for="roll">ชนิดหลอด</label>
                                </div>
                                <select class="custom-select" id="roll" name="roll">
                                    <option selected>---</option>
                                    <?php
                                    $rstTemp6 = mysqli_query($dbconn, 'SELECT * FROM roll_type ORDER BY roll_type');
                                    while ($arr_6 = mysqli_fetch_array($rstTemp6)) {
                                    ?>
                                        <option value="<?php echo ($arr_6['roll_id']) ?>" <?php if ($rowEdit['roll_id'] == $arr_6['roll_id']) echo 'selected'; ?>>
                                            <?php echo $arr_6['roll_type'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="numbox" style="background-color: aquamarine; width:110px;">จำนวนหลอด</label>
                                </div>
                                <input name="numbox" type="number" id="numbox" value='<?php echo $rowEdit["roll_n"] ?>' class="form-control input-no-spinner" step="1" required="required">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="kg" style="background-color: aquamarine; width:110px;">น้ำหนัก(Kg.)</label>
                                </div>
                                <input name="kg" type="number" id="kg" value='<?php echo $rowEdit["prod_kg"] ?>' class="form-control" step="0.01" required="required">
                            </div>
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="in_dex" style="background-color: aquamarine; width:110px;">Index @.</label>
                                </div>
                                <input name="in_dex" type="number" id="in_dex" value='<?php echo $rowEdit["val"] ?>' class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class='col-md-12' align="center">
                                <button type='submit' class='btn btn-success'>บันทีก</button> &nbsp;&nbsp;
                                <button type='button' class='btn btn-danger' onClick="document.location.href='rm_pack?show=OK&packdate=<?php echo ($date_pack) ?>&pack_bill=<?php echo $p_bill ?>&to_dp=<?php echo ($to_dp) ?>&Search=<?php echo ($search) ?>'">ยกเลิก</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php mysqli_close($dbconn);  } ?>
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

        $(document).on('keypress', 'input,select', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var $next = $('[tabIndex=' + (+this.tabIndex + 1) + ']');
                console.log($next.length);
                if (!$next.length) {
                    $next = $('[tabIndex=1]');
                }
                $next.focus().click();
            }
        });

        $(document).on('click', '.cf_delete', function(e) {
            var show = $(this).data('show');
            var id = $(this).data('id');
            var pdate = '';
            var s2 = document.getElementsByName('date_pack');
            s2 = s2.item(0).value;
            var pday = s2.split('/');
            pdate = pday[2] + '-' + pday[1] + '-' + pday[0];

            var bill = "";
            var bill = document.getElementsByName('pack_bill');
            bill = bill.item(0).value;

            var todp = "---"
            var s1 = document.getElementsByName('to_dp');
            todp = s1.item(0).value;

            var sch = '';
            var s3 = document.getElementsByName('Search');
            sch = s3.item(0).value;
            e.preventDefault();

            if ((todp == "---") || (bill == "")) {
                $(function() {
                    bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนลบข้อมูล !!",
                        function() {});
                })

            } else {

                $(function() {
                    bootbox.confirm({
                        title: 'ยืนยันการลบข้อมูล !!!',
                        //size: 'small',
                        message: 'ต้องการลบข้อมูล << <b>' + show + '</b> >> ?',
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
                                window.location.href = 'rm_pack?submit=DEL&show=OK&packdate=' + pdate +
                                    '&selboxid=' + id + '&pack_bill=' + bill + '&to_dp=' + todp + '&Search=' + sch;
                            }
                        }
                    });
                })
            }

        });

        $(document).on('click', '.cf_add', function(e) {
            var pdate = '';
            var s2 = document.getElementsByName('date_pack');
            s2 = s2.item(0).value;
            var pday = s2.split('/');
            pdate = pday[2] + '-' + pday[1] + '-' + pday[0];

            var bill = document.getElementsByName('pack_bill');
            bill = bill.item(0).value;

            var mbox = $(this).data('mbox');

            var todp = "---";
            var s1 = document.getElementsByName('to_dp');
            todp = s1.item(0).value;

            var sch = '';
            var s3 = document.getElementsByName('Search');
            sch = s3.item(0).value;


            e.preventDefault();

            if ((todp == "---") || (bill == "")) {
                $(function() {
                    bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนเพิ่มข้อมูล !!",
                        function() {});
                })

            } else {
                $(function() {
                    window.location.href = 'rm_pack?submit=Add&packdate=' + pdate + '&pack_bill=' + bill +
                        '&show=&mbid=' + mbox + '&to_dp=' + todp + '&Search=' + sch;
                })
            }

        });

        $(document).on('click', '.cf_edit', function(e) {
            var id = $(this).data('id');
            var pdate = '';

            var s2 = document.getElementsByName('date_pack');
            s2 = s2.item(0).value;
            var pday = s2.split('/');
            pdate = pday[2] + '-' + pday[1] + '-' + pday[0];

            var bill = document.getElementsByName('pack_bill');
            bill = bill.item(0).value;

            var todp = "---";
            var s1 = document.getElementsByName('to_dp');
            todp = s1.item(0).value;

            var sch = '';
            var s3 = document.getElementsByName('Search');
            sch = s3.item(0).value;

            e.preventDefault();

            if ((todp == "---") || (bill == "")) {
                $(function() {
                    bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนแก้ไขข้อมูล !!",
                        function() {});
                })
            } else {
                $(function() {
                    window.location.href = 'rm_pack?submit=Edit&show=&packdate=' + pdate + '&selboxid=' + id + '&pack_bill=' + bill + '&to_dp=' + todp + '&Search=' + sch;
                })
            }
        });

        $(document).on('click', '.cf_barcode', function(e) {
            var id = $(this).data('id');
            var pdate = '';

            var s2 = document.getElementsByName('date_pack');
            s2 = s2.item(0).value;
            var pday = s2.split('/');
            pdate = pday[2] + '-' + pday[1] + '-' + pday[0];

            var bill = document.getElementsByName('pack_bill');
            bill = bill.item(0).value;

            var todp = "---";
            var s1 = document.getElementsByName('to_dp');
            todp = s1.item(0).value;

            var sch = '';
            var s3 = document.getElementsByName('Search');
            sch = s3.item(0).value;

            e.preventDefault();

            if ((todp == "---") || (bill == "")) {
                $(function() {
                    bootbox.alert("กรุณาเลือกผู้รับวัตถุดิบ และใส่เลขที่บิล ก่อนเลือกพิมพ์ใบข้างกล่อง !!",
                        function() {});
                })
            } else {
                $(function() {
                    window.location.href = 'rm_barcode_sel?submit=&show=&packdate=' + pdate + '&pack_bill=' + bill + '&to_dp=' + todp + '&Search=' + sch;
                })
            }
        });

        function submitAdd() {
            var s1 = document.getElementsByName('prod');
            s1 = s1.item(0).value;
            var s2 = document.getElementsByName('colid');
            s2 = s2.item(0).value;
            var s3 = document.getElementsByName('rm');
            s3 = s3.item(0).value;
            var s4 = document.getElementsByName('roll');
            s4 = s4.item(0).value;
            if ((s1 == "---") || (s2 == "---") || (s3 == "---") || (s4 == "---")) {
                $(function() {
                    bootbox.alert("โปรดระบุข้อมูลให้ครบถ้วน !!", function() {});
                })
            } else {
                $('#frmAdd').submit();
            }
        }
        $("#numbox").blur(function() {
            this.value = parseFloat(this.value).toFixed(0);
        });
        $("#kg").blur(function() {
            this.value = parseFloat(this.value).toFixed(2);
        });
        $("#in_dex").blur(function() {
            this.value = parseFloat(this.value).toFixed(2);
        });

        function submitS() {
            $('#fmSearch').submit();
        }
    </script>
</body>

</html>