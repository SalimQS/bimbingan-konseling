<?php
session_start();
$error = false;
if(isset($_SESSION['id_user'])) {
  header("location: index.php");
} 
else {
  $submit = @$_POST['submit'];
  $username = @$_POST['username'];
  $password = @$_POST['password'];
  $encodedPassword = md5($password);
  //---
  include_once('./config/db.php');
  //---
  $query = "SELECT jabatan FROM users WHERE jabatan='kesiswaan'";
  $result = $connect->query($query);
  $admins = $result->num_rows;
  //---
  if(isset($submit)) {
    if($username == '' || $password == '') {
      $error = true;
      $errorText = "Masukkan nama dan password";
    } 
    else {
      $query = "SELECT * FROM users WHERE username='$username' AND password='$encodedPassword'";
      $result = $connect->query($query);
      if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        //---
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['fullname'];
        $_SESSION['role'] = $user['jabatan'];
        header("location: index.php");
      } 
      else {
        $error = true;
        $errorText = "Nama atau Password salah";
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
  <div class="m-3">
    <a class="btn btn-primary shadow-lg" href="main.php">Cek Data Dengan NISN</a>
  </div>
  <div class="container">
    <div class="row justify-content-center main-container">
        <div class="wrap d-md-flex">
          <!--kiri-->
          <div class="img login-left"></div>
          <!--kanan-->
          <div class="login-wrap login-right">
            <div class="d-flex">
              <div class="w-100">
                <h3 class="mb-4"><b>Sign In</b></h3>
              </div>
            </div>
            <form method="post" class="signin-form">
              <div class="form-group mb-3">
                <label class="label" for="name">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
              </div>
              <div class="form-group mb-3">
                  <label class="label" for="password">Password</label>
                  <input type="password" name="password" class="form-control" placeholder="Password" required>
              </div>
              <div class="form-group">
                <input type="submit" name="submit" class="form-control btn rounded submit submit-btn" value="Sign In">
              </div>
              <?php if($admins < 2) { ?>
                <a class="btn form-control btn-primary btn-admin" type="btn" href="admin_reg.php">Daftar Menjadi Admin</a>
              <?php } if($error) { ?>
                <script type="text/javascript">
                  //window.onload = () => {
                    Swal.fire({
                      title: "Gagal Login!",
                      text: "<?= $errorText ?>",
                      icon: 'error'
                    });
                  //}
                </script>
              <?php } if(isset($_GET['crg'])) { ?>
                <script type="text/javascript">
                  //window.onload = () => {
                    Swal.fire({
                      title: "Sukses!",
                      text: "Anda berhasil mendaftar, silahkan login",
                      icon: 'success'
                    }).then(() => {
                      window.location = "login.php";
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

