<div class="row mt-3">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">        
              <?php
              $successTambah = false;
              $errorTambah = false;
              $errorTambahText = "";
              if(isset($_POST['tambah'])) {
                $judul = $_POST['judul'];
                $isi_berita = $_POST['isi_berita'];

                $errorTambah = true;
                if($judul == '') {
                  $errorTambahText = "Judul tidak boleh kosong";
                } else if($isi_berita == '') {
                  $errorTambahText = "Isi pengumuman tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "INSERT into berita (judul, berita) ";
                  $query .= "VALUES ('$judul', '$isi_berita')";
                  $result = $connect->query($query);
                  if($result) {
                    $successTambah = true;
                    $successTambahText = 'dipublish';
                  } else {
                    $errorTambah = true;
                    $errorTambahText = "Pengumuman gagal dipublish";
                  }
                }
              }

              if(isset($_POST['update'])) {
                $id = $_POST['id'];
                $judul = $_POST['judul'];
                $isi_berita = $_POST['isi_berita'];

                $errorTambah = true;
                if($judul == '') {
                  $errorTambahText = "Judul tidak boleh kosong";
                } else if($isi_berita == '') {
                  $errorTambahText = "Isi pengumuman tidak boleh kosong";
                } else {
                  $errorTambah = false;
                  $query = "UPDATE berita SET judul = '$judul', berita = '$isi_berita' ";
                  $query .= "WHERE id = $id";
                  $result = $connect->query($query);
                  if($result) {
                    $successTambah = true;
                    $successTambahText = "diubah";
                  } else {
                    $errorTambah = true;
                    $errorTambahText = "Pengumuman gagal diubah";
                  }
                }
              }

              ?>
                <?php if($successTambah) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Pengumuman berhasil <?= $successTambahText ?></div>
                </div>
                <?php } ?>
                <?php if($errorTambah) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> <?= $errorTambahText ?></div>
                </div>
                <?php } ?>
              <?php
              // EDIT BERITA
                if(@$_GET['action'] == 'edit' && isset($_GET['id'])) {
                  $query = "SELECT * FROM berita ";
                  $query .= "WHERE id = '". $_GET['id'] ."'";
                  $result = $connect->query($query);
                  if($result->num_rows > 0) {
                    $pengumuman = $result->fetch_assoc();
                  }
              ?>
              <form class="row" method="POST" action="?page=pengumuman">
                  <input type="hidden" name="id" value="<?= $pengumuman['id'] ?>">
                  <div class="col-12 mb-2">
                    <label class="labels">Judul</label>
                    <input type="text" class="form-control" placeholder="Judul" name='judul' value="<?= $pengumuman['judul'] ?>">
                  </div>
                  <div class="col-12 mb-2">
                    <label class="labels">Isi Pengumuman</label>
                    <textarea  class="form-control" rows='4' placeholder="Isi pengumuman" name='isi_berita'><?= $pengumuman['berita'] ?></textarea>
                  </div>
                  <div class="col-12 text-right">
                      <a href="?page=matkul" class="btn btn-danger" title='Batal Ubah'>Batal</a>
                      <button class="btn btn-primary" name='update' title='Tambah Siswa'>Simpan Perubahan</button>
                  </div>
              </form>              
              <?php
                } else {
              // ADD BERITA
              ?>
              <form class="row" method="POST">
                  <div class="col-12 mb-2">
                    <label class="labels">Judul</label>
                    <input type="text" class="form-control" placeholder="Judul" name='judul'>
                  </div>
                  <div class="col-12 mb-2">
                    <label class="labels">Isi Pengumuman</label>
                    <textarea  class="form-control" rows='4' placeholder="Isi pengumuman" name='isi_berita'></textarea>
                  </div>
                  <div class="col-12 text-right">
                      <button class="btn btn-primary" name='tambah' title='Tambah Siswa'>Publish</button>
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
                    $query = "SELECT id FROM berita WHERE id= '". $_POST['id'] ."'";
                    $result = $connect->query($query);
                    if($result->num_rows > 0) {
                      $query = "DELETE FROM berita WHERE id = '". $_POST['id'] ."'";
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
                    <div><strong>Sukses!</strong> Pengumuman <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Pengumuman <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php      
                  $query = "SELECT * FROM berita ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "WHERE judul LIKE '%$cari%' OR berita LIKE '%$cari%' ";
                  }
                  $query .= "ORDER BY created_at DESC";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari pengumuman" aria-label="Cari" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
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
                                    <th>Judul</th>
                                    <th>Isi</th>
                                    <th>Tanggal Publish</th>
                                    <th>Tanggal Ubah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $berita = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($berita); $i++) {
                                    ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><?= $berita[$i]['judul'] ?></td>
                                    <td><?= strlen($berita[$i]['berita']) > 100 ? substr($berita[$i]['berita'],0,100) . '...' : $berita[$i]['berita'] ?></td>
                                    <td class="text-center"><?= date_format(date_create($berita[$i]['created_at']), "d-M-Y") ?></td>
                                    <td class="text-center"><?= date_format(date_create($berita[$i]['updated_at']), "d-M-Y") ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-4">
                                              <a 
                                                  href="?page=pengumuman&action=edit&id=<?= $berita[$i]['id'] ?>"
                                                  name='delete' 
                                                  class='btn btn-sm btn-primary' 
                                                  title='Ubah Data pengumuman'
                                              >
                                                  <i class="fa fa-pencil-alt"></i> Ubah
                                              </a>
                                            </div>
                                            <form method="post" class="col-12 col-md-4 formDelete" nama-pengumuman="<?= $berita[$i]['judul'] ?>">
                                                <input type="hidden" name="id" value="<?= $berita[$i]['id'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button 
                                                    class='btn btn-sm btn-danger' 
                                                    title='Hapus Data pengumuman'
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
                                    <td colspan="6" class='text-center'>Tidak ada pengumuman</td>
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