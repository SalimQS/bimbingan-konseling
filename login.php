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
  if(isset($submit)) {
    if($username == '' || $password == '') {
      $error = true;
      $errorText = "Masukkan nama dan password";
    } 
    else {
      include_once('./config/db.php');
      //---
      $query = "SELECT id, admin FROM users WHERE username='$username' AND password='$encodedPassword'";
      $result = $connect->query($query);
      if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        //---
        $id_user = $user['id']; 
        $role = $user['admin'];
        $posisi = "none";
        //---
        $query = "SELECT `id` FROM `guru` WHERE id_user = '$id_user'";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $posisi = "guru";
        }
        //---
        $query = "SELECT `id` FROM `siswa` WHERE id_user = '$id_user'";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $posisi = "siswa";
        }
        //---
        $query = "SELECT nama_lengkap, status FROM data WHERE id_user = '$id_user'";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $biodata = $result->fetch_assoc();
          if($biodata['status']) {
            $_SESSION['id_user'] = $id_user;
            $_SESSION['nama_lengkap'] = $biodata['nama_lengkap'];
            $_SESSION['role'] = $role;
            $_SESSION['posisi'] = $posisi;
            header("location: index.php");
          } 
          else {
            $error = true;
            $errorText = "Akun ini tidak aktif, silahkan hubungi administrator";
          }
        }
      } 
      else {
        $error = true;
        $errorText = "nama atau password salah";
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
</head>
<body style="background-color: #182869;">
  <div class="container">
    <div class="row justify-content-center">
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
              <?php
                if($error) {
              ?>
                <div class="form-group d-md-flex">
                  <div class="w-50 text-md-left">
                      <small><p>Gagal! <?= $errorText ?></p></small>
                  </div>
                </div>
              <?php 
                }
              ?>
            </form>
          </div>

        </div>
    </div>
  </div>
</body>
</html>

