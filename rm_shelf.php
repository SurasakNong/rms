<!doctype html>
<html lang="en">

<head>
    <title>RM-RECEIVE SHELF</title>
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

    $date_rec = (isset($_GET['recdate'])) ? $_GET['recdate'] : ((isset($_POST['date_rec'])) ? DateYmd($_POST['date_rec']) : date("Y-m-d"));

    $dp_Acc = (isset($_SESSION['s_dpid'])) ? $_SESSION['s_dpid'] : '';  // รหัสผู้ใช้งาน
    $df_Acc = (isset($_SESSION['s_dfid'])) ? $_SESSION['s_dfid'] : '';  // ช่องก่อนจัดเก็บของหน่วยงานผู้ใช้งาน
    $shelfSel = (isset($_POST['shelf_sel'])) ? $_POST['shelf_sel'] : $df_Acc;
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

    if ($submit == "OK") { //==================  UPDATE  ====================

        for ($i = 1; $i <= $Num_Box; $i++) {
            $strStatus = "ckIn" . $i;
            $strStatus = (isset($_POST[$strStatus])) ? $_POST[$strStatus] : '0';
            if ($strStatus == "1") {
                $b_name = "b_name" . $i;
                $b_name = $_POST[$b_name];
                $sta = "stt" . $i;
                $sta = $_POST[$sta];

                $newStatus = 2;
                if ($shelfSel == $df_Acc) {
                    $newStatus = 1;
                }

                $sqlUp = "UPDATE pro_pack SET status='$newStatus', shelf_id ='$shelfSel'  WHERE(CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0'))  = '$b_name')";
                $rsUpdate = mysqli_query($dbconn, $sqlUp) or die(mysqli_error($dbconn));

                //===== LOG ==========
                $descLog = "shelf" . $date_rec . "_sh" . $shelfSel . "_dp" . $dp_Acc . "_box" . $b_name;
                saveLog($_SESSION['s_id'], '_edit', $descLog);
            }
        }
    }

    ?>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style='margin-bottom: 8px; font-size:18px;'>
                <span class="d-block p-2 bg-success text-white" align="center"><?php echo $_SESSION['s_depart'] ?> <i class="fa fa-th fa-lg fa-fw" aria-hidden="true"></i> จัดเก็บวัตถุดิบ</span>
            </div>

            <?php
            if ($submit == "" or $show == "OK") { ?>
                <!-- ==================== Show Form ========================================= -->
                <div class="col-md-12">
                    <form name="fmSearch" id="fmSearch" method="post" action="rm_shelf?show=OK" role='Search'>
                        <div class="form-group row">
                            <div class="input-group mb-2 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text " for="picker">วันที่รับเข้า</label>
                                </div>
                                <input name="date_rec" type="text" value='<?php echo (DatedmY($date_rec)) ?>' onchange="submitS()" id="picker" class="form-control">
                            </div>
                            <div class="input-group mb-2 col-md-6">
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
                $rec_sql = "SELECT pack_date,rec_date,box_id,roll_n, prod_kg, d1.depart AS frm_dp, from_dp, to_dp, prod_name, col_code, rm_code, roll_type, status, shelf_name, shelf.shelf_id
FROM((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)
    INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE((status BETWEEN 1 AND 2) AND rec_date = '$date_rec' AND to_dp='$dp_Acc' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0'),' ','#',LPAD(box_id,3,'0'),' #s',shelf_name,' ',shelf_desc,' ',mov_type,' ',d1.depart,' ',IF(status = 2,'+y','+n'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%')";

                $Qtotal = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn));
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
                    <form name="fmTable" id="fmTable" method="post" action="rm_shelf?submit=OK&show=OK&recdate=<?php echo ($date_rec) ?>&numbox=<?php echo ($n) ?>">
                        <div class="form-group row">
                            <div class="col-md-10">
                                <div class='alert alert-success' role='alert' align="center">
                                    <?php
                                    echo ($from . "-" . $to);
                                    echo (" (" . number_format($total_data) . " กล่อง ");
                                    echo (" -- " . number_format($numRoll) . " หลอด ");
                                    echo (" -- " . number_format($Kg, 2) . " ก.ก.)");

                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2" title="จัดเก็บเข้าช่อง" align="center" style="padding-bottom: 10px">
                                <a name="cfUp" type='submit' class='btn btn-success'>จัดเก็บ</a>

                            </div>

                            <div class="col-md-12">
                                <table class="table table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <td align='center'><a href="#" class='text-success' onClick="fnCkeckBox();">เลือก</a>
                                            </td>
                                            <td align='center'><strong>บรรจุ</strong>
                                            </td>
                                            <td align='center'><strong>กล่อง</strong>
                                            </td>
                                            <td align='center'><strong>รายการ</strong>
                                            </td>
                                            <td align='center'><strong>หลอด</strong>
                                            </td>
                                            <td align='center'><strong>น้ำหนัก</strong>
                                            </td>
                                            <td align='center'><strong>ช่อง</strong>
                                            </td>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $rec_sql = "SELECT pack_date,rec_date,box_id,from_dp,to_dp,val, roll_n, prod_kg, d1.depart AS frm_dp, to_dp, prod_name, col_code, rm_code, roll_type, status, shelf_name, shelf.shelf_id,CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0')) AS b_name
FROM((((((pro_pack AS pr INNER JOIN depart AS d1 ON from_dp = id_depart)
                INNER JOIN depart AS d2 ON to_dp = d2.id_depart)
            INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
        INNER JOIN color ON pr.col_id = color.col_id)
    INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
WHERE((status BETWEEN 1 AND 2) AND rec_date = '$date_rec' AND to_dp='$dp_Acc' AND CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0'),' ','#',LPAD(box_id,3,'0'),' #s',shelf_name,' ',shelf_desc,' ',mov_type,' ',d1.depart,' ',IF(status = 2,'+y','+n'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) like '%$search%') ORDER BY pack_date, from_dp, (box_id*1) ASC LIMIT $start,$limit";

                                        $query = mysqli_query($dbconn, $rec_sql) or die(mysqli_error($dbconn));
                                        $n =  mysqli_num_rows($query);
                                        $i = 0;
                                        while ($arr = mysqli_fetch_array($query)) {
                                            $i++;
                                        ?>
                                            <tr <?php if ($arr['shelf_id'] == $df_Acc) {
                                                    echo ('style="color: red"');
                                                } ?>>
                                                <td align='center'><input class="form-check-input" type="checkbox" name="ckIn<?php echo ($i); ?>" value="1">
                                                </td>
                                                <td align='center'>
                                                    <?php echo $arr['pack_date'] . " " . $arr['frm_dp'] ?>
                                                </td>
                                                <td align='center'>
                                                    <?php echo $arr['box_id'] ?>
                                                </td>
                                                <td align='center'>
                                                    <?php echo $arr['prod_name'] . ' ' . $arr['col_code'] . ' ' . $arr['rm_code'] . ' ' . $arr['roll_type'] ?>
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

                                            </tr>
                                            <input type='hidden' name="stt<?php echo ($i) ?>" value="<?php echo $arr['status'] ?>" />
                                            <input type='hidden' name="b_name<?php echo ($i) ?>" value="<?php echo $arr['b_name'] ?>" />
                                        <?php }    ?>
                                        <input type='hidden' name="shelf_sel" id="shelf_sel" />
                                    </tbody>
                                </table>
                                <nav aria-label="Page navigation">
								<?php $_link = "rm_shelf?Search=$search&recdate=$date_rec&page="; ?>
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
                        <?php  } ?>
                        </div>
                    </form>
                </div>
        </div>
    </div>

    <!-- ================== SCRIPT =========================================================== -->
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
                bootbox.prompt({
                    title: "กรุณาเลือกช่องจัดเก็บวัตถุดิบ !",
                    inputType: 'select',
                    inputOptions: [
                        <?php $sql = "SELECT shelf.shelf_id,shelf_desc,shelf_name,mov_type, CONCAT(' [', COUNT(box_id),'] ',format(SUM(roll_n),0),' --', format(SUM(prod_kg),2),'kg.') AS shelf_sum
                        FROM (shelf INNER JOIN moving_type ON sh_type_id = mov_id) LEFT JOIN pro_pack ON pro_pack.shelf_id = shelf.shelf_id
                        WHERE shelf.depart_id ='$dp_Acc'
                        GROUP BY shelf_name";
                        $rstTemp = mysqli_query($dbconn, $sql);
                        $row = mysqli_num_rows($rstTemp);
                        $rr = 0;
                        while ($arr = mysqli_fetch_array($rstTemp)) {
                            $rr++; 
                            echo("{text:'".$arr['shelf_name'] . " " . $arr['shelf_desc'] . " " . $arr['mov_type'] . " " . $arr['shelf_sum']."',");
                            echo("value:'".$arr['shelf_id']."',}");
                            if ($rr<$row) {echo(",");}                            
                        } ?> 
                    ],
                    callback: function(result) {
                        if (result) {
                            document.getElementById('shelf_sel').value = result;
                            $('#fmTable').submit();
                        } else {
                            $('#fmSearch').submit();
                        }
                    }
                });
            } else {
                $(function() {
                    bootbox.alert('กรุณาเลือกรายการที่ต้องการจัดเก็บ...!', function() {
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

        function submitS() {
            $('#fmSearch').submit();
        }
    </script>

</body>

</html>