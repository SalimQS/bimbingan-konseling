<?php
session_start();
$error = false;
$errorText = "";
if(isset($_SESSION['id_user'])) {
  header("location: index.php");
} 
else {
  include_once('./config/db.php');
  //---
  $query = "SELECT admin FROM users WHERE admin='admin'";
  $result = $connect->query($query);
  $admins = $result->num_rows;
  if($admins >= 2) {
    header("location: index.php");
  }
  else {
    if(isset($_POST['submit'])) {
      $error = true;
      $usernames = $_POST['usernames'];
      $name = $_POST['name'];
      $passwords = $_POST['passwords'];
      $passwords2 = $_POST['passwords2'];
      $password = md5($passwords);

      if($usernames == '') {
        $errorText = "NIckname tidak boleh kosong";
      } else if($name == '') {
        $errorText = "Nama lengkap tidak boleh kosong";
      } else if($passwords == '') {
        $errorText = "Password tidak boleh kosong";
      } else if($passwords2 == '') {
        $errorText = "Password tidak boleh kosong";
      } else if($passwords != $passwords2) {
        $errorText = "Password tidak sama";
      } else {
        $error = false;
        $connect->begin_transaction();
        
        try {
          $query = "SELECT id FROM users WHERE username ='$usernames'";
          $result = $connect->query($query);
          if($result->num_rows > 0) {
            $error = true;
            $errorText = "Nickname sudah terdaftar";
          } else {
            $error = false;
            //---
            if(!$error) {
              $query = "INSERT INTO users (admin, username, password) ";
              $query .= "VALUES('admin', '$usernames', '$password')";
              $result = $connect->query($query);
              if($result === TRUE) {
                $id_user = $connect->insert_id;
                //---
                $query = "INSERT INTO guru (id_user) ";
                $query .= "VALUES('$id_user')";
                $result = $connect->query($query);
                //---
                if($result) {
                  $query = "INSERT INTO data (id_user, nama_lengkap, status) ";
                  $query .= "VALUES('$id_user', '$name', '1')";
                  $result = $connect->query($query);
                  if($result) {
                    $error = false;
                    $connect->commit();
                    header("location: login.php?crg=1");
                  } else {                 
                      $error = true;
                      $errorText = "Gagal Mendaftar : $connect->error";
                      $connect->rollback();
                  }
                } else {
                  $error = true;
                  $errorText = "Gagal Mendaftar : " . $connect->error;
                }
              }
            }
          }
        } catch (exception $e) {
          print_r($e);
          $connect->rollback();
          $error = true;
          $errorText = $e;
        }
      }
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="./vendors/bootstrap-5.0.0-beta3-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- jQuery 3.6.0 -->
    <script defer src="./vendors/jQuery-3.6.0/jQuery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script defer src="./vendors/bootstrap-5.0.0-beta3-dist/js/bootstrap.bundle.min.js"></script>
    <!-- FontAwesome -->
    <script defer src="./vendors/fontawesome-free-5.15.3-web/js/all.min.js"></script>
    <!-- Script JS -->
    <script defer src="./assets/js/script.js"></script>

    <script src="vendors/sweetalert/sweetalert.min.js"></script>
    <script>
      window.onload = () => {
        var height = $(".login-left").height();
        $(".login-left").width(height + 100);
      }
    </script>
</head>
<body class="login-bg">
  <div class="container">
    <div class="row justify-content-center main-container">
            <div class="wrap d-md-flex">
            <!--kiri-->
            <div class="img login-left"></div>
            <!--kanan-->
            <div class="login-wrap login-right">
                <div class="d-flex">
                <div class="w-100">
                    <h3 class="mb-4"><b>Admin Sign Up</b></h3>
                </div>
                </div>
                <form method="post" class="signup-form" autocomplete="off"> 
                  <input autocomplete="false" name="hidden" type="text" style="display:none;">
                  <div class="form-group mb-3">
                      <label class="label" for="usernames">Username</label>
                      <input id="usernames" type="text" name="usernames" class="form-control" placeholder="Username" required>
                  </div>
                  <div class="form-group mb-3">
                      <label class="label" for="name">Nama Lengkap</label>
                      <input id="name" type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
                  </div>
                  <div class="form-group mb-3">
                      <label class="label" for="passwords">Password</label>
                      <input id="passwords" type="password" name="passwords" class="form-control" placeholder="Password" required>
                  </div>
                  <div class="form-group mb-3">
                      <label class="label" for="passwords2">Ulangi Password</label>
                      <input id="passwords2" type="password" name="passwords2" class="form-control" placeholder="Ulangi Password" required>
                  </div>
                  <div class="form-group">
                      <input type="submit" name="submit" class="form-control btn rounded submit submit-btn" value="Sign Up">
                      <a class="btn form-control btn-primary" type="btn" href="login.php" style="margin-top: 10px;">Kembali</a>
                  </div>
                  <?php if($error) { ?>
                      <script type="text/javascript">
                      //window.onload = () => {
                          Swal.fire({
                          title: "Gagal Daftar!",
                          text: "<?= $errorText ?>",
                          icon: 'error'
                          });
                      //}
                      </script>
                  <?php } ?>
                </form>
            </div>
            </div>
        </div>
    </div>
</body>
</html>

