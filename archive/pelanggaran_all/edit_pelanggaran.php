<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=pelanggar"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $error = false;
  $errorText = "";

  if(isset($_POST['simpan'])) {
    $error = true;
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];

    if($nama == '0') {
      $errorText = "Nama pelanggar tidak boleh kosong";
    } else if($jenis == '0') {
      $errorText = "Jenis pelanggaran tidak boleh kosong";
    } else {
      $error = false;
      $connect->begin_transaction();
      
      try {
        $query = "UPDATE `pelanggaran` SET `id_user`='$nama',`id_pelanggaran`='$jenis' WHERE `id`='" . $_GET['id'] . "'";
        $result = $connect->query($query);
        if($result) {
          $error = false;
          $connect->commit();
          ?>
          <script src="vendors/sweetalert/sweetalert.min.js"></script>
          <script type="text/javascript">
            Swal.fire({
              title: "Sukses!",
              text: "Berhasil mengubah data pelanggar",
              icon: 'success'
            }).then(() => {    
            window.location = "index.php?page=pelanggar";
            });
          </script>
          <?php
        } 
        else {
          $error = true;
          $errorText = "Gagal mengubah data pelanggar : " . $connect->error;
        }
      } catch (exception $e) {
        print_r($e);
        $connect->rollback();
        $error = true;
        $errorText = $e;
      }
    }
  }
  //---
  $query = "SELECT `id_user`, `id_pelanggaran` FROM `pelanggaran` WHERE `id`='" . $_GET['id'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
  } else {
    header("location: login.php");
  }
?>
<form method="POST" enctype="multipart/form-data">
  <div class="row justify-content-md-center">
    <div class="col-md-5 border-right">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Ubah List Pelanggar</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="row mt-2">
            <div class="col-md-12 mb-2">
                <label class="labels">Nama Pelanggar</label>
                <select class="form-control" name="nama">
                    <option value="0">Pilih Nama Pelanggar</option>
                    <?php
                        $query = "SELECT siswa.kelas, data.nama_lengkap, users.id FROM users ";
                        $query .= "LEFT JOIN data ON data.id_user = users.id ";
                        $query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
                        $query .= "WHERE siswa.id IS NOT NULL";
                        //---
                        $result = $connect->query($query);
                        if($result->num_rows > 0) {
                            $row = $result->fetch_all(MYSQLI_ASSOC);
                            for($i = 0; $i < count($row); $i++) {
                                if($user['id_user'] == $row[$i]['id']) {
                                    echo("<option value='" . $row[$i]['id'] . "' selected>" . $row[$i]['kelas'] . " - " . $row[$i]['nama_lengkap'] . "</option>");
                                }
                                else echo("<option value='" . $row[$i]['id'] . "'>" . $row[$i]['kelas'] . " - " . $row[$i]['nama_lengkap'] . "</option>");                
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Jenis Pelanggaran</label>
                <select class="form-control" name="jenis">
                    <option value="0" selected>Pilih Jenis Pelanggaran</option>
                    <?php
                        $query = "SELECT * FROM aturan WHERE 1";
                        //---
                        $result = $connect->query($query);
                        if($result->num_rows > 0) {
                            $row = $result->fetch_all(MYSQLI_ASSOC);
                            for($i = 0; $i < count($row); $i++) {
                                if($user['id_pelanggaran'] == $row[$i]['id']) {
                                    echo("<option value='" . $row[$i]['id'] . "' selected>" . $row[$i]['jenis'] . " - " . $row[$i]['poin'] . " Poin</option>");
                                }
                                else echo("<option value='" . $row[$i]['id'] . "'>" . $row[$i]['jenis'] . " - " . $row[$i]['poin'] . " Poin</option>");
                            }
                        }
                    ?>
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