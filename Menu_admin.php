<?php session_start();
if (!isset($_SESSION['s_username'])) {
  header('Location: index.php');
  die();
}
?>
<!-- Required meta tags -->
<meta http-equiv='Content-Type' content='text/html; charset=utf-8;' />
<?php
if (isset($_SESSION['s_username'])) {
  $ss_iduser = $_SESSION['s_id'];
  $ss_username = $_SESSION['s_username'];
  $ss_user = $_SESSION['s_user'];
  $ss_dpid = $_SESSION['s_dpid'];
  $ss_depart = $_SESSION['s_depart'];

  $ss_pack = ($_SESSION['s_pack'] == "0") ? "ok" : "";
  $ss_rec = ($_SESSION['s_rec'] == "0") ? " disabled" : "";
  $ss_keep = ($_SESSION['s_keep'] == "0") ? " disabled" : "";
  $ss_tran = ($_SESSION['s_tran'] == "0") ? " disabled" : "";
  $ss_data = ($_SESSION['s_data'] == "0") ? " disabled" : "";
  $ss_sys = ($_SESSION['s_sys'] == "0") ? " disabled" : "";
  $ss_sys2 = ($_SESSION['s_sys'] == "0") ? "ok" : "";
}

function saveLog($_id, $_ss, $_des)
{
  echo ("<script>");
  echo ("$(function(){ logSave('" . $_id . "','" . $_ss . "','" . $_des . "'); })");
  echo ("</script>");
}

?>

<!-- NavBar -->
<header class="header_area">
  <div class="main_menu">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mx-0 px-0">
      <div class="container-fluid">
        <a class="navbar-brand" valign="middle" href="intro">
          <img src="image/Stockicon.png" width="32" valign="middle" height="32" class="d-inline-block align-center " alt="" loading="lazy"> RMS
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"><i class="fas fa-cog fa-lg" aria-hidden="true"></i> ระบบ</a>
              <div class="dropdown-menu bg-info" style="background-color: #95A9AF" aria-labelledby="navbarDropdown">
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_prod"><i class="fas fa-bacon" aria-hidden="true"></i> เบอร์ด้าย</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_col"><i class="fas fa-palette" aria-hidden="true"></i> สี</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_rm"><i class="fas fa-flask" aria-hidden="true"></i> วัตถุดิบ</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_roll"><i class="fas fa-hourglass" aria-hidden="true"></i> ชนิดหลอด</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_type"><i class="fas fa-bookmark" aria-hidden="true"></i> ชนิดด้าย</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_shelf"><i class="fas fa-braille" aria-hidden="true"></i> ช่องจัดเก็บ</a>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_data) ?>" href="data_depart"><i class="fas fa-university" aria-hidden="true"></i> หน่วยงาน</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item bg-info text-white <?php echo ($ss_sys) ?>" href="user"><i class="fas fa-user" aria-hidden="true"></i> ทะเบียนผู้ใช้งาน</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item bg-primary text-white " href="user_changepass"><i class="fas fa-lock" aria-hidden="true"></i> เปลี่ยนรหัสผ่าน</a>
              </div>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown"><i class="fas fa-briefcase fa-lg " aria-hidden="true"></i> งานวัตถุดิบ
              </a>
              <div class="dropdown-menu bg-success" style="background-color: #95A9AF" aria-labelledby="navbarDropdown2">
                <a class="dropdown-item bg-success text-white <?php echo ($ss_pack) ?>" href="rm_pack"><i class="fas fa-cube" aria-hidden="true"></i> บรรจุวัตถุดิบ</a>
                <a class="dropdown-item bg-success text-white <?php echo ($ss_rec) ?>" href="rm_rec"><i class="fas fa-arrow-circle-right" aria-hidden="true"></i> รับเข้าวัตถุดิบ</a>
                <a class="dropdown-item bg-success text-white <?php echo ($ss_keep) ?>" href="rm_shelf"><i class="fas fa-th" aria-hidden="true"></i> จัดเก็บวัตถุดิบ</a>
                <a class="dropdown-item bg-success text-white <?php echo ($ss_tran) ?>" href="rm_tran"><i class="fas fa-truck" aria-hidden="true"></i> ส่งวัตถุดิบ</a>

                <?php if ($ss_sys2 != 'ok') { ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item bg-warning text-white" href="rm_pack_admin"><i class="fas fa-cube" aria-hidden="true"></i> บรรจุวัตถุดิบ-admin</a>
                  <a class="dropdown-item bg-warning text-white" href="data_log"><i class="fas fa-clipboard-list" aria-hidden="true"></i> Log info</a>
                <?php } ?>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown"><i class="fas fa-print fa-lg " aria-hidden="true"></i>
                รายงาน
              </a>
              <div class="dropdown-menu bg-primary" style="background-color: #95A9AF" aria-labelledby="navbarDropdown2">
                <a class="dropdown-item bg-primary text-white" href="frm_report"><i class="fas fa-newspaper" aria-hidden="true"></i> แสดงรายงาน</a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item bg-primary text-white" href="frm_check"><i class="fas fa-barcode" aria-hidden="true"></i> ตรวจสอบข้อมูลวัตถุดิบ</a>
              </div>
            </li>

          </ul>
          <form class="logout-bt d-flex">
            <a href="#" class='cf_logout btn btn-outline-danger btn-sm' data-id='<?php echo ($ss_iduser); ?>' data-name='<?php echo ($ss_user); ?>' role='button'>Logout</a>
          </form>
        </div>
      </div>
    </nav>

  </div>

  <script>
    $(document).on('click', '.cf_logout', function(e) {
      var v_nn = $(this).data('name');
      var v_id = $(this).data('id');
      var v_ss = 'Logout';
      var v_de = 'ออกระบบ';
      e.preventDefault();

      bootbox.confirm({
        title: 'ยืนยัน !!!',
        //size: 'small',
        message: 'คุณ' + v_nn + ' ต้องการออกจากโปรแกรม ใช่หรือไม่?',
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
        callback: function(result) {
          if (result) {
            logSave(v_id, v_ss, v_de);
            window.location.href = 'logout_cls.php';
          }
        }
      });

    });

    function logSave(id, ss, de) {
      $.ajax({
        url: "./logsave.php?id=" + id + "&ss=" + ss + "&de=" + de,
        type: 'get',
        success: function(data) {
          // success
        }

      });

    }
    
  </script>
</header>
<!--  End NavBar -->