<?php
session_start();
if(!isset($_SESSION['id_user'])) {
  header("location: login.php");
}
include_once('./config/db.php');
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
    <?php   
      $page = @$_GET['page'];
      $action = @$_GET['action'];
      $file = '';
      $title = '';
      $script = '';
      if(isset($page)) {
        // ROUTE ADMIN
        if($_SESSION['role'] == 'admin') {
          if($page === '') { //dashboard
            $file = 'admin/dashboard.php';
            $title = 'Dashboard - ';
          } 
          else if($page === 'guru') { //guru
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'admin/guru.php';
              $title = 'Data Guru - ';
              $script .= '<script src="assets/js/guru.js"></script>';
            }
            else if ($action === 'add') {
              $file = 'admin/add_guru.php';
              $title = 'Tambah Data Guru - ';
              $script .= '<script src="assets/js/add_guru.js"></script>';
            } 
            else if ($action === 'edit') {
              $file = 'admin/edit_guru.php';
              $title = 'Ubah Data Guru - ';
              $script .= '<script src="assets/js/edit_guru.js"></script>';
            } 
            else {
              $file = 'admin/guru.php';
              $title = 'Data Guru - ';
              $script .= '<script src="assets/js/guru.js"></script>';
            }
          }
          else if($page === 'siswa') { //siswa
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'admin/siswa.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            } 
            else if ($action === 'edit') {
              $file = 'admin/edit_siswa.php';
              $title = 'Ubah Data Siswa - ';
              $script .= '<script src="assets/js/edit_siswa.js"></script>';
            } 
            else if ($action === 'add') {
              $file = 'admin/add_siswa.php';
              $title = 'Tambah Data Siswa - ';
              $script .= '<script src="assets/js/add_siswa.js"></script>';
            } 
            else {
              $file = 'admin/siswa.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            }
          } 
          else if($page === 'list') { //list
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'admin/list.php';
              $title = 'List Pelanggaran - ';
              $script .= '<script src="assets/js/list.js"></script>';
            }
            else if ($action === 'add') {
              $file = 'admin/add_list.php';
              $title = 'Tambah List Pelanggaran - ';
            }
            else if ($action === 'edit') {
              $file = 'admin/edit_list.php';
              $title = 'Ubah List Pelanggaran - ';
            }
            else {
              $file = 'admin/list.php';
              $title = 'List Pelanggaran - ';
              $script .= '<script src="assets/js/list.js"></script>';
            }
          }
          else if($page === 'pelanggar') { //pelanggaran
            if ($action === '') {
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              $file = 'admin/pelanggaran.php';
              $title = 'Data Pelanggar - ';
              $script .= '<script src="assets/js/pelanggaran.js"></script>';
            }
            else if ($action === 'edit') {
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              $file = 'admin/edit_pelanggaran.php';
              $title = 'Ubah Pelanggar - ';
              $script .= '<script src="assets/js/edit_pelanggaran.js"></script>';
            }
            else {
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              $file = 'admin/pelanggaran.php';
              $title = 'Data Pelanggar - ';
              $script .= '<script src="assets/js/pelanggaran.js"></script>';
            }
          }
          else if($page === 'peringatan') { //peringatan
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'admin/peringatan.php';
            $title = 'Peringatan - ';
            $script .= '<script src="assets/js/peringatan.js"></script>';
          }
          else if($page === 'profile') { //profile
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'profile.php';
            $title = 'Profile - ';
            $script .= '<script src="assets/js/profile.js"></script>';
          }
          else {
            $file = 'admin/dashboard.php';
            $title = 'Dashboard - ';
          }
        }

        // ROUTE user
        if($_SESSION['role'] == 'user') {
          if($_SESSION['posisi'] == 'siswa') {
            if($page === '') {
              $file = 'dashboard.php';
              $title = 'Dashboard - ';
            }
            else if($page === 'profile') { //profile
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              $file = 'profile.php';
              $title = 'Profile - ';
              $script .= '<script src="assets/js/profile.js"></script>';
            }
          }
          else if($_SESSION['posisi'] == 'guru') {
            if($page === '') {
              $file = 'dashboard.php';
              $title = 'Dashboard - ';
            }
            else if($page === 'profile') { //profile
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              $file = 'profile.php';
              $title = 'Profile - ';
              $script .= '<script src="assets/js/profile.js"></script>';
            }
            else if($page === 'siswa') { //siswa
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              if ($action === '') {
                $file = 'admin/siswa.php';
                $title = 'Data Siswa - ';
                $script .= '<script src="assets/js/siswa.js"></script>';
              } 
              else if ($action === 'edit') {
                $file = 'admin/edit_siswa.php';
                $title = 'Ubah Data Siswa - ';
                $script .= '<script src="assets/js/edit_siswa.js"></script>';
              } 
              else if ($action === 'add') {
                $file = 'admin/add_siswa.php';
                $title = 'Tambah Data Siswa - ';
                $script .= '<script src="assets/js/add_siswa.js"></script>';
              } 
              else {
                $file = 'admin/siswa.php';
                $title = 'Data Siswa - ';
                $script .= '<script src="assets/js/siswa.js"></script>';
              }
            } 
            else if($page === 'list') { //list
              $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
              if ($action === '') {
                $file = 'admin/list.php';
                $title = 'List Pelanggaran - ';
                $script .= '<script src="assets/js/list.js"></script>';
              }
              else if ($action === 'add') {
                $file = 'admin/add_list.php';
                $title = 'Tambah List Pelanggaran - ';
              }
              else if ($action === 'edit') {
                $file = 'admin/edit_list.php';
                $title = 'Ubah List Pelanggaran - ';
              }
              else {
                $file = 'admin/list.php';
                $title = 'List Pelanggaran - ';
                $script .= '<script src="assets/js/list.js"></script>';
              }
            }
            else if($page === 'pelanggar') { //pelanggaran
              if ($action === '') {
                $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
                $file = 'admin/pelanggaran.php';
                $title = 'Data Pelanggar - ';
                $script .= '<script src="assets/js/pelanggaran.js"></script>';
              }
              else if ($action === 'edit') {
                $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
                $file = 'admin/edit_pelanggaran.php';
                $title = 'Ubah Pelanggar - ';
                $script .= '<script src="assets/js/edit_pelanggaran.js"></script>';
              }
              else {
                $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
                $file = 'admin/pelanggaran.php';
                $title = 'Data Pelanggar - ';
                $script .= '<script src="assets/js/pelanggaran.js"></script>';
              }
            }
            else {
              $file = 'dashboard.php';
              $title = 'Dashboard - ';
            }
          }
        }
      } else {
        if($_SESSION['role'] == 'admin') {
          $file = 'admin/dashboard.php';
        } else {
          $file = 'dashboard.php';
        }
        $title = 'Dashboard - ';
      }
    ?>
    <title><?= $title ?>Sistem Informasi Siswa</title>
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar  -->
      <?php include('./components/sidebar.php'); ?>
      <!-- Page Content  -->
      <div id="main">
        <!-- Navbar -->
        <?php include('./components/navbar.php'); ?>
        <!-- Userinfo -->
        <div class='py-3 px-4 bg-success text-light fs-5'>Selamat datang <b><?= $_SESSION['nama_lengkap'] ?></b>
        </div>
        <!-- Content -->
        <div id="content">
        <?php
          include('pages/' . $file);
        ?>
        <!-- End div content -->
        </div>
      <!-- End div main -->
      </div>
    <!-- End div wrapper -->
    </div>
    <!-- Script -->
    <?= $script ?>
  </body>
</html>