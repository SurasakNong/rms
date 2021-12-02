<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>JsBarcode</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" >
    <script src="node_modules/barcode/JsBarcode.all.min.js"></script>
</head>
<body>

  <br />
<br />
<style type="text/css">
.mycss{
    border:1px solid #CCC;
    padding:10px;
}
</style>
 <div style="width:500px;margin:auto;">
    <svg id="barcode"></svg>
    <br>
    <svg class="mycss mybarcode"></svg>
 </div>

<script type="text/javascript">
//  JsBarcode("element selector", "ค่าหรือข้อความที่จะแสดง");
    JsBarcode("#barcode", "21021902030");   // กรณีใช้ผ่าน id
    JsBarcode(".mybarcode", "ninenik.com");  // กรณีใช้ผาน css class
</script>
</body>
</html>
