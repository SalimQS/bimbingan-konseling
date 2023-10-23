<?php
session_start();
$error = false;
if (isset($_SESSION['id_user'])) {
  header("location: index.php");
} else {
  $submit = @$_POST['submit'];
  $username = @$_POST['username'];
  $password = @$_POST['password'];
  $encodedPassword = md5($password);
  if (isset($submit)) {
    if($username == '' || $password == '') {
      $error = true;
    } else {
      include_once('./config/db.php');
      $query = "SELECT users.id, roles.nama_role FROM users ";
      $query .= "LEFT JOIN roles ON roles.id = users.id_role ";
      $query .= "WHERE username='$username' AND password='$encodedPassword'";
      $result = $connect->query($query);
      if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $id_user = $user['id'];
        $role = $user['nama_role'];
        $query = "SELECT nama_lengkap, status FROM biodata WHERE id_user = '$id_user'";
        $result = $connect->query($query);
        if ($result->num_rows > 0) {
          $biodata = $result->fetch_assoc();
          if ($biodata['status']) {
            $_SESSION['id_user'] = $id_user;
            $_SESSION['nama_lengkap'] = $biodata['nama_lengkap'];
            $_SESSION['role'] = $role;
            if ($role === 'siswa') {
              $query = "SELECT prodi.nama_prodi FROM siswa ";
              $query .= "LEFT JOIN prodi ON prodi.id = siswa.id_prodi ";
              $query .= "WHERE id_user = '$id_user'";
              $result = $connect->query($query);
              if ($result->num_rows > 0) {
                $prodi = $result->fetch_assoc();
                $_SESSION['nama_prodi'] = $prodi['nama_prodi'];
              }
            }
            header("location: index.php");
          } else {
            $error = true;
            $errorText = "Akun ini tidak aktif, silahkan hubungi administrator";
          }
        }
      } else {
        $error = true;
        $errorText = "NIM atau password salah";
      }
    }
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

    <style>
      html, body {
        height: 100%;
      }
      .container-fluid, .row, .full-height {
        height: 100%;
      }
    </style>

    <title>Masuk - Sistem Informasi Siswa</title>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-0 col-md-4 d-none d-md-block bg-primary full-height position-relative">
          <img class='w-75 exact-center' src="./assets/img/login.svg"/>
        </div>
        <div class="col-sm-12 col-md-8 full-height">
          <div class="p-5">
            <h3 class="fw-bold mb-4">Login</h3>
            <?php
              if ($error) {
            ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
              <div><strong>Gagal!</strong> <?= $errorText ?></div>
            </div>
            <?php 
              }
            ?>
            <form method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username (NIM/NIP)</label>
                <input type="text" class="form-control" name="username" value="admin" placeholder="Username (NIM/NIP)">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" value="admin" placeholder="*******">
              </div>
              <div class="d-grid mb-4">
                <button class="btn btn-primary" name='submit' type="submit">Masuk</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>