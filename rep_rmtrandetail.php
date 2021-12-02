<?php
session_start();
require_once("dbConn.php"); // ไฟล์เชือมต่อฐานข้อมูล
if(isset($_POST['datefm'])){$date_fm = $_POST['datefm']; $date_fm2 = DateYmd($date_fm);}
if(isset($_POST['dateto'])){$date_to = $_POST['dateto']; $date_to2 = DateYmd($date_to);}
$dp_id = (isset($_POST['dp'])) ? $_POST['dp'] : '0';
$ss = (isset($_POST['search'])) ? $_POST['search'] : '';

if($dp_id != '0'){$_where ="AND tranto_dp ='$dp_id'";} else{$_where ='';}


$dp_Acc = $_SESSION['s_dpid']; // รหัสแผนกผู้ใช้งานระบบ
$departAcc =$_SESSION['s_depart']; // ชื่อแผนกผู้ใช้งานระบบ

function DateYmd($date){
  $get_date = explode("/",$date);
  return $get_date['2']."-".$get_date['1']."-".$get_date['0'];
}

function DatedmY($date){
  $get_date = explode("-",$date);
  return $get_date['2']."/".$get_date['1']."/".$get_date['0'];
}
$_depart =' ทั้งหมด ';
$rsTemp = mysqli_query( $dbconn, "SELECT * FROM depart WHERE id_depart='$dp_id'" );
 if ( mysqli_num_rows($rsTemp) > 0  ) {
  $row = mysqli_fetch_array($rsTemp);
  $_depart = $row['depart'];
 }

?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>รายงานการส่งวัตถุดิบ</title>

  <link rel="shortcut icon" href="./image/Report.ico">
  <link rel="stylesheet" type="text/css" href="./css/report.css">
  <link rel="stylesheet" type="text/css" href="./css/printMe.css" media="print">
  <script src="./js/jquery.min.js"></script>

</head>

<body>
<!--==================== HeadReport =========================== -->
<div align="center">
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" class="headTitle">รายงานการส่งวัตถุดิบ (<?=$departAcc?>)</td>
  </tr>
  <tr>
    <td align="center" class="headTitle2">วันที่ส่ง : <?=$date_fm ?>&nbsp;&nbsp;ถึง&nbsp;&nbsp;<?=$date_to ?></td>
  </tr>
  <tr>
    <td align="center" class="headTitle2"><?="ผู้รับวัตถุดิบ :".$_depart?>&nbsp;&nbsp;&nbsp;&nbsp;คำค้นหา : ( &nbsp;<?=$ss ?>&nbsp; )</td>
  </tr>
</table>

  <?php
$total_page_data = 0;  // เก็บจำนวนหน้า รายการทั้งหมด
$row_head_rep = 4; //จำนวนบรรทัดหัวรายงาน
$tpi = 48; //จำนวนบรรทัดข้อมูลเมื่อไม่มีหัวหรือท้ายรายงาน
$total_page_item = $tpi; // จำนวนรายการที่แสดงสูงสุดในแต่ละหน้า
$total_page_item_all = 0; // ไว้เก็บจำนวนรายการจริงทั้งหมด
$arr_data_set=array(array()); // [][];
$sum_box =0;
$sum_roll = 0;
$sum_kg = 0;
$sum_val = 0;

$sqlG = "SELECT prodname, CAST(SUM(valA) AS DECIMAL(16,2)) as val, COUNT(box_id) AS s_box, SUM(roll_n) AS s_roll_n, SUM(prod_kg) AS s_prod_kg, prID, colID, rmID, rollID, MAX(sst) AS stat
FROM (SELECT CONCAT(prod_name, ' ', col_code, ' ', rm_code, ' ', roll_type) AS prodname, box_id, (pr.val * prod_kg) AS valA, roll_n, prod_kg, pr.prod_id AS prID, pr.col_id AS colID, pr.rm_id AS rmID, pr.roll_id as rollID, pr.status AS sst
    FROM ( ( ( ( ((pro_pack AS pr
                      INNER JOIN depart AS d1 ON tranto_dp = id_depart )
                  INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id )
                INNER JOIN color ON pr.col_id = color.col_id )
            INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
        INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id )
      INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id)
    WHERE((pr.status = 3) AND (tran_date BETWEEN '$date_fm2' AND '$date_to2') AND to_dp ='$dp_Acc' ".$_where." AND CONCAT('$',bill_no,' #',LPAD(box_id,3,'0'),' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) LIKE '%$ss%')) AS TT
GROUP BY ProdName
ORDER BY ProdName ASC";

$i=1;
$resultG = $dbconn->query($sqlG);
if($resultG && $resultG->num_rows>0){  // คิวรี่ข้อมูล Group สำเร็จหรือไม่ และมีรายการข้อมูลหรือไม่
    while($rowG = $resultG->fetch_assoc()){ // วนลูปแสดงรายการ Group
          $strGroup =$rowG['prodname'];
          $sql = "SELECT tran_date, bill_no, tranto_dp, box_id, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prodname, CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),'-',LPAD(box_id,3,'0')) AS boxname,pr.prod_id, pr.col_id, pr.rm_id, pr.roll_id, roll_n, prod_kg, d1.depart AS nameto_dp, prod_name, col_code, rm_code, roll_type, pr.status, val
          FROM((((((pro_pack AS pr
                        INNER JOIN depart AS d1 ON tranto_dp = id_depart)
                    INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
                  INNER JOIN color ON pr.col_id = color.col_id)
              INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
            INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
          INNER JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id)
          WHERE((pr.status = 3) AND (tran_date BETWEEN '$date_fm2' AND '$date_to2') AND to_dp ='$dp_Acc' ".$_where." AND CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) = '$strGroup' AND CONCAT('$',bill_no,' #',LPAD(box_id,3,'0'), ' p',DATE_FORMAT(pack_date, '%d/%m/%Y'),' #s',shelf_name,' ',shelf_desc,' ',mov_type,' r',DATE_FORMAT(rec_date, '%d/%m/%Y'),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type) LIKE '%$ss%')
          ORDER BY prodname, tran_date, bill_no, box_id";

          $result = $dbconn->query($sql);
          if($result && $result->num_rows>0){  // คิวรี่ข้อมูลสำเร็จหรือไม่ และมีรายการข้อมูลหรือไม่
              while($row = $result->fetch_assoc()){ // วนลูปแสดงรายการ
                $arr_data_set['prodname'][$i]=$row['prodname'];
                $arr_data_set['tran_date'][$i]=$row['tran_date'];
                $arr_data_set['nameto_dp'][$i]=$row['nameto_dp'];
                $arr_data_set['bill_no'][$i]=$row['bill_no'];
                $arr_data_set['boxname'][$i]=$row['boxname'];
                $arr_data_set['roll_n'][$i]=$row['roll_n'];
                $arr_data_set['prod_kg'][$i]=$row['prod_kg'];
                $arr_data_set['val'][$i]=$row['val'];
                $arr_data_set['valS'][$i]=$row['prod_kg']*$row['val'];
                $sum_box = $sum_box + 1;
                $sum_roll = $sum_roll + $row['roll_n'];
                $sum_kg = $sum_kg + $row['prod_kg'];
                $sum_val = $sum_val + $arr_data_set['valS'][$i];
                $i++;
              }
            }

      $arr_data_set['g1'][$i] = 'g1';
      $arr_data_set['prodname'][$i]=$rowG['prodname'];
      $arr_data_set['box_n'][$i]=$rowG['s_box'];
      $arr_data_set['roll_n'][$i]=$rowG['s_roll_n'];
      $arr_data_set['prod_kg'][$i]=$rowG['s_prod_kg'];
      $arr_data_set['valS'][$i]=$rowG['val'];

      $i++;
    }
    $total_page_item_all = $i; // จำนวนรายการทั้งหมด
    $total_page_data = ceil($total_page_item_all/$total_page_item); // หาจำนวนหน้าจากรายการทั้งหมด
  }

  ?>
  <?php for($i=1;$i<=$total_page_data;$i++){  ?>

    <table width="750" border="0" align="center" cellpadding="0" cellspacing="0">

<!--=================== Data Report ============================ -->
      <tr>
        <td align="left">
          <table width="750" border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-top:2px solid #000;">
            <tr>
              <td width="80" height="25px" class="headerTabel" align="center" valign="middle">วันที่ส่ง-บิล</td>
              <td width="80" class="headerTabel_thin" align="center" valign="middle">ผู้รับ</td>
              <td width="90" class="headerTabel_thin" align="center" valign="middle">#กล่อง</td>
              <td width="200" class="headerTabel_thin" align="center" valign="middle">รายการ</td>
              <td width="60" class="headerTabel_thin" align="center" valign="middle">ind.@</td>
              <td width="90" class="headerTabel_thin" align="center" valign="middle">index</td>
              <td width="70" class="headerTabel" align="center" valign="middle">หลอด</td>
              <td width="80" class="headerTabel_R_thin" align="center" valign="middle">กิโลกรัม</td>
            </tr>
            <?php
        // ส่วนของ repeat content
            for($v=1;$v<=$total_page_item;$v++){
              $item_i=(($i-1)*$total_page_item)+$v;

              $item_i = isset($arr_data_set['prodname'][$item_i])?$item_i:"";
              $_tran_date = isset($arr_data_set['tran_date'][$item_i])?DatedmY($arr_data_set['tran_date'][$item_i]):"";
              $_nametodp = isset($arr_data_set['nameto_dp'][$item_i])?$arr_data_set['nameto_dp'][$item_i]:"";
              $_prodname = isset($arr_data_set['prodname'][$item_i])?$arr_data_set['prodname'][$item_i]:"";
              $_bill_no = isset($arr_data_set['bill_no'][$item_i])?$arr_data_set['bill_no'][$item_i]:"";
              $_boxname = isset($arr_data_set['boxname'][$item_i])?$arr_data_set['boxname'][$item_i]:"";
              $_box_n = isset($arr_data_set['box_n'][$item_i])?$arr_data_set['box_n'][$item_i]:"";

              $_G1 = isset($arr_data_set['g1'][$item_i])?$arr_data_set['g1'][$item_i]:"";

              $_roll_n = isset($arr_data_set['roll_n'][$item_i])?number_format($arr_data_set['roll_n'][$item_i]):"";
              $_prod_kg = isset($arr_data_set['prod_kg'][$item_i])?number_format($arr_data_set['prod_kg'][$item_i],2):"";
              $_val = isset($arr_data_set['val'][$item_i])?number_format($arr_data_set['val'][$item_i],2):"";
              $_valS = isset($arr_data_set['valS'][$item_i])?number_format($arr_data_set['valS'][$item_i],2):"";

             if ($_prodname !=''){
                if($_G1 == 'g1') { ?>
                  <tr>
                    <td colspan="5" width="490" height="20" align="right" class="lbg"><?=$_prodname.' ( '.number_format($_box_n).' กล่อง )'?>&nbsp;&nbsp; </td>
                    <td align="right" class="lbg_thin"><?=$_valS?>&nbsp;</td>
                    <td align="right" class="lbg"><?=$_roll_n?>&nbsp;</td>
                    <td align="right" class="lrbg_thin"><?=$_prod_kg ?>&nbsp;</td>
                  </tr>


              <?php  }else { ?>
                  <tr>
                    <td align="center" class="left_bottom"><?=$_tran_date?>-<?=$_bill_no?></td>
                    <td align="center" class="left_bottom_thin"><?=$_nametodp?></td>
                    <td align="center" class="left_bottom_thin"><?=$_boxname?></td>
                    <td height="20" align="left" class="left_bottom_thin">&nbsp; <?=$_prodname?> </td>
                    <td align="right" class="left_bottom_thin"><?=$_val?>&nbsp;</td>
                    <td align="right" class="left_bottom_thin"><?=$_valS?>&nbsp;</td>
                    <td align="right" class="left_bottom"><?=$_roll_n?>&nbsp;</td>
                    <td align="right" class="left_right_bottom_thin"><?=$_prod_kg ?>&nbsp;</td>
                  </tr>

              <?php  } ?>

            <?php }else{$v = $total_page_item +1;} }?>
        <div class="page-break<?=($i==1)?"-no":""?>">&nbsp;</div>
<?php }
mysqli_close($dbconn); ?>
                  <tr>
                    <td colspan="5" height="25" align="center" class="lbf" >รวมทั้งหมด (&nbsp;<?=number_format($sum_box)?>&nbsp;กล่อง )
                    </td>
                    <td align="right" class="lbf_thin"> <?=number_format($sum_val,2)?>&nbsp;
                    </td>
                    <td align="right" class="lbf"> <?=number_format($sum_roll)?>&nbsp;
                    </td>
                    <td align="right" class="lrbf_thin"> <?=number_format($sum_kg,2)?>&nbsp;
                    </td>
                  </tr>
          </table>
        </td>
      </tr>
      </table>


<!--====================== Footdata Report ========================= -->


    <table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="6" align="left">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="6" align="left">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="6" align="left">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center" class="FooterR">ผู้ส่ง.................................</td>
          <td colspan="2" align="center" class="FooterR">ผู้รับ.................................</td>
          <td colspan="2" align="center" class="FooterR">ผู้อนุมัติ...............................</td>
        </tr>
        <tr>
          <td colspan="6" align="left">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
          <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
          <td colspan="2" align="center" class="FooterR">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        </tr>
        <tr>
          <td colspan="6" align="left">&nbsp;</td>
        </tr>
    </table>
</div>
  </body>

  </html>
