<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=user"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $error = false;
  $errorText = "";

  if(isset($_POST['simpan'])) {
    $error = true;
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $level = $_POST['level'];

    if($username == '') {
      $errorText = "Username tidak boleh kosong";
    } else if($fullname == '') {
      $errorText = "Nama Lengkap tidak boleh kosong";
    } else if($password == '') {
      $errorText = "Password tidak boleh kosong";
    } else if($level == '-1') {
      $errorText = "Level tidak boleh kosong";
    } else {
      $error = false;
      $connect->begin_transaction();
      
      try {
        $query = "SELECT * FROM `admin` WHERE `username_admin`='$username'";
        $result = $connect->query($query);
        if($result) {
          if($result->num_rows > 0) {
            $error = true;
            $errorText = "Gagal! Username digunakan oleh orang lain";
          }
          else {
            $query = "INSERT INTO `admin` (`nama_admin`, `username_admin`, `password_admin`, `level_admin`) VALUES ('$fullname','$username','$password','$level')";
            $result = $connect->query($query);
            if($result) {
              $error = false;
              $connect->commit();
              ?>
              <script src="vendors/sweetalert/sweetalert.min.js"></script>
              <script type="text/javascript">
                Swal.fire({
                  title: "Sukses!",
                  text: "Berhasil menambahkan Admin",
                  icon: 'success'
                }).then(() => {    
                window.location = "index.php?page=user";
                });
              </script>
              <?php
            } 
            else {
              $error = true;
              $errorText = "Gagal menambahkan Admin : " . $connect->error;
            }
          }
        }
        else {
          $error = true;
          $errorText = "Gagal menambahkan Admin : " . $connect->error;
        }
      } catch (exception $e) {
        print_r($e);
        $connect->rollback();
        $error = true;
        $errorText = $e;
      }
    }
  }
?>
<form method="POST" enctype="multipart/form-data">
  <div class="row justify-content-md-center">
    <div class="col-md-5 border-right">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Tambah Akun Admin</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="row mt-2">
            <div class="col-md-12 mb-2">
                <label class="labels">Username Admin</label>
                <input type="text" class="form-control" placeholder="Username Admin" name='username'>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Nama Lengkap Admin</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap Admin" name='fullname'>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Password Admin</label>
                <input type="text" class="form-control" placeholder="Password Admin" name='password'>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Level Akun Admin</label>
                <select name="level" class="form-select">
                    <option value="-1">Pilih Level Akun</option>
                    <option value="bk">Guru BK</option>
                    <option value="kepsek">Kepala Sekolah</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 col-md-12 text-right">
                <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</form>  