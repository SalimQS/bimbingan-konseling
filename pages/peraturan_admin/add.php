<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=peraturan"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>
<?php
  $error = false;
  $errorText = "";

  if(isset($_POST['simpan'])) {
    $error = true;
    $jenis = $_POST['jenis'];
    $sanksi = $_POST['sanksi'];

    if($jenis == '') {
      $errorText = "Jenis Peraturan tidak boleh kosong";
    } else if($sanksi == '' || $sanksi > 100 || $sanksi < 1) {
      $errorText = "Sanksi harus valid (1-100)";
    } else {
      $error = false;
      $connect->begin_transaction();
      
      try {
        $query = "INSERT INTO peraturan (jenis_peraturan, poin_peraturan) VALUES ('$jenis', '$sanksi')";
        $result = $connect->query($query);
        if($result) {
          $error = false;
          $connect->commit();
          ?>
          <script src="vendors/sweetalert/sweetalert.min.js"></script>
          <script type="text/javascript">
            Swal.fire({
              title: "Sukses!",
              text: "Berhasil menambahkan Peraturan",
              icon: 'success'
            }).then(() => {    
            window.location = "index.php?page=peraturan";
            });
          </script>
          <?php
        } 
        else {
          $error = true;
          $errorText = "Gagal menambahkan Peraturan : " . $connect->error;
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
            <h4 class="text-right">Tambah List Peraturan</h4>
        </div>
        <?php if($error) {?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
            <div><strong>Gagal!</strong> <?= $errorText ?></div>
        </div>
        <?php } ?>
        <div class="row mt-2">
            <div class="col-md-12 mb-2">
                <label class="labels">Jenis Peraturan</label>
                <textarea class="form-control" id="jenis" rows="3" name='jenis' placeholder="Jenis Peraturan"><?= @$_POST['jenis'] ?></textarea>
            </div>
            <div class="col-md-12 mb-2">
                <label class="labels">Sanksi Peraturan</label>
                <input type="number" class="form-control" placeholder="Sanksi Peraturan (1-100)" name='sanksi' value="<?= @$_POST['sanksi'] ?>" min="1" max="100">
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