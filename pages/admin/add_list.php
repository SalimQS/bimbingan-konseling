<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=siswa"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $error = false;
  $errorText = "";

  if(isset($_POST['simpan'])) {
    $error = true;
    $nick = $_POST['nick'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $golongan_darah = $_POST['golongan_darah'];
    $agama = $_POST['agama'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $kelas = $_POST['kelas'];
    $nisn = $_POST['nisn'];
    $password = md5($nick);

    if($nick == '') {
      $errorText = "NIckname tidak boleh kosong";
    } else if($nama_lengkap == '') {
      $errorText = "Nama lengkap tidak boleh kosong";
    } else if($jenis_kelamin == '-1') {
      $errorText = "Jenis kelamin tidak boleh kosong";
    } else if($tempat_lahir == '') {
      $errorText = "Tempat lahir tidak boleh kosong";
    } else if($tanggal_lahir == '') {
      $errorText = "Tanggal lahir tidak boleh kosong";
    } else if($golongan_darah == '-1') {
      $errorText = "Golongan darah tidak boleh kosong";
    } else if($agama == '-1') {
      $errorText = "Agama tidak boleh kosong";
    } else if($alamat == '') {
      $errorText = "Alamat tidak boleh kosong";
    } else if($no_telepon == '') {
      $errorText = "No telepon tidak boleh kosong";
    } else if($kelas == '') {
      $errorText = "Kelas tidak boleh kosong";
    } else if($nisn == '') {
      $errorText = "NISN tidak boleh kosong";
    } else {
      $error = false;
      $foto = $_FILES['foto'];
      $hasFoto = $foto['size'] > 0;
      $file = '';
      $connect->begin_transaction();
      
      try {
        $query = "SELECT id FROM users WHERE username ='$nick'";
        $result = $connect->query($query);
        if($result->num_rows > 0) {
          $error = true;
          $errorText = "Nickname sudah terdaftar";
        } else {
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
            } else {
              $error = false;
            }
          } else {
            $file = "assets/img/default.jpg";
          }

          if(!$error) {
            $query = "INSERT INTO users (admin, username, password) ";
            $query .= "VALUES('user', '$nick', '$password')";
            $result = $connect->query($query);
            if($result === TRUE) {
              $id_user = $connect->insert_id;

              $query = "INSERT INTO siswa (id_user, kelas, nisn) ";
              $query .= "VALUES('$id_user', '$kelas', '$nisn')";
              $result = $connect->query($query);
              if($result) {
                if($hasFoto) {
                  $query = "INSERT INTO data (id_user, nama_lengkap, foto, jenis_kelamin, tempat_lahir, tanggal_lahir, golongan_darah, agama, alamat, no_telepon, status) ";
                  $query .= "VALUES('$id_user', '$nama_lengkap', '$file', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$golongan_darah', '$agama', '$alamat', '$no_telepon', '1')";
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
                          text: "Berhasil menambahkan siswa",
                          icon: 'success'
                        }).then(() => {    
                        window.location = "index.php?page=siswa";
                        });
                      </script>
                      <?php
                    } else {
                      $error = true;
                      $errorText = "Foto gagal diupload";
                      $connect->rollback();
                    }
                  } else {
                      $error = true;
                      $errorText = "Gagal menambah siswa";
                      $connect->rollback();
                  }
                } else {
                  $query = "INSERT INTO data (id_user, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, golongan_darah, agama, alamat, no_telepon, status) ";
                  $query .= "VALUES('$id_user', '$nama_lengkap', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$golongan_darah', '$agama', '$alamat', '$no_telepon', '1')";
                  $result = $connect->query($query);
                  if($result) {
                    $error = false;
                    $connect->commit();
                    ?>
                    <script src="vendors/sweetalert/sweetalert.min.js"></script>
                    <script type="text/javascript">
                      Swal.fire({
                        title: "Sukses!",
                        text: "Berhasil menambahkan siswa",
                        icon: 'success'
                      }).then(() => {
                        window.location = "index.php?page=siswa";
                      });
                    </script>
                    <?php
                  } else {                 
                      $error = true;
                      $errorText = "Gagal menambahkan siswa : $connect->error";
                      $connect->rollback();
                  }
                }
              } else {
                $error = true;
                $errorText = "Gagal menambahkan siswa : " . $connect->error;
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
?>
<form method="POST" enctype="multipart/form-data">
  <div class="row justify-content-md-center">
    <div class="col-md-5 border-right">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Penambahan List Pelanggaran</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="row mt-2">
            <div class="col-md-12 mb-2">
                <label class="labels">Jenis Pelanggaran</label>
                <input type="text" class="form-control" placeholder="Jenis Pelanggaran" name='jenis' value="<?= @$_POST['jenis'] ?>">
                
                <textarea class="form-control" id="jenis" rows="3" name='jenis'><?= @$_POST['jenis'] ?></textarea>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Sanksi Pelanggaran</label>
                <input type="number" class="form-control" placeholder="Sanksi Pelanggaran (1-100)" name='sanksi' value="<?= @$_POST['sanksi'] ?>" min="1" max="100">
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