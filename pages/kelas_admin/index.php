<div class="row mt-3">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">        
              <?php
              $successTambah = false;
              $errorTambah = false;
              $errorTambahText = "";
              //---
              if(isset($_POST['tambah'])) {
                $kelas = $_POST['kelas'];
                $walikelas = $_POST['walikelas'];

                $errorTambah = true;
                if($kelas == '') {
                  $errorTambahText = "Kelas tidak boleh kosong";
                } else if($walikelas == -1) {
                  $errorTambahText = "Wali Kelas tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "INSERT into kelas (nama_kelas, id_wali_kelas) ";
                  $query .= "VALUES ('$kelas', '$walikelas')";
                  $result = $connect->query($query);
                  if($result) {
                    $successTambah = true;
                    $successTambahText = 'ditambah';
                  } else {
                    $errorTambah = true;
                    $errorTambahText = "Kelas gagal ditambah";
                  }
                }
              }

              if(isset($_POST['update'])) {
                $kelas = $_POST['kelas'];
                $walikelas = $_POST['walikelas'];

                $errorTambah = true;
                if($kelas == '') {
                  $errorTambahText = "Kelas tidak boleh kosong";
                } else if($walikelas == -1) {
                  $errorTambahText = "Wali Kelas tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "UPDATE `kelas` SET `nama_kelas`='$kelas',`id_wali_kelas`='$walikelas' WHERE `id_kelas`='". $_GET['id'] ."'";
                  $result = $connect->query($query);
                  if($result) {
                    $successTambah = true;
                    $successTambahText = 'diedit';
                  } else {
                    $errorTambah = true;
                    $errorTambahText = "Kelas gagal diedit";
                  }
                }
              }

              ?>
                <?php if($successTambah) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Kelas berhasil <?= $successTambahText ?></div>
                </div>
                <?php } ?>
                <?php if($errorTambah) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> <?= $errorTambahText ?></div>
                </div>
                <?php } ?>
              <?php
              // EDIT KELAS
                if(@$_GET['action'] == 'edit' && isset($_GET['id'])) {
                  $query = "SELECT * FROM kelas ";
                  $query .= "WHERE id_kelas = '". $_GET['id'] ."' LIMIT 1";
                  $result = $connect->query($query);
                  if($result->num_rows > 0) {
                    $kelas = $result->fetch_assoc();
                  }
              ?>
              <form class="row" method="POST">
                  <div class="col-12 mb-2">
                    <label class="labels">Nama Kelas</label>
                    <input type="text" class="form-control" placeholder="Nama Kelas" name='kelas' value="<?= $kelas['nama_kelas'] ?>">
                  </div>
                  <div class="col-12 mb-2">
                    <label class="labels">Wali Kelas</label>
                    <select name="walikelas" class="form-control">
                        <option value="-1">Pilih Wali Kelas</option>
                        <?php
                            $query = "SELECT id_guru, nama_guru as nama FROM guru WHERE 1";
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                while($guru = $result->fetch_assoc()) {
                                    if($guru['id_guru'] == $kelas['id_wali_kelas'])
                                        echo('<option value="'.$guru['id_guru'].'" selected>'.$guru['nama'].'</option>');
                                    else
                                        echo('<option value="'.$guru['id_guru'].'">'.$guru['nama'].'</option>');
                                }
                            }
                            else {
                                echo('<option value="-1">Tidak Ada Guru</option>');
                            }
                        ?>
                    </select>
                  </div>
                  <div class="col-12 text-right">
                      <a href="?page=kelas" class="btn btn-danger" title='Batal Ubah'>Batal</a>
                      <button class="btn btn-primary" name='update' title='Edit Kelas'>Edit</button>
                  </div>
              </form>           
              <?php
                } else {
              // ADD KELAS
              ?>
              <form class="row" method="POST">
                  <div class="col-12 mb-2">
                    <label class="labels">Nama Kelas</label>
                    <input type="text" class="form-control" placeholder="Nama Kelas" name='kelas'>
                  </div>
                  <div class="col-12 mb-2">
                    <label class="labels">Wali Kelas</label>
                    <select name="walikelas" class="form-control">
                        <?php
                            $query = "SELECT id_guru, nama_guru as nama FROM guru WHERE 1";
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                echo('<option value="-1">Pilih Wali Kelas</option>');
                                while($guru = $result->fetch_assoc()) {
                                    echo('<option value="'.$guru['id_guru'].'">'.$guru['nama'].'</option>');
                                }
                            }
                            else echo('<option value="-1">Tidak Ada Guru</option>');
                        ?>
                    </select>
                  </div>
                  <div class="col-12 text-right">
                      <button class="btn btn-primary" name='tambah' title='Tambah Kelas'>Buat</button>
                  </div>
              </form>
              <?php
                }
              ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php
                $successStatus = false;
                $successText = "";
                $errorStatus = false;
                $errorText = "";

                if(isset($_POST['delete'])) {
                    $query = "SELECT id_kelas FROM kelas WHERE id_kelas = '". $_POST['id'] ."'";
                    $result = $connect->query($query);
                    if($result->num_rows > 0) {
                      $query = "DELETE FROM kelas WHERE id_kelas = '". $_POST['id'] ."'";
                      $result = $connect->query($query);
                      if($result) {
                        $successStatus = true;
                        $successText = "Berhasil dihapus";
                      } else {
                        $errorStatus = true;
                        $errorText = "gagal dihapus";
                      }
                    } else {
                        $errorStatus = true;
                        $errorText = "tidak ditemukan";
                    }
                }

                ?>
                <?php if($successStatus) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Kelas <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Kelas <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php      
                  $query = "SELECT kelas.*, guru.nama_guru FROM kelas ";
                  $query .= "LEFT JOIN guru ON guru.id_guru = kelas.id_wali_kelas ";
                  $query .= "WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND kelas.nama_kelas LIKE '%$cari%' OR guru.nama_guru LIKE '%$cari%' ";
                  }
                  $query .= "ORDER BY kelas.nama_kelas DESC";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Kelas (Kelas atau Wali Kelas)" aria-label="Cari" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>   
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Nama Wali Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $kelas = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($kelas); $i++) {
                                    ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><b><?= $kelas[$i]['nama_kelas'] ?></b></td>
                                    <td class="text-center"><?= $kelas[$i]['nama_guru'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div style="display: inline-block;">
                                            <a href="?page=kelas&action=edit&id=<?= $kelas[$i]['id_kelas'] ?>" class='btn btn-sm btn-primary btn-data-form' title='Ubah Kelas'>Ubah</a>
                                        </div>
                                        <form style="display: inline-block;" method="post" class="formDelete" nama-kelas="<?= $kelas[$i]['nama_kelas'] ?>">
                                            <input type="hidden" name="id" value="<?= $kelas[$i]['id_kelas'] ?>"/>
                                            <input type="hidden" name='delete'/>
                                            <button class='btn btn-sm btn-danger btn-data-form' title='Hapus Kelas'>Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                ?>                                
                                <tr>
                                    <td colspan="6" class='text-center'>Tidak ada Kelas</td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>