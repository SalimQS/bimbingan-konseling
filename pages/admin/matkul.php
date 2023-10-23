<div class="row mt-3">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">        
              <?php
              $successTambah = false;
              $errorTambah = false;
              $errorTambahText = "";
              if (isset($_POST['tambah'])) {
                $nama_mata_kuliah = $_POST['nama_mata_kuliah'];
                $sks = $_POST['sks'];
                $semester = $_POST['semester'];
                $prodi = $_POST['prodi'];

                $errorTambah = true;
                if ($nama_mata_kuliah == '') {
                  $errorTambahText = "Mata kuliah tidak boleh kosong";
                } else if ($sks == '') {
                  $errorTambahText = "SKS tidak boleh kosong";
                } else if ($semester == '') {
                  $errorTambahText = "Semester tidak boleh kosong";
                } else if ($prodi == '-1') {
                  $errorTambahText = "Program Studi tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "INSERT into mata_kuliah (nama_mata_kuliah, id_prodi, sks, semester) ";
                  $query .= "VALUES ('$nama_mata_kuliah', '$prodi', '$sks', '$semester')";
                  $result = $connect->query($query);
                  if ($result) {
                    $successTambah = true;
                    $successTambahText = 'ditambah';
                  }
                }
              }

              if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $nama_mata_kuliah = $_POST['nama_mata_kuliah'];
                $sks = $_POST['sks'];
                $semester = $_POST['semester'];
                $prodi = $_POST['prodi'];

                $errorTambah = true;
                if ($nama_mata_kuliah == '') {
                  $errorTambahText = "Mata kuliah tidak boleh kosong";
                } else if ($sks == '') {
                  $errorTambahText = "SKS tidak boleh kosong";
                } else if ($semester == '') {
                  $errorTambahText = "Semester tidak boleh kosong";
                } else if ($prodi == '-1') {
                  $errorTambahText = "Program Studi tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "UPDATE mata_kuliah SET nama_mata_kuliah = '$nama_mata_kuliah', id_prodi = '$prodi' , sks = '$sks', semester = '$semester' ";
                  $query .= "WHERE id = $id";
                  $result = $connect->query($query);
                  if ($result) {
                    $successTambah = true;
                    $successTambahText = "diubah";
                  }
                }
              }

              ?>
                <?php if ($successTambah) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Mata kuliah berhasil <?= $successTambahText ?></div>
                </div>
                <?php } ?>
                <?php if ($errorTambah) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> <?= $errorTambahText ?></div>
                </div>
                <?php } ?>
              <?php
              // EDIT MATKUL
                if (@$_GET['action'] == 'edit' && isset($_GET['id'])) {
                  $query = "SELECT mata_kuliah.*, prodi.id as prodi FROM mata_kuliah ";
                  $query .= "LEFT JOIN prodi ON mata_kuliah.id_prodi = prodi.id ";
                  $query .= "WHERE mata_kuliah.id = '". $_GET['id'] ."'";
                  $result = $connect->query($query);
                  if ($result->num_rows > 0) {
                    $matkul = $result->fetch_assoc();
                  }
              ?>
              <form class="row" method="POST" action="?page=matkul">
                  <input type="hidden" name="id" value="<?= $matkul['id'] ?>">
                  <div class="col-12 col-md-4 mb-2">
                    <label class="labels">Mata Kuliah</label>
                    <input type="text" class="form-control" placeholder="Mata Kuliah" name='nama_mata_kuliah' value="<?= @$matkul['nama_mata_kuliah'] ?>">
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">SKS</label>
                    <input type="number" min=1 class="form-control" placeholder="SKS" name='sks' value="<?= @$matkul['sks'] ?>">
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Semester</label>
                    <input type="number" min=1 class="form-control" placeholder="Semester" name='semester' value="<?= @$matkul['semester'] ?>">
                  </div>
                  <?php
                    $query = "SELECT prodi.id, CONCAT(fakultas.nama_fakultas, ' - ', prodi.nama_prodi) as nama_prodi FROM prodi ";
                    $query .= "LEFT JOIN fakultas ON fakultas.id = prodi.id_fakultas ";
                    $query .= "ORDER BY nama_fakultas";
                    $result = $connect->query($query);
                    $prodi = [];
                    $dosen = [];
                    if ($result->num_rows > 0) {
                      $prodi = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-4 mb-2">
                    <label class="labels">Program Studi</label>
                    <select class="form-select" aria-label="Program Studi" name='prodi'>
                        <option value='-1'>Pilih Program Studi</option>
                        <?php                        
                          if (count($prodi) == 0) {
                              echo "<option selected>Tidak ada program studi</option>";
                          }
                          foreach ($prodi as $item) {
                            $selected = @$matkul['prodi'] == $item['id'] ? 'selected' : '';
                              echo "<option value='".$item['id']."' " . $selected . ">" . $item['nama_prodi'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <div class="col-12 text-right">
                      <a href="?page=matkul" class="btn btn-danger" title='Batal Ubah'>Batal</a>
                      <button class="btn btn-primary" name='update' title='Tambah Siswa'>Simpan Mata Kuliah</button>
                  </div>
              </form>              
              <?php
                } else {
              // ADD MATKUL
              ?>
              <form class="row" method="POST">
                  <div class="col-12 col-md-4 mb-2">
                    <label class="labels">Mata Kuliah</label>
                    <input type="text" class="form-control" placeholder="Mata Kuliah" name='nama_mata_kuliah'>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">SKS</label>
                    <input type="number" min=1 class="form-control" placeholder="SKS" name='sks'>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Semester</label>
                    <input type="number" min=1 class="form-control" placeholder="Semester" name='semester'>
                  </div>
                  <?php
                    $query = "SELECT prodi.id, CONCAT(fakultas.nama_fakultas, ' - ', prodi.nama_prodi) as nama_prodi FROM prodi ";
                    $query .= "LEFT JOIN fakultas ON fakultas.id = prodi.id_fakultas ";
                    $query .= "ORDER BY nama_fakultas";
                    $result = $connect->query($query);
                    $prodi = [];
                    $dosen = [];
                    if ($result->num_rows > 0) {
                      $prodi = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-4 mb-2">
                    <label class="labels">Program Studi</label>
                    <select class="form-select" aria-label="Program Studi" name='prodi'>
                        <option value='-1'>Pilih Program Studi</option>
                        <?php                        
                          if (count($prodi) == 0) {
                              echo "<option selected>Tidak ada program studi</option>";
                          }
                          foreach ($prodi as $item) {
                              echo "<option value='".$item['id']."'>" . $item['nama_prodi'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <div class="col-12 text-right">
                      <button class="btn btn-primary" name='tambah' title='Tambah Siswa'>Tambah Mata Kuliah</button>
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

                if (isset($_POST['delete'])) {
                    $query = "SELECT id FROM mata_kuliah WHERE id= '". $_POST['id'] ."'";
                    $result = $connect->query($query);
                    if ($result->num_rows > 0) {
                      $query = "DELETE FROM mata_kuliah WHERE id = '". $_POST['id'] ."'";
                      $result = $connect->query($query);
                      if ($result) {
                        $successStatus = true;
                        $successText = "Berhasil dihapus";
                      } else {
                        $errorStatus = true;
                        $errorText = "gagal dihapus : mata kuliah sedang aktif";
                      }
                    } else {
                        $errorStatus = true;
                        $errorText = "tidak ditemukan";
                    }
                }

                ?>
                <?php if ($successStatus) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Mata kuliah <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if ($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Mata kuliah <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php      
                  $query = "SELECT mata_kuliah.*, prodi.nama_prodi FROM mata_kuliah ";
                  $query .= "LEFT JOIN prodi ON mata_kuliah.id_prodi = prodi.id ";
                  if (isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "WHERE mata_kuliah.nama_mata_kuliah LIKE '%$cari%' ";
                  }
                  $query .= "ORDER BY mata_kuliah.nama_mata_kuliah";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari mata kuliah" aria-label="Cari berdasarkan nama" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>   
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                    <th>Program Studi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if ($result->num_rows > 0) {
                                $mataKuliah = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($mataKuliah); $i++) {
                                    ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><?= $mataKuliah[$i]['nama_mata_kuliah'] ?></td>
                                    <td class="text-center"><?= $mataKuliah[$i]['sks'] ?></td>
                                    <td class="text-center"><?= $mataKuliah[$i]['semester'] ?></td>
                                    <td><?= $mataKuliah[$i]['nama_prodi'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-4">
                                              <a 
                                                  href="?page=matkul&action=edit&id=<?= $mataKuliah[$i]['id'] ?>"
                                                  name='delete' 
                                                  class='btn btn-sm btn-primary' 
                                                  title='Ubah Data mata kuliah'
                                              >
                                                  <i class="fa fa-pencil-alt"></i> Ubah
                                              </a>
                                            </div>
                                            <form method="post" class="col-12 col-md-4 formDelete" nama-matkul="<?= $mataKuliah[$i]['nama_mata_kuliah'] ?>">
                                                <input type="hidden" name="id" value="<?= $mataKuliah[$i]['id'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button 
                                                    class='btn btn-sm btn-danger' 
                                                    title='Hapus Data mata kuliah'
                                                >
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                ?>                                
                                <tr>
                                    <td colspan="6" class='text-center'>Tidak ada mata kuliah</td>
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