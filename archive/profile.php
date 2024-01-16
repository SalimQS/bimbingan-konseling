<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="index.php"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $success = false;
  $error = false;
  $successFoto = false;
  $errorFoto = false;
  $errorFotoText = "";

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
      $query = "UPDATE data SET foto='$file' WHERE id_user = '". $_SESSION['id_user'] ."'";
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
  if(isset($_POST['simpan'])) {
    $query = "UPDATE data SET ";
    /*$query .= "nama_lengkap='". $_POST['nama_lengkap'] ."', ";
    $query .= "jenis_kelamin='". $_POST['jenis_kelamin'] ."', ";
    $query .= "tempat_lahir='". $_POST['tempat_lahir'] ."', ";
    $query .= "tanggal_lahir='". $_POST['tanggal_lahir'] ."', ";
    $query .= "golongan_darah='". $_POST['golongan_darah'] ."', ";
    $query .= "agama='". $_POST['agama'] ."', ";
    $query .= "alamat='". $_POST['alamat'] ."', ";*/
    $query .= "no_telepon='". $_POST['no_telepon'] ."'";
    $query .= "WHERE id_user='" . $_SESSION['id_user'] . "'";
    $result = $connect->query($query);
    if($result) {
        $success = true;
        $_SESSION['nama_lengkap'] = $_POST['nama_lengkap'];
    } else {
        $error = true;
    }
  }

  $query = "SELECT data.*, users.username ";

  if($_SESSION['posisi'] == 'siswa') {
    $query .= ", siswa.nisn, siswa.kelas ";
  }
  else if($_SESSION['posisi'] == 'guru') {
    $query .= ", guru.nip, guru.nuptk ";
  }
  $query .= "FROM users ";
  $query .= "LEFT JOIN data ON data.id_user = users.id ";
  if($_SESSION['posisi'] == 'siswa') {
    $query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
  }
  else if($_SESSION['posisi'] == 'guru') {
    $query .= "LEFT JOIN guru ON guru.id_user = users.id ";
  }
  $query .= "WHERE users.id='" . $_SESSION['id_user'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
      $user = $result->fetch_assoc();
  } else {
      header("location: login.php");
  }
?>
<div class="row">
   <div class="col-md-3 border-right">
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
            <div><strong>Gagal!</strong> Data gagal diperbarui</div>
        </div>
        <?php } ?>
         <form method="POST">
            <div class="row mt-2">
                <table class="table profileTable">
                  <tr>
                    <td>Username</td>
                    <td><?= $user['username'] ?></td>
                  </tr>
                  <tr>
                    <td>Nama Lengkap</td>
                    <td><?= $user['nama_lengkap'] ?></td>
                  </tr>
                  <?php
                      if($_SESSION['posisi'] == 'siswa') {
                  ?>
                    <tr>
                      <td>NISN</td>
                      <td><?= $user['nisn'] ?></td>
                    </tr>
                    <tr>
                      <td>Kelas</td>
                      <td><?= $user['kelas'] ?></td>
                    </tr>
                  <?php
                      }
                      else if($_SESSION['posisi'] == 'guru') {
                  ?>
                  <tr>
                    <td>NIP</td>
                      <td><?= $user['nip'] ?></td>
                    </tr>
                    <tr>
                      <td>NUPTK</td>
                      <td><?= $user['nuptk'] ?></td>
                    </tr>
                  <?php
                      }
                  ?>
                  <tr>
                    <td>Jenis Kelamin</td>
                    <td><?php if($user['jenis_kelamin'] == 0) { echo 'Laki-Laki'; } else { echo "Perempuan"; } ?></td>
                  </tr>
                  <tr>
                    <td>Golongan Darah</td>
                    <td>
                      <?php 
                        switch($user['golongan_darah']) {
                          case 0:
                            echo 'A'; 
                            break;
                          case 1:
                            echo 'B'; 
                            break;
                          case 2:
                            echo 'AB'; 
                            break;
                          default:
                            echo 'O';
                            break;
                        }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Agama</td>
                    <td>
                      <?php 
                        switch($user['agama']) {
                          case 0:
                            echo('Islam'); 
                            break;
                          case 1:
                            echo('Protestan'); 
                            break;
                          case 2:
                            echo('Katolik'); 
                            break;
                          case 3:
                            echo('Hindu'); 
                            break;
                          case 4:
                            echo('Buddha'); 
                            break;
                          default:
                            echo('Konghucu');
                            break;
                        }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Alamat</td>
                    <td><?= $user['alamat'] ?></td>
                  </tr>
                </table>
                <!--<div class="col-md-12 mb-2">
                <label class="labels">Username</label>
                <input type="text" class="form-control" placeholder="Nomor Induk Siswa" value="<?= $user['username'] ?>" disabled>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Nama Lengkap</label>
                <input type="text" class="form-control" placeholder="Nama Lengkap" value="<?= $user['nama_lengkap'] ?>" disabled>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Jenis Kelamin</label>
                <select class="form-select" aria-label="Jenis Kelamin" disabled>
                    <option>Pilih Jenis Kelamin</option>
                    <option value="0" <?php if($user['jenis_kelamin'] == 0) echo 'selected'; ?>>Laki-laki</option>
                    <option value="1" <?php if($user['jenis_kelamin'] == 1) echo 'selected'; ?>>Perempuan</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tempat Lahir</label>
                <input type="text" class="form-control" placeholder="Tempat lahir" value="<?= $user['tempat_lahir'] ?>" disabled>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Tanggal Lahir</label>
                <input type="date" class="form-control" placeholder="Tanggal Lahir" value="<?= $user['tanggal_lahir'] ?>" disabled>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Golongan Darah</label>
                <select class="form-select" aria-label="Golongan Darah" disabled>
                    <option selected>Pilih Golongan Darah</option>
                    <option value="0" <?php if($user['golongan_darah'] == 0) echo 'selected'; ?>>A</option>
                    <option value="1" <?php if($user['golongan_darah'] == 1) echo 'selected'; ?>>B</option>
                    <option value="2" <?php if($user['golongan_darah'] == 2) echo 'selected'; ?>>AB</option>
                    <option value="3" <?php if($user['golongan_darah'] == 3) echo 'selected'; ?>>O</option>
                </select>
                </div>
                <div class="col-md-6 mb-2">
                <label class="labels">Agama</label>
                <select class="form-select" aria-label="Agama" disabled>
                    <option selected>Pilih Agama</option>
                    <option value="0" <?php if($user['agama'] == 0) echo 'selected'; ?>>Islam</option>
                    <option value="1" <?php if($user['agama'] == 1) echo 'selected'; ?>>Protestan</option>
                    <option value="2" <?php if($user['agama'] == 2) echo 'selected'; ?>>Katholik</option>
                    <option value="3" <?php if($user['agama'] == 3) echo 'selected'; ?>>Hindu</option>
                    <option value="4" <?php if($user['agama'] == 4) echo 'selected'; ?>>Budha</option>
                    <option value="5" <?php if($user['agama'] == 5) echo 'selected'; ?>>Konghucu</option>
                </select>
                </div>
                <div class="col-md-12 mb-2">
                <label class="labels">Alamat</label>
                <textarea class="form-control" id="alamat" rows="3" disabled><?= $user['alamat'] ?></textarea>
                </div>-->
            </div>
         </form>
      </div>
   </div>
   <?php
      $errorPass = false;    
      $errorText = "";
      $successPass = false;
      if(isset($_POST['updatePassword'])) {
          $passwordLama = md5($_POST['password_lama']);
          $passwordBaru = md5($_POST['password_baru']);
          $konfirmasiPasswordBaru = md5($_POST['konfirmasi_password_baru']);
          if($passwordLama == '') {
            $errorPass = true;
            $errorText = 'Password lama tidak boleh kosong';
          } else if($passwordBaru == '') { 
            $errorPass = true;
            $errorText = 'Password baru tidak boleh kosong';
          } else if($passwordBaru != $konfirmasiPasswordBaru) {
            $errorPass = true;
            $errorText = 'Konfirmasi password tidak sama';
          } else {
              $errorPass = false;
              $query = "SELECT id FROM users WHERE id='" . $_SESSION['id_user'] . "' AND password='". $passwordLama ."'";
              $result = $connect->query($query);
              if($result->num_rows > 0) {
                  $query = "UPDATE users SET password='". $passwordBaru ."' WHERE id='". $_SESSION['id_user'] ."'";
                  $result = $connect->query($query);
                  if($result) {
                    $successPass = true;
                  } else {
                    $errorPass = true;
                    $errorText = "Gagal memperbarui password";
                  }
              } else {
                  $errorPass = true;
                  $errorText = "Password lama salah";
              }
          }
      }
   ?>
   <div class="col-md-4 mt-5">
      <div class="p-3">
         <?php if($successPass) { ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Berhasil!</strong> Password berhasil diperbarui</div>
        </div>
        <?php } ?>
         <?php if($errorPass) { ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
         <div class="d-flex justify-content-between align-items-center fw-bold mb-3">
           <span>Ubah Password Login</span>
         </div>
         <form method='POST'>
            <div class="col-md-12 mb-2">
                <label class="labels">Password Lama</label>
                <input type="password" class="form-control" placeholder="Password lama" name='password_lama' value="">
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Password Baru</label>
                <input type="password" class="form-control" placeholder="Password lama" name='password_baru' value="">
            </div>
            <div class="col-md-12 mb-3">
                <label class="labels">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" placeholder="Password lama" name='konfirmasi_password_baru' value=''>
            </div>
            <div class="text-right">
                <button class="btn btn-primary profile-button" name='updatePassword' type="submit">Update Password</button>
            </div>
        </form>
      </div>
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center fw-bold mb-3">
           <span>Ubah No Telepon</span>
         </div>
        <div class="col-md-12 mb-4">
          <label class="labels">No Telepon</label>
          <input type="text" class="form-control" placeholder="Nomor Telepon" name="no_telepon" value="<?= $user['no_telepon'] ?>">
        </div>
        <div class="text-right">
                <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan Perubahan</button>
        </div>
      </div>
   </div>
</div>