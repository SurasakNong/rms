<?php
require_once("dbConn.php"); // ไฟล์เชือมต่อฐานข้อมูล
require_once 'vendor/autoload.php';

if(isset($_POST['datefm'])){$date_fm = $_POST['datefm']; $date_fm2 = DateYmd($date_fm);}
if(isset($_POST['dateto'])){$date_to = $_POST['dateto']; $date_to2 = DateYmd($date_to);}
if(isset($_POST['dp'])){$dp_id = $_POST['dp'];}
if(isset($_POST['search'])){$ss = $_POST['search'];}

function DateYmd($date){
  $get_date = explode("/",$date);
  return $get_date['2']."-".$get_date['1']."-".$get_date['0'];
}

function DatedmY($date){
  $get_date = explode("-",$date);
  return $get_date['2']."/".$get_date['1']."/".$get_date['0'];
}

?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">

  <title>รายงานป้ายข้างกล่อง</title>
  <link rel="shortcut icon" href="./image/Report.ico">  

<script src="./js/jquery.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script src="./js/JsBarcode.all.min.js"></script>
<link rel="stylesheet" href="./css/bootstrap.min.css">  
<link rel="stylesheet" type="text/css" href="./css/report.css">
<link rel="stylesheet" type="text/css" href="./css/printMe.css" media="print">  
</head>

<body>

<!--==================== HeadReport =========================== -->
  <?php
$total_page_item_all = 0; // ไว้เก็บจำนวนรายการจริงทั้งหมด
$arr_data_set=array(array()); // [][];

$i=1;
          $sql = "SELECT box_id, pack_date, pack_bill,d1.depart as nameDepart, from_dp,LPAD(from_dp,2,'0') AS fromdp, CONCAT(RIGHT(YEAR(pack_date),2),LPAD(MONTH(pack_date),2,'0'),LPAD(DAY(pack_date),2,'0'),LPAD(from_dp,2,'0'),LPAD(box_id,3,'0')) AS b_name,LPAD(box_id,3,'0') AS bbb, CONCAT(prod_name,' ',col_code,' ',rm_code,' ',roll_type) AS prodname, roll_n, prod_kg, d1.depart AS frm_dp, prod_name, col_code, rm_code, roll_type, pr.status, val
          FROM(((((pro_pack AS pr
                        INNER JOIN depart AS d1 ON from_dp = id_depart)
                    INNER JOIN (product INNER JOIN prod_type ON product.prod_type_id = prod_type.prod_type_id) ON pr.prod_id = product.prod_id)
                  INNER JOIN color ON pr.col_id = color.col_id)
              INNER JOIN rm_code ON pr.rm_id = rm_code.rm_id)
            INNER JOIN roll_type ON pr.roll_id = roll_type.roll_id)
            LEFT JOIN (shelf INNER JOIN moving_type ON sh_type_id = mov_id) ON pr.shelf_id = shelf.shelf_id
          WHERE((pack_date BETWEEN '$date_fm2' AND '$date_to2') AND from_dp ='$dp_id' AND CONCAT('$',pack_bill,' #',LPAD(box_id,3,'0'),' ', IF(pr.status = 0,'+n','+y'),' ',IF(pr.status = 1,'+รับ',IF(pr.status = 2,'+เก็บ','+จ่าย')),' ',prod_name,' ',col_code,' ',rm_code,' ',roll_type,' ',prod_type,' #s',shelf_name,' ',shelf_desc,' ',mov_type) like '%$ss%')
          ORDER BY box_id*1 ASC ";
          $result = $dbconn->query($sql);
          if($result && $result->num_rows>0){  // คิวรี่ข้อมูลสำเร็จหรือไม่ และมีรายการข้อมูลหรือไม่
              while($row = $result->fetch_assoc()){ // วนลูปแสดงรายการ
                $arr_data_set['b_name'][$i]=$row['b_name'];
                $arr_data_set['bid'][$i]=$row['bbb'];
                $arr_data_set['nameDepart'][$i]=$row['nameDepart']." (".$row['fromdp'].")";
                $arr_data_set['prodname'][$i]=$row['prodname'];
                $arr_data_set['pack_date'][$i]=$row['pack_date'];
                $arr_data_set['pack_bill'][$i]=$row['pack_bill'];
                $arr_data_set['box_id'][$i]=$row['box_id'];
                $arr_data_set['status'][$i]=$row['status'];
                $arr_data_set['roll_n'][$i]=$row['roll_n'];
                $arr_data_set['prod_kg'][$i]=$row['prod_kg'];
                $arr_data_set['val'][$i]=$row['val'];
                $arr_data_set['valS'][$i]=$row['prod_kg']*$row['val'];
                $i++;
              }
            }
    $total_page_item_all = $i; // จำนวนรายการทั้งหมด
    //$total_page_data = ceil($total_page_item_all/$total_page_item); // หาจำนวนหน้าจากรายการทั้งหมด
  ?>
  <?php for($i=1;$i<$total_page_item_all;$i++){?>
     <!--=================== Data Report ============================ -->

  <div  align="center">
    

            <?php
        // ส่วนของ repeat content
              $item_i=$i;

              $item_i = isset($arr_data_set['prodname'][$item_i])?$item_i:"";
              $_b_name = isset($arr_data_set['b_name'][$item_i])?$arr_data_set['b_name'][$item_i]:"";
              $_bid = isset($arr_data_set['bid'][$item_i])?$arr_data_set['bid'][$item_i]:"";
              $_ndepart = isset($arr_data_set['nameDepart'][$item_i])?$arr_data_set['nameDepart'][$item_i]:"";

              $_pack_date = isset($arr_data_set['pack_date'][$item_i])?DatedmY($arr_data_set['pack_date'][$item_i]):"";
              $_packbill = isset($arr_data_set['pack_bill'][$item_i])?$arr_data_set['pack_bill'][$item_i]:"";
              $_prodname = isset($arr_data_set['prodname'][$item_i])?$arr_data_set['prodname'][$item_i]:"";
              $_box_id = isset($arr_data_set['box_id'][$item_i])?$arr_data_set['box_id'][$item_i]:"";
              $_roll_n = isset($arr_data_set['roll_n'][$item_i])?number_format($arr_data_set['roll_n'][$item_i]):"";
              $_prod_kg = isset($arr_data_set['prod_kg'][$item_i])?number_format($arr_data_set['prod_kg'][$item_i],2):"";
              $_val = isset($arr_data_set['val'][$item_i])?number_format($arr_data_set['val'][$item_i],2):"";

              $code = $_b_name;              

              ?>
            <table width="372" border="0" align="left" cellpadding="0" cellspacing="0"  style="margin-bottom: 50px; margin-left: 5px; margin-right: 5px; border-collapse:collapse;border-top:2px solid #000;">
            <tr>
              <td rowspan="2" style="font-size: 50px;" width="124" class="headerTabel" align="center" valign="bottom"><?=$_bid?></td>
              <td colspan="4" height="40px" style="font-size: 14px; border-right: 2Px solid #000;" align="center" valign="bottom" >บ.โรงงานทออวนเดชาพานิช จำกัด</td>
            </tr>
            <tr>
              <td colspan="4" height="30px" style="font-size: 20px; border-bottom:2px solid #000; border-right: 2Px solid #000;" align="center" valign="middle"><?=$_ndepart?></td>
            </tr>
            <tr>
              <td colspan="6" height="50px" style="font-size: 22px; border-left:2px solid #000; border-right: 2Px solid #000;" align="center" valign="middle"><?=$_prodname?></td>
            </tr>
            <tr>
              <td colspan="2" width="124" height="34px" style="font-size: 18px; border-left:2px solid #000; " align="center" valign="bottom"><?=$_pack_date?></td>
              <td colspan="2" width="124" height="34px" style="font-size: 22px; border-left:1px solid #000; border-right: 1Px solid #000;" align="center" valign="bottom"><?=$_roll_n?></td>
              <td colspan="2" width="124" height="34px" style="font-size: 22px; border-right: 2Px solid #000;" align="center" valign="bottom"><?=$_prod_kg?></td>
            </tr>
            <tr>
              <td colspan="2" width="124" height="5px" style="font-size: 12px; border-left:2px solid #000; " align="center" valign="middle">วันบรรจุ</td>
              <td colspan="2" width="124" height="5px" style="font-size: 12px; border-left:1px solid #000; border-right: 1Px solid #000;" align="center" valign="middle">หลอด</td>
              <td colspan="2" width="124" height="5px" style="font-size: 12px; border-right: 2Px solid #000;" align="center" valign="middle">กิโลกรัม</td>
            </tr>
            <tr>
              <td colspan="6" height="100px" style="border-bottom:2px solid #000; border-left:2px solid #000; border-right: 2Px solid #000;" align="center" valign="middle"><svg class="barcode"jsbarcode-value="<?=$code?>"></svg></td>
            </tr>
            <tr>
              <td colspan="6">&nbsp;</td>
            </tr>
            </table>
     
</div>   

<?php } mysqli_close($dbconn); ?>


<script type="text/javascript">
//  JsBarcode("element selector", "ค่าหรือข้อความที่จะแสดง");
 //   JsBarcode("#barcode", "21021902030");   // กรณีใช้ผ่าน id
   // JsBarcode(".mybarcode", "ninenik.com");  // กรณีใช้ผาน css class
</script>

<script type="text/javascript">
    JsBarcode(".barcode").options({
        format: "CODE128",
        font:"Arial",
        lineColor:"black",
        height:50
        }).init();
</script>
  </body>

  </html>
