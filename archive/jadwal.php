<div class="row mt-3">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">        
              <?php
              $successTambah = false;
              $errorTambah = false;
              $errorTambahText = "";
              if (isset($_POST['tambah'])) {
                $matkul = $_POST['matkul'];
                $dosen = $_POST['dosen'];
                $jam_kuliah = $_POST['jam_kuliah'];
                $hari_kuliah = $_POST['hari_kuliah'];
                $ruang = $_POST['ruang'];

                $errorTambah = true;
                if ($matkul == '-1') {
                  $errorTambahText = "Mata kuliah tidak boleh kosong";
                } else if ($dosen == '-1') {
                  $errorTambahText = "Dosen tidak boleh kosong";
                } else if ($jam_kuliah == '') {
                  $errorTambahText = "Jam kuliah tidak boleh kosong";
                } else if ($hari_kuliah == '-1') {
                  $errorTambahText = "Hari kuliah tidak boleh kosong";
                } else if ($ruang == '') {
                  $errorTambahText = "Ruang tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "INSERT into jadwal_kuliah (id_mata_kuliah, id_dosen, jam_kuliah, hari_kuliah, ruang) ";
                  $query .= "VALUES ('$matkul', '$dosen', '$jam_kuliah', '$hari_kuliah', '$ruang')";
                  $result = $connect->query($query);
                  if ($result) {
                    $successTambah = true;
                    $successTambahText = 'ditambah';
                  } else {
                    $errorTambah = true;
                    $errorTambahText = $connect->error;
                  }
                }
              }

              if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $matkul = $_POST['matkul'];
                $dosen = $_POST['dosen'];
                $jam_kuliah = $_POST['jam_kuliah'];
                $hari_kuliah = $_POST['hari_kuliah'];
                $ruang = $_POST['ruang'];

                $errorTambah = true;
                if ($matkul == '-1') {
                  $errorTambahText = "Mata kuliah tidak boleh kosong";
                } else if ($dosen == '-1') {
                  $errorTambahText = "Dosen tidak boleh kosong";
                } else if ($jam_kuliah == '') {
                  $errorTambahText = "Jam kuliah tidak boleh kosong";
                } else if ($hari_kuliah == '-1') {
                  $errorTambahText = "Hari kuliah tidak boleh kosong";
                } else if ($ruang == '') {
                  $errorTambahText = "Ruang tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "UPDATE jadwal_kuliah SET id_mata_kuliah = '$matkul', id_dosen = '$dosen' , jam_kuliah = '$jam_kuliah', hari_kuliah = '$hari_kuliah', ruang = '$ruang' ";
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
                    <div><strong>Sukses!</strong> Jadwal kuliah berhasil <?= $successTambahText ?></div>
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
                  $query = "SELECT mata_kuliah.id as matkul, dosen.nip as dosen, jadwal_kuliah.* FROM jadwal_kuliah ";
                  $query .= "LEFT JOIN mata_kuliah ON mata_kuliah.id = jadwal_kuliah.id_mata_kuliah ";
                  $query .= "LEFT JOIN dosen ON dosen.nip = jadwal_kuliah.id_dosen ";
                  $query .= "WHERE jadwal_kuliah.id = '". $_GET['id'] ."'";
                  $result = $connect->query($query);
                  if ($result->num_rows > 0) {
                    $jadwal = $result->fetch_assoc();
                  }
              ?>
              <form class="row" method="POST" action="?page=jadwal">
                  <input type="hidden" name="id" value="<?= $jadwal['id'] ?>">
                  <?php
                    $query = "SELECT mata_kuliah.id, CONCAT(prodi.nama_prodi, ' | ', .mata_kuliah.nama_mata_kuliah, ' | ', mata_kuliah.sks) as matkul FROM mata_kuliah ";
                    $query .= "LEFT JOIN prodi ON prodi.id = mata_kuliah.id_prodi ";
                    $query .= "ORDER BY prodi.nama_prodi, nama_mata_kuliah";
                    $result = $connect->query($query);
                    $matkul = [];
                    if ($result->num_rows > 0) {
                      $matkul = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-3 mb-2">
                    <label class="labels">Mata Kuliah</label>
                    <select class="form-select" aria-label="Mata kuliah" name='matkul'>
                        <option value='-1'>Pilih Mata kuliah</option>
                        <?php                        
                          if (count($matkul) == 0) {
                              echo "<option selected>Tidak ada Mata kuliah</option>";
                          }
                          foreach ($matkul as $item) {
                              $selected = $jadwal['matkul'] == $item['id'] ? 'selected' : '';
                              echo "<option value='".$item['id']."' $selected>" . $item['matkul'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <?php
                    $query = "SELECT * FROM dosen ";
                    $query .= "ORDER BY nama_dosen";
                    $result = $connect->query($query);
                    $dosen = [];
                    if ($result->num_rows > 0) {
                      $dosen = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-3 mb-2">
                    <label class="labels">Dosen</label>
                    <select class="form-select" aria-label="Dosen" name='dosen'>
                        <option value='-1'>Pilih Dosen</option>
                        <?php                        
                          if (count($dosen) == 0) {
                              echo "<option selected>Tidak ada Dosen</option>";
                          }
                          foreach ($dosen as $item) {
                              $selected = $jadwal['dosen'] == $item['nip'] ? 'selected' : '';
                              echo "<option value='".$item['nip']."' $selected >" . $item['nama_dosen'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Jam Kuliah</label>
                    <input type="time" min=1 class="form-control" placeholder="Jam Kuliah" name='jam_kuliah' value="<?= $jadwal['jam_kuliah'] ?>">
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Hari Kuliah</label>
                    <select class="form-select" aria-label="Hari Kuliah" name='hari_kuliah'>
                        <option value='-1'>Pilih Hari Kuliah</option>
                        <option value='Senin' <?= $jadwal['hari_kuliah'] == 'Senin' ? 'selected' : '' ?>>Senin</option>
                        <option value='Selasa' <?= $jadwal['hari_kuliah'] == 'Selasa' ? 'selected' : '' ?>>Selasa</option>
                        <option value='Rabu' <?= $jadwal['hari_kuliah'] == 'Rabu' ? 'selected' : '' ?>>Rabu</option>
                        <option value='Kamis' <?= $jadwal['hari_kuliah'] == 'Kamis' ? 'selected' : '' ?>>Kamis</option>
                        <option value='Jumat' <?= $jadwal['hari_kuliah'] == 'Jumat' ? 'selected' : '' ?>>Jumat</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Ruang Kuliah</label>
                    <input type="text" min=1 class="form-control" placeholder="Ruang Kuliah" name='ruang' value="<?= $jadwal['ruang'] ?>">
                  </div>
                  <div class="col-12 text-right">
                      <a href="?page=jadwal" class="btn btn-danger" title='Batal Ubah'>Batal</a>
                      <button class="btn btn-primary" name='update' title='Tambah Siswa'>Simpan Mata Kuliah</button>
                  </div>
              </form>              
              <?php
                } else {
              // ADD MATKUL
              ?>
              <form class="row" method="POST">
                  <?php
                    $query = "SELECT mata_kuliah.id, CONCAT(prodi.nama_prodi, ' | ', .mata_kuliah.nama_mata_kuliah, ' | ', mata_kuliah.sks) as matkul FROM mata_kuliah ";
                    $query .= "LEFT JOIN prodi ON prodi.id = mata_kuliah.id_prodi ";
                    $query .= "ORDER BY prodi.nama_prodi, nama_mata_kuliah";
                    $result = $connect->query($query);
                    $matkul = [];
                    if ($result->num_rows > 0) {
                      $matkul = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-3 mb-2">
                    <label class="labels">Mata Kuliah</label>
                    <select class="form-select" aria-label="Mata kuliah" name='matkul'>
                        <option value='-1'>Pilih Mata kuliah</option>
                        <?php                        
                          if (count($matkul) == 0) {
                              echo "<option selected>Tidak ada Mata kuliah</option>";
                          }
                          foreach ($matkul as $item) {
                              echo "<option value='".$item['id']."'>" . $item['matkul'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <?php
                    $query = "SELECT * FROM dosen ";
                    $query .= "ORDER BY nama_dosen";
                    $result = $connect->query($query);
                    $dosen = [];
                    if ($result->num_rows > 0) {
                      $dosen = $result->fetch_all(MYSQLI_ASSOC);
                    }
                  ?>
                  <div class="col-12 col-md-3 mb-2">
                    <label class="labels">Dosen</label>
                    <select class="form-select" aria-label="Dosen" name='dosen'>
                        <option value='-1'>Pilih Dosen</option>
                        <?php                        
                          if (count($dosen) == 0) {
                              echo "<option selected>Tidak ada Dosen</option>";
                          }
                          foreach ($dosen as $item) {
                              echo "<option value='".$item['nip']."'>" . $item['nama_dosen'] . "</option>";
                          }
                        ?>
                    </select>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Jam Kuliah</label>
                    <input type="time" min=1 class="form-control" placeholder="Jam Kuliah" name='jam_kuliah'>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Hari Kuliah</label>
                    <select class="form-select" aria-label="Hari Kuliah" name='hari_kuliah'>
                        <option value='-1'>Pilih Hari Kuliah</option>
                        <option value='Senin'>Senin</option>
                        <option value='Selasa'>Selasa</option>
                        <option value='Rabu'>Rabu</option>
                        <option value='Kamis'>Kamis</option>
                        <option value='Jumat'>Jumat</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2 mb-2">
                    <label class="labels">Ruang Kuliah</label>
                    <input type="text" min=1 class="form-control" placeholder="Ruang Kuliah" name='ruang'>
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
                    <div><strong>Sukses!</strong> Jadwal kuliah <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if ($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Jadwal kuliah <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php      
                  $query = "SELECT jadwal_kuliah.*, mata_kuliah.nama_mata_kuliah, dosen.nama_dosen FROM jadwal_kuliah ";
                  $query .= "LEFT JOIN mata_kuliah ON mata_kuliah.id = jadwal_kuliah.id_mata_kuliah ";
                  $query .= "LEFT JOIN dosen ON dosen.nip = jadwal_kuliah.id_dosen ";
                  if (isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "WHERE mata_kuliah.nama_mata_kuliah LIKE '%$cari%' OR dosen.nama_dosen LIKE '%$cari%' ";
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
                                    <th>Dosen</th>
                                    <th>Jam Kuliah</th>
                                    <th>Hari</th>
                                    <th>Ruang</th>
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
                                    <td><?= $mataKuliah[$i]['nama_dosen'] ?></td>
                                    <td class="text-center"><?= $mataKuliah[$i]['jam_kuliah'] ?></td>
                                    <td class="text-center"><?= $mataKuliah[$i]['hari_kuliah'] ?></td>
                                    <td class="text-center"><?= $mataKuliah[$i]['ruang'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-4">
                                              <a 
                                                  href="?page=jadwal&action=edit&id=<?= $mataKuliah[$i]['id'] ?>"
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
                                    <td colspan="7" class='text-center'>Tidak ada jadwal kuliah</td>
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