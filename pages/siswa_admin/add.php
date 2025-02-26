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
    $nama_lengkap = $_POST['nama_lengkap'];
    $nama_ibu = $_POST['nama_ibu'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $agama = $_POST['agama'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $kelas = $_POST['kelas'];
    $nisn = $_POST['nisn'];

    if($nama_lengkap == '') {
      $errorText = "Nama lengkap tidak boleh kosong";
    } else if($nama_ibu == '') {
      $errorText = "Jenis kelamin tidak boleh kosong";
    } else if($jenis_kelamin == '-1') {
      $errorText = "Jenis kelamin tidak boleh kosong";
    } else if($tempat_lahir == '') {
      $errorText = "Tempat lahir tidak boleh kosong";
    } else if($tanggal_lahir == '') {
      $errorText = "Tanggal lahir tidak boleh kosong";
    } else if($agama == '-1') {
      $errorText = "Agama tidak boleh kosong";
    } else if($alamat == '') {
      $errorText = "Alamat tidak boleh kosong";
    } else if($no_telepon == '') {
      $errorText = "No telepon tidak boleh kosong";
    } else if($kelas == '-1') {
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
          if($hasFoto) {
            $query = "INSERT INTO siswa (id_kelas, nisn, nama_lengkap, nama_ibu, foto_siswa, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, no_telepon) ";
            $query .= "VALUES('$kelas', '$nisn', '$nama_lengkap', '$nama_ibu', '$file', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$agama', '$alamat', '$no_telepon')";
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
              $errorText = "Gagal menambahkan siswa : " . $connect->error;
            }
          }
          else {
            $query = "INSERT INTO siswa (id_kelas, nisn, nama_lengkap, nama_ibu, foto_siswa, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, no_telepon) ";
            $query .= "VALUES('$kelas', '$nisn', '$nama_lengkap', '$nama_ibu', '$file', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$agama', '$alamat', '$no_telepon')";
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
            <div class="row mt-2">
                <div class="col-md-12 mb-2">
                  <label class="labels">Nama Lengkap</label>
                  <input type="text" class="form-control" placeholder="Nama Lengkap" name='nama_lengkap' value="<?= @$_POST['nama_lengkap'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Nama Ibu Kandung</label>
                  <input type="text" class="form-control" placeholder="Nama Ibu Kandung" name='nama_ibu' value="<?= @$_POST['nama_ibu'] ?>">
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">Jenis Kelamin</label>
                  <select class="form-select" aria-label="Jenis Kelamin" name='jenis_kelamin'>
                      <option value='-1'>Pilih Jenis Kelamin</option>
                      <option value="0">Laki-laki</option>
                      <option value="1">Perempuan</option>
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
                <div class="col-md-12 mb-2">
                <label class="labels">Agama</label>
                <select class="form-select" aria-label="Agama" name='agama'>
                    <option value='-1' selected>Pilih Agama</option>
                    <option value="0">Islam</option>
                    <option value="1">Kristen Protestan</option>
                    <option value="2">Kristen Katholik</option>
                    <option value="3">Hindu</option>
                    <option value="4">Budha</option>
                    <option value="5">Konghucu</option>
                </select>
                </div>
                <div class="col-md-12 mb-2">
                  <label class="labels">NISN</label>
                  <input type="text" class="form-control" placeholder="NISN" name="nisn" value="<?= @$_POST['nisn'] ?>">
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
                          echo('<option value="'.$kelas['id_kelas'].'">'.$kelas['nama_kelas'].'</option>');
                        }
                    }
                    else echo('<option value="-1">Kelas Kosong</option>');
                    ?>
                  </select>
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
            <div class="row">
              <div class="col-12 col-md-12 text-right">
                  <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
              </div>
            </div>
      </div>
    </div>
  </div>
</form>  