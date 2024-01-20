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
  $submit = @$_POST['submit'];
  if(isset($submit)) {
    $username = @$_POST['username'];
    $password = @$_POST['password'];
    $login_as = @$_POST['login_as'];
    //---
    if($login_as == 0) {//guru login
      if($username == '' || $password == '') {
        $error = true;
        $errorText = "Masukkan nama dan password";
      } 
      else {
        $query = "SELECT * FROM guru WHERE username_guru='$username' LIMIT 1";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $user = $result->fetch_assoc();
          if($user['password_guru'] == $password) {
            //---
            $query = "SELECT * FROM kelas WHERE id_wali_kelas='".$user['id_guru']."' LIMIT 1";
            $result = $connect->query($query);
            if($result->num_rows > 0) {
              $kelas = $result->fetch_assoc();
              $_SESSION['id_user'] = $user['id_guru'];
              $_SESSION['username'] = $user['username_guru'];
              $_SESSION['nama_lengkap'] = $user['nama_guru'];
              $_SESSION['role'] = "guru";
              $_SESSION['level'] = "walikelas";
              $_SESSION['kelas'] = $kelas['nama_kelas'];
              header("location: index.php");
            }
            else {
              $_SESSION['id_user'] = $user['id_guru'];
              $_SESSION['username'] = $user['username_guru'];
              $_SESSION['nama_lengkap'] = $user['nama_guru'];
              $_SESSION['role'] = "guru";
              $_SESSION['level'] = "guru";
              header("location: index.php");
            }
          }
          else {
            $error = true;
            $errorText = "Password salah!";
          }
        } 
        else {
          $error = true;
          $errorText = "Akun ini tidak terdaftar sebagai Guru!";
        }
      }
    }
    else {//admin login
      if($username == '' || $password == '') {
        $error = true;
        $errorText = "Masukkan nama dan password";
      } 
      else {
        $query = "SELECT * FROM admin WHERE username_admin='$username' LIMIT 1";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $user = $result->fetch_assoc();
          if($user['password_admin'] == $password) {
            //---
            $_SESSION['id_user'] = $user['id_admin'];
            $_SESSION['username'] = $user['username_admin'];
            $_SESSION['nama_lengkap'] = $user['nama_admin'];
            $_SESSION['role'] = "admin";
            $_SESSION['level'] = $user['level_admin'];
            header("location: index.php");
          }
          else {
            $error = true;
            $errorText = "Password salah!";
          }
        } 
        else {
          $error = true;
          $errorText = "Akun ini tidak terdaftar sebagai Admin!";
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
  <div class="m-3">
    <a class="btn btn-primary shadow-lg" href="nisn_check.php">Cek Data Dengan NISN</a>
  </div>
  <div class="container">
    <div class="row justify-content-center main-container">
        <div class="wrap d-md-flex">
          <!--kiri-->
          <div class="login-left"></div>
          <!--kanan-->
          <div class="login-wrap login-right">
            <div class="d-flex">
              <div class="w-100">
                <h3 class="mb-4"><b>Login</b></h3>
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
              <div class="form-group mb-3">
                  <label class="label" for="login_as">Login Sebagai</label><br>
                  <select class="form-select" name="login_as" id="login_as">
                    <option value="0" selected>Guru</option>
                    <option value="1">Admin</option>
                  </select>
              </div>
              <div class="form-group">
                <input type="submit" name="submit" class="form-control btn rounded submit submit-btn" value="Sign In">
              </div>
              <?php if($error) { ?>
                <script type="text/javascript">
                    Swal.fire({
                      title: "Gagal Login!",
                      text: "<?= $errorText ?>",
                      icon: 'error'
                    });
                </script>
              <?php } ?>
            </form>
          </div>
        </div>
    </div>
  </div>
</body>
</html>

