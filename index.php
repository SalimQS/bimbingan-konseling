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
        if($_SESSION['role'] == 'admin' && $_SESSION['level'] == 'petugas') {
          //---
          if($page === '') { //dashboard
            $file = 'dashboard_admin/index.php';
            $title = 'Dashboard - ';
            $script .= '<script src="assets/js/dashboard.js"></script>';
          } 
          //---
          else if($page === 'kelas') { //dashboard
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'kelas_admin/index.php';
            $title = 'Data Kelas - ';
            $script .= '<script src="assets/js/kelas.js"></script>';
          } 
          //---
          else if($page === 'guru') { //guru
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'guru_admin/index.php';
              $title = 'Data Guru - ';
              $script .= '<script src="assets/js/guru.js"></script>';
            }
            else if ($action === 'add') {
              $file = 'guru_admin/add.php';
              $title = 'Tambah Data Guru - ';
              $script .= '<script src="assets/js/add_guru.js"></script>';
            } 
            else if ($action === 'edit') {
              $file = 'guru_admin/edit.php';
              $title = 'Ubah Data Guru - ';
              $script .= '<script src="assets/js/edit_guru.js"></script>';
            } 
            else {
              $file = 'guru_admin/index.php';
              $title = 'Data Guru - ';
              $script .= '<script src="assets/js/guru.js"></script>';
            }
          }
          //---
          else if($page === 'siswa') { //siswa
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'siswa_admin/index.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            } 
            else if ($action === 'edit') {
              $file = 'siswa_admin/edit.php';
              $title = 'Ubah Data Siswa - ';
              $script .= '<script src="assets/js/edit_siswa.js"></script>';
            } 
            else if ($action === 'add') {
              $file = 'siswa_admin/add.php';
              $title = 'Tambah Data Siswa - ';
              $script .= '<script src="assets/js/add_siswa.js"></script>';
            } 
            else if ($action === 'lihat') {
              $file = 'siswa_admin/review.php';
              $title = 'Lihat Data Siswa - ';
              $script .= '<script src="assets/js/lihat_siswa.js"></script>';
            } 
            else if ($action === 'pelanggaran') {
              $file = 'siswa_admin/add_pelanggaran.php';
              $title = 'Tambah Pelanggaran Siswa - ';
              $script .= '<script src="assets/js/pelanggaran_siswa.js"></script>';
            } 
            else {
              $file = 'siswa_admin/index.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            }
          } 
          //---
          else if($page === 'peraturan') { //list
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'peraturan_admin/index.php';
              $title = 'List Peraturan - ';
              $script .= '<script src="assets/js/list.js"></script>';
            }
            else if ($action === 'add') {
              $file = 'peraturan_admin/add.php';
              $title = 'Tambah List Peraturan - ';
            }
            else if ($action === 'edit') {
              $file = 'peraturan_admin/edit.php';
              $title = 'Ubah List Peraturan - ';
            }
            else {
              $file = 'peraturan_admin/index.php';
              $title = 'List Peraturan - ';
              $script .= '<script src="assets/js/list.js"></script>';
            }
          }
          //---
          else if($page === 'peringatan') { //peringatan
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'peringatan/index.php';
            $title = 'Peringatan - ';
            $script .= '<script src="assets/js/peringatan.js"></script>';
          }
          //---
          else if($page === 'user') { //peringatan
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'user_admin/index.php';
              $title = 'User - ';
              $script .= '<script src="assets/js/user.js"></script>';
            }
            else if ($action === 'add') {
              $file = 'user_admin/add.php';
              $title = 'Tambah User - ';
            }
            else if ($action === 'edit') {
              $file = 'user_admin/edit.php';
              $title = 'Edit User - ';
            }
            else {
              $file = 'user_admin/index.php';
              $title = 'User - ';
              $script .= '<script src="assets/js/user.js"></script>';
            }
          }
          //---
          else {
            $file = 'dashboard_admin/index.php';
            $title = 'Dashboard - ';
            $script .= '<script src="assets/js/dashboard.js"></script>';
          }
        }
        //kepsek dan bk
        else if($_SESSION['role'] == 'admin' && ($_SESSION['level'] == 'kepsek' || $_SESSION['level'] == 'bk')) {
          //---
          if($page === '') { //dashboard
            $file = 'dashboard_not_admin/index.php';
            $title = 'Dashboard - ';
            $script .= '<script src="assets/js/dashboard.js"></script>';
          }
          //---
          else if($page === 'kelas') { //dashboard
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'kelas_not_admin/index.php';
            $title = 'Data Kelas - ';
            $script .= '<script src="assets/js/kelas.js"></script>';
          } 
          //---
          else if($page === 'guru') { //guru
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'guru_not_admin/index.php';
            $title = 'Data Guru - ';
            $script .= '<script src="assets/js/guru.js"></script>';
          }
          //---
          else if($page === 'siswa') { //siswa
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            if ($action === '') {
              $file = 'siswa_not_admin/index.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            } 
            else if ($action === 'lihat') {
              $file = 'siswa_not_admin/review.php';
              $title = 'Lihat Data Siswa - ';
              $script .= '<script src="assets/js/lihat_siswa.js"></script>';
            } 
            else if ($action === 'pelanggaran') {
              $file = 'siswa_not_admin/add_pelanggaran.php';
              $title = 'Tambah Pelanggaran Siswa - ';
              $script .= '<script src="assets/js/pelanggaran_siswa.js"></script>';
            } 
            else {
              $file = 'siswa_not_admin/index.php';
              $title = 'Data Siswa - ';
              $script .= '<script src="assets/js/siswa.js"></script>';
            }
          } 
          //---
          else if($page === 'peraturan') { //list
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'peraturan_not_admin/index.php';
            $title = 'List Peraturan - ';
            $script .= '<script src="assets/js/list.js"></script>'; 
          }
          //---
          else if($page === 'peringatan') { //peringatan
            $script = '<script src="vendors/sweetalert/sweetalert.min.js"></script>';
            $file = 'peringatan/index.php';
            $title = 'Peringatan - ';
            $script .= '<script src="assets/js/peringatan.js"></script>';
          }
          //---
          else {
            $file = 'dashboard_not_admin/index.php';
            $title = 'Dashboard - ';
            $script .= '<script src="assets/js/dashboard.js"></script>';
          }
        }
        else if($_SESSION['role'] == 'guru' && $_SESSION['level'] == 'walikelas') {
          if($page === '') { //dashboard
            $file = 'dashboard_wali_kelas/index.php';
            $title = 'Dashboard - ';
          }
          else if($page === 'siswa') { //dashboard
            if($action === '') {
              $file = 'siswa_wali_kelas/index.php';
              $title = 'Data Siswa - ';
            }
            else if($action === 'lihat') {
              $file = 'siswa_wali_kelas/review.php';
              $title = 'Data Siswa - ';
            }
            else {
              $file = 'siswa_wali_kelas/index.php';
              $title = 'Data Siswa - ';
            }
          }
          else if($page === 'peraturan') { //dashboard
            $file = 'peraturan_guru/index.php';
            $title = 'Peraturan - ';
          }
          else if($page === 'profile') { //dashboard
            $file = 'profile_guru/index.php';
            $title = 'Profile - ';
          }
          else {
            $file = 'dashboard_wali_kelas/index.php';
            $title = 'Dashboard - ';
          }
        }
        else if($_SESSION['role'] == 'guru' && $_SESSION['level'] == 'guru') {
          if($page === '') { //dashboard
            $file = 'peraturan_guru/index.php';
            $title = 'Dashboard - ';
          }
          else if($page === 'profile') { //dashboard
            $file = 'profile_guru/index.php';
            $title = 'Profile - ';
          }
          else {
            $file = 'peraturan_guru/index.php';
            $title = 'Dashboard - ';
          }
        }
      } else {
        if($_SESSION['role'] == 'admin' && $_SESSION['level'] == 'petugas') {
          $file = 'dashboard_admin/index.php';
          $title = 'Dashboard - ';
          $script .= '<script src="assets/js/dashboard.js"></script>';
        }
        else if($_SESSION['role'] == 'admin' && ($_SESSION['level'] == 'kepsek' || $_SESSION['level'] == 'bk')) {
          $file = 'dashboard_not_admin/index.php';
          $title = 'Dashboard - ';
          $script .= '<script src="assets/js/dashboard.js"></script>';
        }
        else if($_SESSION['role'] == 'guru' && $_SESSION['level'] == 'walikelas') {
          $file = 'dashboard_wali_kelas/index.php';
          $title = 'Dashboard - ';
        }
        else {
          $file = 'peraturan_guru/index.php';
          $title = 'Dashboard - ';
        }
      }
    ?>
    <title><?= $title ?>Pelanggaran Siswa</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <?= $script ?>
  </head>
  <body>
    <div class="wrapper">
      <?php include('./components/sidebar.php'); ?>
      <div id="main">
        <?php include('./components/navbar.php'); ?>
        <div class='py-3 px-4 bg-success text-light fs-5'>Selamat datang <b><?= $_SESSION['nama_lengkap'] ?></b>,
        <?php
        if($_SESSION['role'] == 'admin') {
          if($_SESSION['level'] == 'petugas') {
            $jabatan = 'Petugas';
          }
          else if($_SESSION['level'] == 'bk') {
            $jabatan = 'Guru BK';
          }
          else if($_SESSION['level'] == 'kepsek') {
            $jabatan = 'Kepala Sekolah';
          }
          else if($_SESSION['level'] == 'walikelas') {
            $jabatan = 'Wali Kelas';
          }
          echo("Anda Login sebagai <b>$jabatan</b>.");
        }
        else if($_SESSION['role'] == 'guru') {
          if($_SESSION['level'] == "walikelas") {
            $kelas = $_SESSION['kelas'];
            echo("Anda Login sebagai <b>Wali Kelas $kelas</b>.");
          }
          else echo("Anda Login sebagai <b>Guru</b>.");
        }
        ?>
        </div>
        <div id="content">
        <?php
          include('pages/' . $file);
        ?>
        </div>
      </div>
    </div>
  </body>
</html>