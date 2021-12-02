<!DOCTYPE html>

<head>
  <title>Raw Material Stock</title>
  <link rel="shortcut icon" href="./image/stockicon128.ico">
  <meta name="description" content="Raw Material Stock">
  <meta name="generator" content="Surasak.i">

  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


  <script src="./js/jquery.min.js"></script>
  <script src="./js/popper.min.js"></script>
  <script src="./js/bootstrap.min.js"></script>
  <script src='./js/bootbox.min.js'></script>
  <link rel="stylesheet" href="./css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/all.min.css"> <!-- font awesome -->
  <link rel="stylesheet" href="./css/menunav.css">
  <link rel="stylesheet" href="./css/intro.css">
  

</head>
<?php
include 'Menu_admin.php';
include_once "dbConn.php";
//======= Delete log more than 2 month ====
$DelLog = mysqli_query($dbconn, "DELETE FROM u_log WHERE(DATEDIFF(CURDATE(),dt_stamp) > 60)")
  or die(mysqli_error($dbconn));

?>
<script>
  var sss = '<?php echo ($ss_user); ?>';
</script>

<body>

  <div id="carouselIntro" class="carousel slide carousel-fade" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#carouselIntro" data-slide-to="0" class="active"></li>
      <li data-target="#carouselIntro" data-slide-to="1"></li>
      <li data-target="#carouselIntro" data-slide-to="2"></li>
      <li data-target="#carouselIntro" data-slide-to="3"></li>
      <li data-target="#carouselIntro" data-slide-to="4"></li>
    </ol>
    <div class="carousel-inner">
      <div class="carousel-item active" style="background-image: url('./image/intro1.jpg');" data-interval="6000">
        <div class="carousel-caption d-block">
          <p>RMS : Raw Material Stock</p>
          <h4>ยินดีต้อนรับ&nbsp;คุณ<?php echo ($ss_user) ?></h4>
          <p>( <?php echo ($ss_depart) ?> )</p>
        </div>
      </div>
      <div class="carousel-item" style="background-image: url('./image/intro2.jpg');" data-interval="6000">
        <div class="carousel-caption d-block">
          <a onclick="log_save('1',sss,'de');">1. งานบรรจุวัตถุดิบ</a>
          <p>คือ การบรรจุวัตถุดิบลงกล่องพร้อมกับติดป้ายข้างกล่องระบุข้อมูลวัตถุดิบให้ชัดเจนก่อนนำส่งสต็อกวัตถุดิบ</p>
        </div>
      </div>
      <div class="carousel-item" style="background-image: url('./image/intro3.jpg');" data-interval="6000">
        <div class="carousel-caption d-block">
          <a>2. งานรับเข้าวัตถุดิบ</a>
          <p>คือ การยอมรับเข้าในบัญชีเพื่อรอจัดเก็บ ใปยังที่จัดเก็บ ตามประเภท และชนิดของวัตถุดิบ</p>
        </div>
      </div>
      <div class="carousel-item" style="background-image: url('./image/intro4.jpg');" data-interval="6000">
        <div class="carousel-caption d-block">
          <a>3. งานจัดเก็บวัตถุดิบ</a>
          <p>คือ การนำวัตถุดิบที่รับเข้ามาแล้ว ทำการระบุชั้นวาง หรือ โซนจัดเก็บเพื่อให้สะดวกในตอนค้นหา</p>
        </div>
      </div>
      <div class="carousel-item" style="background-image: url('./image/intro5.jpg');" data-interval="6000">
        <div class="carousel-caption d-block">
          <a>4. งานส่งวัตถุดิบ</a>
          <p>คือ การจัดเตรียมส่งวัตถุดิบตามที่มีการเบิก เพื่อนำไปทำการผลิตอวน</p>
        </div>
      </div>
    </div>
    <a class="carousel-control-prev" href="#carouselIntro" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselIntro" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>

</body>

</html>
