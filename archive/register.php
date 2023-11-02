<?php
session_start();
$error = false;
if(isset($_SESSION['nim'])) {
  header("location: index.php");
} else {
      include_once('./config/db.php');
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
    <title>Daftar - Sistem Informasi Siswa</title>
    <style>
      html, body {
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid h-100">
      <div class="row h-100">
        <div class="col-sm-0 col-md-4 d-none d-md-block bg-primary position-relative">
          <img class='w-75 exact-center' src="./assets/img/login.svg"/>
        </div>
        <?php
          $error = false;
          $errorText = "";
          $warning = false;
          $warningText = "";
          $success = false;
          if(isset($_POST['submit'])) {
            $warning = true;
            if($_POST['nama_lengkap'] == '') {
              $warningText = "Nama lengkap tidak boleh kosong";
            } else if($_POST['jenis_kelamin'] == '-1') {
              $warningText = "Jenis kelamin tidak boleh kosong";
            } else if($_POST['tempat_lahir'] == '') {
              $warningText = "Tempat lahir tidak boleh kosong";
            } else if($_POST['tanggal_lahir'] == '') {
              $warningText = "Tanggal lahir tidak boleh kosong";
            } else if($_POST['golongan_darah'] == '-1') {
              $warningText = "Golongan darah tidak boleh kosong";
            } else if($_POST['agama'] == '-1') {
              $warningText = "Agama tidak boleh kosong";
            } else if($_POST['no_telepon'] == '') {
              $warningText = "No Telepon tidak boleh kosong";
            } else if($_POST['alamat'] == '') {
              $warningText = "Alamat tidak boleh kosong";
            } else if($_POST['nim'] == '') {
              $warningText = "NIM tidak boleh kosong";
            } else if($_POST['prodi'] == '-1') {
              $warningText = "Program Studi tidak boleh kosong";
            } else if($_POST['dosen'] == '-1') {
              $warningText = "Dosen wali tidak boleh kosong";
            } else {
              $warning = false;
              $query = "SELECT nim FROM biodata WHERE nim='".$_POST['nim']."'";
              $result = $connect->query($query);
              $error = true;
              if($result->num_rows > 0) {
                $errorText = "NIM sudah terdaftar, silahkan gunakan NIM lain";
              } else {
                $nim = $_POST['nim'];
                $nama_lengkap = $_POST['nama_lengkap'];
                $jenis_kelamin = $_POST['jenis_kelamin'];
                $tempat_lahir = $_POST['tempat_lahir'];
                $tanggal_lahir = $_POST['tanggal_lahir'];
                $golongan_darah = $_POST['golongan_darah'];
                $agama = $_POST['agama'];
                $alamat = $_POST['alamat'];
                $no_telp = $_POST['no_telepon'];
                $dosen = $_POST['dosen'];
                $prodi = $_POST['prodi'];
                $password = md5($_POST['nim']);
                $query = "INSERT INTO biodata (nim, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, golongan_darah, agama, alamat, no_telepon, id_dosen, id_prodi, semester, status, password) VALUES ";
                $query .= "('$nim', '$nama_lengkap', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$golongan_darah', '$agama', '$alamat', '$no_telp', '$dosen', '  $prodi', '1', 1, '$password')";
                $result = $connect->query($query);
                if($result) {
                  $error = false;
                  $success = true;
                } else {
                  $errorText = "kesalahan saat mendaftar!";
                }
              }
            }
          }
        ?>
        <div class="col-sm-12 col-md-8">
          <div class="p-5">
            <h3 class="fw-bold mb-5">Register</h3>
            <form method="POST">
            <?php
              if($error) {
            ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
              <div><strong>Gagal!</strong> <?= $errorText ?></div>
            </div>
            <?php 
              }
            ?>
            <?php
              if($warning) {
            ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
              <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
              <div><strong>Perhatian!</strong> <?= $warningText ?></div>
            </div>
            <?php 
              }
            ?>
            <?php
              if($success) {
            ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
              <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
              <div><strong>Berhasil!</strong> Akun berhasil didaftarkan, silahkan <a href='login.php' class='fw-bold fst-italic'>masuk</a> dengan NIM dan password <b><?=$_POST['nim']?></b></div>
            </div>
            <?php 
              }
            ?>
            <div class="row mt-2">
              <div class="d-flex justify-content-between align-items-center">
                  <h6 class="text-right">Biodata Siswa</h6>
              </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Nama Lengkap</label>
                  <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap' value="<?= @$_POST['nama_lengkap'] ?>">
                </div>
                <div class="col-md-4 mb-2">
                  <label class="labels">Jenis Kelamin</label>
                  <select class="form-select" aria-label="Jenis Kelamin" name='jenis_kelamin'>
                      <option value='-1'>Pilih Jenis Kelamin</option>
                      <option value="0" <?= @$_POST['jenis_kelamin'] == '0' ? 'selected' : ''?>>Laki-laki</option>
                      <option value="1" <?= @$_POST['jenis_kelamin'] == '1' ? 'selected' : ''?>>Perempuan</option>
                  </select>
                </div>
                <div class="col-md-4 mb-2">
                  <label class="labels">Tempat Lahir</label>
                  <input type="text" class="form-control" placeholder="Tempat lahir" name='tempat_lahir' value="<?= @$_POST['tempat_lahir'] ?>">
                </div>
                <div class="col-md-4 mb-2">
                  <label class="labels">Tanggal Lahir</label>
                  <input type="date" class="form-control" placeholder="Tanggal Lahir" name='tanggal_lahir' value="<?= @$_POST['tanggal_lahir'] ?>">
                </div>
                <div class="col-md-4 mb-2">
                <label class="labels">Golongan Darah</label>
                  <select class="form-select" aria-label="Golongan Darah" name='golongan_darah'>
                      <option  value='-1'>Pilih Golongan Darah</option>
                      <option value="0" <?= @$_POST['golongan_darah'] == '0' ? 'selected' : ''?>>A</option>
                      <option value="1" <?= @$_POST['golongan_darah'] == '1' ? 'selected' : ''?>>B</option>
                      <option value="2" <?= @$_POST['golongan_darah'] == '2' ? 'selected' : ''?>>AB</option>
                      <option value="3" <?= @$_POST['golongan_darah'] == '3' ? 'selected' : ''?>>O</option>
                  </select>
                </div>
                <div class="col-md-4 mb-2">
                <label class="labels">Agama</label>
                  <select class="form-select" aria-label="Agama" name='agama'>
                      <option  value='-1'>Pilih Agama</option>
                      <option value="0" <?= @$_POST['agama'] == '0' ? 'selected' : ''?>>Islam</option>
                      <option value="1" <?= @$_POST['agama'] == '1' ? 'selected' : ''?>>Kristen</option>
                      <option value="2" <?= @$_POST['agama'] == '2' ? 'selected' : ''?>>Katholik</option>
                      <option value="3" <?= @$_POST['agama'] == '3' ? 'selected' : ''?>>Hindu</option>
                      <option value="4" <?= @$_POST['agama'] == '4' ? 'selected' : ''?>>Budha</option>
                      <option value="5" <?= @$_POST['agama'] == '5' ? 'selected' : ''?>>Konghucu</option>
                      <option value="6" <?= @$_POST['agama'] == '6' ? 'selected' : ''?>>Protestan</option>
                  </select>
                </div>
                <div class="col-md-4 mb-4">
                  <label class="labels">No Telepon</label>
                  <input type="text" class="form-control" placeholder="Nomor Telepon" name="no_telepon" value="<?= @$_POST['no_telepon'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Alamat</label>
                  <textarea class="form-control" id="alamat" rows="3" name='alamat'><?= @$_POST['alamat'] ?></textarea>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="text-right">Informasi Akademik</h6>
            </div>
            <?php
            
            $query = "SELECT prodi.id, CONCAT(fakultas.nama_fakultas, ' - ', prodi.nama_prodi) as nama_prodi FROM prodi ";
            $query .= "LEFT JOIN fakultas ON fakultas.id = prodi.id_fakultas ";
            $query .= "ORDER BY nama_fakultas";
            $result = $connect->query($query);
            $prodi = [];
            $dosen = [];
            if($result->num_rows > 0) {
              $prodi = $result->fetch_all(MYSQLI_ASSOC);
            }
            $query = "SELECT * from dosen";
            $result = $connect->query($query);
            if($result->num_rows > 0) {
              $dosen = $result->fetch_all(MYSQLI_ASSOC);
            }
            ?>
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                  <label class="labels">NIM</label>
                  <input type="text" class="form-control" placeholder="Nomor Induk Siswa" name='nim' value="<?= @$_POST['nim'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Program Studi</label>
                  <select class="form-select" aria-label="Program Studi" name='prodi'>
                      <option value='-1'>Pilih Program Studi</option>
                      <?php                        
                        if(count($prodi) == 0) {
                            echo "<option selected>Tidak ada program studi</option>";
                        }
                        foreach ($prodi as $item) {
                           $selected = @$_POST['prodi'] == $item['id'] ? 'selected' : '';
                            echo "<option value='".$item['id']."' " . $selected . ">" . $item['nama_prodi'] . "</option>";
                        }
                      ?>
                  </select>
                </div>
                <div class="col-md-12 mb-4">
                  <label class="labels">Dosen Wali</label>
                  <select class="form-select" aria-label="Dosen Wali" name='dosen'>
                      <option value='-1'>Pilih Dosen Wali</option>
                      <?php                        
                        if(count($dosen) == 0) {
                            echo "<option selected>Tidak ada dosen wali</option>";
                        }
                        foreach ($dosen as $item) {
                           $selected = @$_POST['dosen'] == $item['nip'] ? 'selected' : '';
                            echo "<option value='".$item['nip']."' " . $selected . ">" . $item['nama_dosen'] . "</option>";
                        }
                      ?>
                  </select>
                </div>
            </div>
            <div class="d-grid mb-4">
              <button class="btn btn-primary" name='submit' type="submit">Daftar</button>
            </div>
            <div class="mb-3 text-center">
              Sudah punya akun? <a href="login.php" class='fs-6 link-primary'>Masuk</a>
            </div>
         </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>