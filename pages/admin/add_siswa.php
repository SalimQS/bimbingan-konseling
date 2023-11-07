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
            <h4 class="text-right">Data Siswa</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="text-right">Biodata Diri</h6>
        </div>
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                <label class="labels">Nickname</label>
                <input type="text" class="form-control" placeholder="Nama Unik Siswa" name='nick' value="<?= @$_POST['nick'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Nama Lengkap</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap' value="<?= @$_POST['nama_lengkap'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Jenis Kelamin</label>
                <select class="form-select" aria-label="Jenis Kelamin" name='jenis_kelamin'>
                    <option value='-1'>Pilih Jenis Kelamin</option>
                    <option value="0" <?php if(@$_POST['jenis_kelamin'] == 0) echo 'selected'; ?>>Laki-laki</option>
                    <option value="1" <?php if(@$_POST['jenis_kelamin'] == 1) echo 'selected'; ?>>Perempuan</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tempat Lahir</label>
                <input type="text" class="form-control" placeholder="Tempat lahir" name='tempat_lahir' value="<?= @$_POST['tempat_lahir'] ?>">
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tanggal Lahir</label>
                <input type="date" class="form-control" placeholder="Tanggal Lahir" name='tanggal_lahir' value="<?= @$_POST['tanggal_lahir'] ?>">
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Golongan Darah</label>
                <select class="form-select" aria-label="Golongan Darah" name='golongan_darah'>
                    <option value='-1' selected>Pilih Golongan Darah</option>
                    <option value="0" <?php if(@$_POST['golongan_darah'] == 0) echo 'selected'; ?>>A</option>
                    <option value="1" <?php if(@$_POST['golongan_darah'] == 1) echo 'selected'; ?>>B</option>
                    <option value="2" <?php if(@$_POST['golongan_darah'] == 2) echo 'selected'; ?>>AB</option>
                    <option value="3" <?php if(@$_POST['golongan_darah'] == 3) echo 'selected'; ?>>O</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Agama</label>
                <select class="form-select" aria-label="Agama" name='agama'>
                    <option value='-1' selected>Pilih Agama</option>
                    <option value="0" <?php if(@$_POST['agama'] == 0) echo 'selected'; ?>>Islam</option>
                    <option value="1" <?php if(@$_POST['agama'] == 1) echo 'selected'; ?>>Kristen</option>
                    <option value="2" <?php if(@$_POST['agama'] == 2) echo 'selected'; ?>>Katholik</option>
                    <option value="3" <?php if(@$_POST['agama'] == 3) echo 'selected'; ?>>Hindu</option>
                    <option value="4" <?php if(@$_POST['agama'] == 4) echo 'selected'; ?>>Budha</option>
                    <option value="5" <?php if(@$_POST['agama'] == 5) echo 'selected'; ?>>Konghucu</option>
                    <option value="6" <?php if(@$_POST['agama'] == 6) echo 'selected'; ?>>Protestan</option>
                </select>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Kelas</label>
                  <input type="text" list="kelas-list" class="form-control" placeholder="Kelas" name="kelas" value="<?= @$_POST['kelas'] ?>">
                  <datalist id="kelas-list"></datalist>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NISN</label>
                  <input type="text" list="kelas-list" class="form-control" placeholder="NISN" name="nisn" value="<?= @$_POST['nisn'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Alamat</label>
                  <textarea class="form-control" id="alamat" rows="3" name='alamat'><?= @$_POST['alamat'] ?></textarea>
                </div>
                <div class="col-md-12 mb-4">
                  <label class="labels">No Telepon</label>
                  <input type="text" class="form-control" placeholder="Nomor Telepon" name="no_telepon" value="<?= @$_POST['no_telepon'] ?>">
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="text-right">Informasi Akademik</h6>
            </div>
            
            <div class="row">
            <div class="col-12 col-md-6">
                <button class="btn btn-warning" type="submit" name='resetPassword' onClick="javascript: return confirm('Yakin ingin mereset password?');">Reset Password</button>
            </div>
            <div class="col-12 col-md-6 text-right">
                <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
            </div>
            </div>
      </div>
    </div>
  </div>
</form>  