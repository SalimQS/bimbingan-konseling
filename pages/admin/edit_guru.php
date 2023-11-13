<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=guru"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<?php
  $success = false;
  $error = false;
  $errorText = "";
    
  if(isset($_POST['simpan'])) {
    $connect->begin_transaction();
    try {
      $query = "UPDATE data SET ";
      $query .= "nama_lengkap='". $_POST['nama_lengkap'] ."', ";
      $query .= "jenis_kelamin='". $_POST['jenis_kelamin'] ."', ";
      $query .= "tempat_lahir='". $_POST['tempat_lahir'] ."', ";
      $query .= "tanggal_lahir='". $_POST['tanggal_lahir'] ."', ";
      $query .= "golongan_darah='". $_POST['golongan_darah'] ."', ";
      $query .= "agama='". $_POST['agama'] ."', ";
      $query .= "alamat='". $_POST['alamat'] ."', ";
      $query .= "no_telepon='". $_POST['no_telepon'] ."' ";
      $query .= "WHERE id_user='" . $_GET['id'] . "'";
      $result = $connect->query($query);
      if($result) {
          $query = "UPDATE guru SET ";
          $query .= "nip='" .$_POST['nip'] . "', ";
          $query .= "nuptk='" .$_POST['nuptk'] . "' ";
          $query .= "WHERE id_user='" . $_GET['id'] . "'";
          $result = $connect->query($query);
          if($result) {
            $success = true;
            $connect->commit();
          } else {
            $error = true;
            $errorText = "Data gagal diperbarui";
            $connect->rollback();
          }
      } else {
        $error = true;
        $errorText = "Data gagal diperbarui";
        $connect->rollback();
      }
    } catch (mysqli_sql_exception $e) {
      $connect->rollback();
      $error = true;
      $errorText = $e;
    }
  }

  $errorPass = false;    
  $errorText = "";
  $successPass = false;
  $successText = "";
  $successFoto = false;
  $errorFoto = false;
  $errorFotoText = "";
  if(isset($_POST['resetPassword'])) {
      $query = "SELECT username FROM users WHERE id='" . $_GET['id'] . "'";
      $result = $connect->query($query);
      if($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $password = $data['username'];
        $hashedPassword = md5($password);
        $query = "UPDATE users SET password='". $hashedPassword ."' WHERE id='". $_GET['id'] ."'";
        $result = $connect->query($query);
        if($result) {
          $successPass = true;
          $successText = "Password berhasil direset ke <b>$password</b>";
        } else {
          $errorPass = true;
          $errorText = "Gagal mereset password";
        }
      }
  }

  if(isset($_POST['changeFoto'])) {
    $fileName = "uploads/" . basename($_FILES['foto']['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $file = "uploads/" . time() . "." . $ext;
    $maxSize = 2097152;
    $imgSize = getimagesize($_FILES['foto']['tmp_name']);
    if(($_FILES['foto']['size'] >= $maxSize)) {
      $errorFoto = true;
      $errorFotoText = "Ukuran foto maksimal 2MB";
    } else {
      $connect->begin_transaction();
      $query = "UPDATE data SET foto='$file' WHERE id_user = '". $_GET['id'] ."'";
      $result = $connect->query($query);
      if($result) {
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $file)) {
          $successFoto = true;
          $connect->commit();
        } else {
          $errorFoto = true;
          $errorFotoText = "Foto gagal diperbarui";
          $connect->rollback();
        }
      } else {
        $errorFoto = true;
        $errorFotoText = "Foto gagal diperbarui";
        $connect->rollback();
      }
    }
  }

  $query = "SELECT data.*, users.username, guru.nip, guru.nuptk ";
  $query .= "FROM users ";
  $query .= "LEFT JOIN data ON data.id_user = users.id ";
  $query .= "LEFT JOIN guru ON guru.id_user = users.id ";
  $query .= "WHERE users.id='" . $_GET['id'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $foto = $user['foto'];
  } else {
    header("location: login.php");
  }
?>
<div class="row ">
  <div class="col-md-4 border-right">   
    <?php if($successFoto) {?>
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
        <div><strong>Berhasil!</strong> Foto berhasil diperbarui</div>
    </div>
    <?php } ?>
    <?php if($errorFoto) {?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
        <div><strong>Gagal!</strong> <?= $errorFotoText ?></div>
    </div>
    <?php } ?>
    <form method="post" class="d-flex flex-column align-items-center text-center p-3" enctype="multipart/form-data">
      <img class="rounded-circle mt-5 object-cover" width="150px" height="150px" src="<?= $user['foto'] ?>" id='previewFoto'>
      <input type="file" name="foto" id="foto" class="hidden" accept='image/png,image/jpg,image/jpeg' onchange="handlePreview(this)">
      <button class="btn btn-primary btn-sm hidden mt-2" id='btn-simpan' name='changeFoto'>Simpan</button>
      <button class="btn btn-danger btn-sm hidden mt-2" id='btn-batal' onclick='handleCancel("<?= $foto ?>")'>Batal</button>
      <label class="btn btn-primary btn-sm mt-2" type="button" for="foto" id='btn-ubah'> Ubah Foto</label>
    </form>
  </div>
  <div class="col-md-5 border-right">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Data Siswa</h4>
        </div>
        <?php if($success) {?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Berhasil!</strong> Data berhasil diperbarui</div>
        </div>
        <?php } ?>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <?php if($successPass) {?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Berhasil!</strong> <?= $successText ?></div>
        </div>
        <?php } ?>
        <?php if($errorPass) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="text-right">Data Diri</h6>
        </div>
        <form method="POST">
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                <label class="labels">Username</label>
                <input type="text" class="form-control" placeholder="Username" value="<?= $user['username'] ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Nama Lengkap</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap' value="<?= $user['nama_lengkap'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Jenis Kelamin</label>
                <select class="form-select" aria-label="Jenis Kelamin" name='jenis_kelamin'>
                    <option>Pilih Jenis Kelamin</option>
                    <option value="0" <?php if($user['jenis_kelamin'] == 0) echo 'selected'; ?>>Laki-laki</option>
                    <option value="1" <?php if($user['jenis_kelamin'] == 1) echo 'selected'; ?>>Perempuan</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tempat Lahir</label>
                <input type="text" class="form-control" placeholder="Tempat lahir" name='tempat_lahir' value="<?= $user['tempat_lahir'] ?>">
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tanggal Lahir</label>
                <input type="date" class="form-control" placeholder="Tanggal Lahir" name='tanggal_lahir' value="<?= $user['tanggal_lahir'] ?>">
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Golongan Darah</label>
                <select class="form-select" aria-label="Golongan Darah" name='golongan_darah'>
                    <option selected>Pilih Golongan Darah</option>
                    <option value="0" <?php if($user['golongan_darah'] == 0) echo 'selected'; ?>>A</option>
                    <option value="1" <?php if($user['golongan_darah'] == 1) echo 'selected'; ?>>B</option>
                    <option value="2" <?php if($user['golongan_darah'] == 2) echo 'selected'; ?>>AB</option>
                    <option value="3" <?php if($user['golongan_darah'] == 3) echo 'selected'; ?>>O</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Agama</label>
                <select class="form-select" aria-label="Agama" name='agama'>
                    <option selected>Pilih Agama</option>
                    <option value="0" <?php if($user['agama'] == 0) echo 'selected'; ?>>Islam</option>
                    <option value="1" <?php if($user['agama'] == 1) echo 'selected'; ?>>Kristen</option>
                    <option value="2" <?php if($user['agama'] == 2) echo 'selected'; ?>>Katholik</option>
                    <option value="3" <?php if($user['agama'] == 3) echo 'selected'; ?>>Hindu</option>
                    <option value="4" <?php if($user['agama'] == 4) echo 'selected'; ?>>Budha</option>
                    <option value="5" <?php if($user['agama'] == 5) echo 'selected'; ?>>Konghucu</option>
                </select>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NIP</label>
                  <input type="text" class="form-control" placeholder="NIP" name="nip" value="<?= $user['nip'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NUPTK</label>
                  <input type="text" class="form-control" placeholder="NUPTK" name="nuptk" value="<?= $user['nuptk'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Alamat</label>
                  <textarea class="form-control" id="alamat" rows="3" name='alamat'><?= $user['alamat'] ?></textarea>
                </div>
                <div class="col-md-12 mb-4">
                  <label class="labels">No Telepon</label>
                  <input type="text" class="form-control" placeholder="Nomor Telepon" name="no_telepon" value="<?= $user['no_telepon'] ?>">
                </div>
            </div>
            <div class="row">
            <div class="col-12 col-md-6">
                <button class="btn btn-warning" type="submit" name='resetPassword' onClick="javascript: return confirm('Yakin ingin mereset password?');">Reset Password</button>
            </div>
            <div class="col-12 col-md-6 text-right">
                <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
            </div>
            </div>
         </form>
      </div>
   </div>
</div>