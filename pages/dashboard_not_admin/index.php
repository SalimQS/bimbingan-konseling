<?php
$query = "SELECT * FROM siswa WHERE 1 ";
$result = $connect->query($query);
$siswa = $result->num_rows;

$query = "SELECT * FROM guru WHERE 1 ";
$result = $connect->query($query);
$guru = $result->num_rows;

$query = "SELECT * FROM peraturan";
$result = $connect->query($query);
$pelanggaran = $result->num_rows;

$query = "SELECT * FROM pelanggaran";
$result = $connect->query($query);
$pelanggar = $result->num_rows;

/*$query = "SELECT id FROM prodi";
$result = $connect->query($query);
$prodi = $result->num_rows;

$query = "SELECT id FROM fakultas";
$result = $connect->query($query);
$fakultas = $result->num_rows;*/

?>
<div class="row mt-3">
<div class="col">
    <div class="small-box bg-primary">
      <div class="inner">
          <h3> <?= $siswa ?> </h3>
          <p class='text-light'> Jumlah Siswa </p>
      </div>
      <i class="icon fas fa-users"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-success">
      <div class="inner">
          <h3> <?= $guru ?> </h3>
          <p class='text-light'> Jumlah Guru </p>
      </div>
      <i class="icon fas fa-chalkboard-teacher"></i></i>
    </div>
  </div>
  
  <div class="col">
    <div class="small-box bg-warning">
      <div class="inner">
          <h3> <?= $pelanggaran ?> </h3>
          <p class='text-light'> List Peraturan </p>
      </div>
      <i class="icon fas fa-book"></i></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-danger">
      <div class="inner">
          <h3> <?= $pelanggar ?> </h3>
          <p class='text-light'> Jumlah Pelanggaran </p>
      </div>
      <i class="icon fas fa-exclamation-circle"></i>
    </div>
  </div>

</div>
<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="dashboard-pelanggar-page">
          <?php
            $query = "SELECT siswa.id_siswa, peraturan.jenis_peraturan, peraturan.poin_peraturan, siswa.nama_lengkap, kelas.nama_kelas, siswa.nisn, pelanggaran.tanggal_pelanggaran, pelanggaran.tempat_pelanggaran, pelanggaran.id_pelanggaran FROM pelanggaran ";
            $query .= "LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa ";
            $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
            $query .= "LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan ";
            $query .= "WHERE 1 ";
            if(isset($_GET['kelas'])) {
              $kelas = $_GET['kelas'];
              $query .= "AND kelas.id_kelas='$kelas' ";
            }
            if(isset($_POST['btn-cari'])) {
                $cari = $_POST['cari'];
                $query .= "AND (peraturan.jenis_peraturan LIKE '%$cari%' OR siswa.nama_lengkap LIKE '%$cari%') ";
            }
            $query .= "ORDER BY pelanggaran.tanggal_pelanggaran desc";
          ?>
          <div class="row">     
            <div class="col-12 col-md-4 mt-2">
              <form method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name='cari' placeholder="Cari Pelanggaran (Nama atau Jenis Pelanggaran)" aria-label="Cari berdasarkan nama pelanggaran" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                    <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                </div>
              </form>
            </div>
            <div class="col-12 col-md-4 mt-2">
              <select class="form-select kelasFilter" name="kelas">
                  <?php
                  $query2 = "SELECT id_kelas as id, kelas.nama_kelas as kelas, guru.nama_guru as nama_lengkap FROM kelas LEFT JOIN guru ON guru.id_guru = kelas.id_wali_kelas WHERE 1";
                  $result2 = $connect->query($query2);
                  if($result2->num_rows > 0) {
                      echo('<option value="-1">Semua Kelas</option>');
                      while($kelas = $result2->fetch_assoc()) {
                          if(isset($_GET['kelas'])) {
                              if($_GET['kelas'] == $kelas['id']) {
                                  echo('<option value="'.$kelas['id'].'" selected>'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                              }
                              else echo('<option value="'.$kelas['id'].'">'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                          }
                          else echo('<option value="'.$kelas['id'].'">'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                      }
                  }
                  else echo('<option value="-1">Kelas Kosong</option>');
                  ?>
              </select>
          </div> 
          </div>
          <div>
              <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
                      <thead class="bg-primary text-white">
                          <tr class="text-center">
                              <th>No</th>
                              <th>NISN</th>
                              <th>Nama Pelanggar</th>
                              <th>Kelas</th>
                              <th>Jenis Pelanggaran</th>
                              <th>Sanksi</th>
                              <th>Tanggal Pelanggaran</th>
                              <th>Tempat Pelanggaran</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php
                      $result = $connect->query($query);
                      if($result->num_rows > 0) {
                          $pelanggar = $result->fetch_all(MYSQLI_ASSOC);
                          for ($i = 0; $i < count($pelanggar); $i++) {
                      ?>                                
                          <tr>
                              <td class="text-center"><?= $i + 1 ?></td>
                              <td class="text-center"><?= $pelanggar[$i]['nisn'] ?></td>
                              <td class="text-center"><a href="index.php?page=siswa&action=lihat&id=<?= $pelanggar[$i]['id_siswa'] ?>"><?= $pelanggar[$i]['nama_lengkap'] ?></a></td>
                              <td class="text-center"><?= $pelanggar[$i]['nama_kelas'] ?></td>
                              <td class="text-center"><?= $pelanggar[$i]['jenis_peraturan'] ?></td>
                              <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $pelanggar[$i]['poin_peraturan'] ?> Poin</span></td>
                              <td class="text-center"><?= $pelanggar[$i]['tanggal_pelanggaran'] ?></td>
                              <td class="text-center"><?= $pelanggar[$i]['tempat_pelanggaran'] ?></td>
                          </tr>
                      <?php
                          }
                      } 
                      else {
                          ?>                                
                          <tr>
                              <td colspan="10" class='text-center'>Tidak ada pelanggar</td>
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

<!--<div class="row mt-3">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header">
        <div class="row">
          <div class="col-12 col-md-4">
            <h3 style="display: inline-block;">Pengumuman Terbaru</h3>
          </div>
          <div class="col-12 col-md-8 text-right">
            <a href="?page=pengumuman" name='pengumuman' class='btn btn-sm btn-primary text-right' title='Tambah Pengumuman'>
                <i class="fa fa-pencil-alt"></i> Ubah
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
      <?php
      $query = "SELECT * FROM berita";
      $result = $connect->query($query);
      if($result->num_rows > 0) {
        $berita = $result->fetch_all(MYSQLI_ASSOC);
        for ($i = 0; $i < count($berita); $i++) {
      ?>                     
      
      <div class='border-bottom mb-4'>
      <h5><?=$berita[$i]['judul']?> <small style="color: #cecece;"><?=$berita[$i]['created_at']?></small></h5>
      <p><?=$berita[$i]['berita']?></p>
      </div>

      <?php        
        }   
      } else {
      ?>
      <h5>Tidak ada pengumuman</h5>
      <?php
      }
      ?>
      </div>
    </div>
  </div>
</div>-->