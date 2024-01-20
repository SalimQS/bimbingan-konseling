<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=guru"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $error = false;
  $errorText = "";

  if(isset($_POST['simpan'])) {
    $error = true;
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $no_telepon = $_POST['no_telepon'];
    $nuptk = $_POST['nuptk'];

    if($nama_lengkap == '') {
      $errorText = "Nama lengkap tidak boleh kosong";
    } else if($username == '') {
      $errorText = "Username tidak boleh kosong";
    } else if($password == '') {
      $errorText = "Password tidak boleh kosong";
    } else if($jenis_kelamin == '-1') {
      $errorText = "Jenis kelamin tidak boleh kosong";
    } else if($no_telepon == '') {
      $errorText = "No telepon tidak boleh kosong";
    } else if($nuptk == '') {
      $errorText = "NIP atau NUPTK harus diisi salah satunya";
    } else {
      $error = false;
      $foto = $_FILES['foto'];
      $hasFoto = $foto['size'] > 0;
      $file = '';
      $connect->begin_transaction();
      
      try {
        $error = false;
        if($hasFoto) {
          $fileName = "uploads/" . basename($_FILES['foto']['name']);
          $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
          $file = "uploads/" . time() . "." . $ext;
          $maxSize = 2097152;//2 mb
          $imgSize = getimagesize($_FILES['foto']['tmp_name']);
          if(($_FILES['foto']['size'] >= $maxSize)) {
            $error = true;
            $errorText = "Ukuran foto maksimal 2MB";
          }
        } 
        else {
          $file = "assets/img/default.jpg";
        }

        if(!$error) {
          if($hasFoto) {
            $query = "INSERT INTO guru (nuptk, nama_guru, username_guru, password_guru, foto_guru, jenis_kelamin, no_telepon) ";
            $query .= "VALUES('$nuptk', '$nama_lengkap', '$username', '$password', '$file', '$jenis_kelamin', '$no_telepon')";
            $result = $connect->query($query);
            if($result) {
              if(move_uploaded_file($_FILES['foto']['tmp_name'], $file)) {
                $error = false;
                $connect->commit();
                ?>
                <script src="vendors/sweetalert/sweetalert.min.js"></script>
                <script type="text/javascript">
                  Swal.fire({
                    title: "Sukses!",
                    text: "Berhasil menambahkan guru",
                    icon: 'success'
                  }).then(() => {    
                  window.location = "index.php?page=guru";
                  });
                </script>
                <?php
              } 
              else {
                $error = true;
                $errorText = "Foto gagal diupload";
                $connect->rollback();
              }
            } 
            else {
                $error = true;
                $errorText = "Gagal menambah guru";
                $connect->rollback();
            }
          } 
          else {
            $query = "INSERT INTO guru (nuptk, nama_guru, username_guru, password_guru, foto_guru, jenis_kelamin, no_telepon) ";
            $query .= "VALUES('$nuptk', '$nama_lengkap', '$username', '$password', '$file', '$jenis_kelamin', '$no_telepon')";
            $result = $connect->query($query);
            if($result) {
              $error = false;
              $connect->commit();
              ?>
              <script src="vendors/sweetalert/sweetalert.min.js"></script>
              <script type="text/javascript">
                Swal.fire({
                  title: "Sukses!",
                  text: "Berhasil menambahkan guru",
                  icon: 'success'
                }).then(() => {
                  window.location = "index.php?page=guru";
                });
              </script>
              <?php
            } 
            else {                 
                $error = true;
                $errorText = "Gagal menambahkan guru : $connect->error";
                $connect->rollback();
            }
          }
        }
      } 
      catch (exception $e) {
        print_r($e);
        $connect->rollback();
        $error = true;
        $errorText = $e;
      }
    }
  }
?>
<form method="POST" enctype="multipart/form-data">
  <div class="row ">
    <div class="col-md-4 border-right">   
      <div class="d-flex flex-column align-items-center text-center p-3" >
          <img class="rounded-circle mt-5 object-cover" width="150px" height="150px" src="assets/img/default.jpg" id='previewFoto'>
          <input type="file" name="foto" id="foto" class="hidden" accept='image/png,image/jpg,image/jpeg' onchange="handlePreview(this)">
          <button class="btn btn-danger btn-sm hidden mt-2" id='btn-batal' onclick='handleCancel("assets/img/default.jpg")'>Batal</button>
          <label class="btn btn-primary btn-sm mt-2" type="button" for="foto" id='btn-ubah'> Ubah Foto</label>
        </div>
    </div>
    <div class="col-md-5 border-right">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Data Guru</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                <label class="labels">Nama Lengkap</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap'>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Username</label>
                <input type="text" class="form-control" placeholder="Username" name='username'>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Password</label>
                <input type="text" class="form-control" placeholder="Password" name='password'>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Jenis Kelamin</label>
                <select class="form-select" aria-label="Jenis Kelamin" name='jenis_kelamin'>
                    <option value='-1'>Pilih Jenis Kelamin</option>
                    <option value="0">Laki-laki</option>
                    <option value="1">Perempuan</option>
                </select>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NUPTK</label>
                  <input type="text" class="form-control" placeholder="NUPTK" name="nuptk">
                </div>
                <div class="col-md-12 mb-4">
                  <label class="labels">No Telepon</label>
                  <input type="text" class="form-control" placeholder="Nomor Telepon" name="no_telepon">
                </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-12 text-right">
                  <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
              </div>
            </div>
      </div>
    </div>
  </div>
</form>  