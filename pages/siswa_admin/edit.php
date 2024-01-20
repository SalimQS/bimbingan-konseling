<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=siswa"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<?php
  $success = false;
  $error = false;
  $errorText = "";
    
  if(isset($_POST['simpan'])) {
    $connect->begin_transaction();
    try {
      $query = "UPDATE siswa SET ";
      $query .= "nisn='" .$_POST['nisn'] . "', ";
      $query .= "id_kelas='" .$_POST['kelas'] . "', ";
      $query .= "nama_lengkap='". $_POST['nama_lengkap'] ."', ";
      $query .= "nama_ibu='". $_POST['nama_ibu'] ."', ";
      $query .= "jenis_kelamin='". $_POST['jenis_kelamin'] ."', ";
      $query .= "tempat_lahir='". $_POST['tempat_lahir'] ."', ";
      $query .= "tanggal_lahir='". $_POST['tanggal_lahir'] ."', ";
      $query .= "agama='". $_POST['agama'] ."', ";
      $query .= "alamat='". $_POST['alamat'] ."', ";
      $query .= "no_telepon='". $_POST['no_telepon'] ."' ";
      $query .= "WHERE id_siswa='" . $_GET['id'] . "'";
      $result = $connect->query($query);
      if($result) {
        $success = true;
        $connect->commit();
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
      $query = "UPDATE siswa SET foto_siswa='$file' WHERE id_siswa = '". $_GET['id'] ."'";
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

  $query = "SELECT kelas.nama_kelas, kelas.id_kelas as kelas_id, siswa.* FROM siswa ";
  $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
  $query .= "WHERE siswa.id_siswa='" . $_GET['id'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $foto = $user['foto_siswa'];
  } else {
    echo('<script>alert("error '.$result->num_rows.'")</script>');
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
      <img class="rounded mt-5 object-cover border border-dark" width="150px" height="150px" src="<?= $user['foto_siswa'] ?>" id='previewFoto'>
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
        <form method="POST">
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                  <label class="labels">Nama Lengkap</label>
                  <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap' value="<?= $user['nama_lengkap'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Nama Ibu Kandung</label>
                  <input type="text" class="form-control" placeholder="Nama Ibu Kandung" name='nama_ibu' value="<?= $user['nama_ibu'] ?>">
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
                <div class="col-md-12 mb-2">
                  <label class="labels">Agama</label>
                  <select class="form-select" aria-label="Agama" name='agama'>
                      <option selected>Pilih Agama</option>
                      <option value="0" <?php if($user['agama'] == 0) echo 'selected'; ?>>Islam</option>
                      <option value="1" <?php if($user['agama'] == 1) echo 'selected'; ?>>Kristen Protestan</option>
                      <option value="2" <?php if($user['agama'] == 2) echo 'selected'; ?>>Kristen Katholik</option>
                      <option value="3" <?php if($user['agama'] == 3) echo 'selected'; ?>>Hindu</option>
                      <option value="4" <?php if($user['agama'] == 4) echo 'selected'; ?>>Budha</option>
                      <option value="5" <?php if($user['agama'] == 5) echo 'selected'; ?>>Konghucu</option>
                  </select>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NISN</label>
                  <input type="text" class="form-control" placeholder="NISN" name="nisn" value="<?= $user['nisn'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Kelas</label>
                  <select class="form-select" name="kelas">
                    <?php
                    $query2 = "SELECT * FROM kelas WHERE 1";
                    $result2 = $connect->query($query2);
                    if($result2->num_rows > 0) {
                        echo('<option value="-1">Pilih Kelas</option>');
                        while($kelas = $result2->fetch_assoc()) {
                          if($user['kelas_id'] == $kelas['id_kelas']) {
                              echo('<option value="'.$kelas['id_kelas'].'" selected>'.$kelas['nama_kelas'].'</option>');
                          }
                          else echo('<option value="'.$kelas['id_kelas'].'">'.$kelas['nama_kelas'].'</option>');
                        }
                    }
                    else echo('<option value="-1">Kelas Kosong</option>');
                    ?>
                  </select>
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
              <div class="col-12 col-md-12 text-right">
                  <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
              </div>
            </div>
         </form>
      </div>
   </div>
</div>