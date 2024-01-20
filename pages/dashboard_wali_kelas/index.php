<?php
$query = "SELECT kelas.nama_kelas, siswa.id_siswa FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE kelas.nama_kelas='".$_SESSION['kelas']."' ";
$result = $connect->query($query);
$siswa = $result->num_rows;
$ratarata = 0;
$total_poin = 0;
//---
while($siswa_data = $result->fetch_assoc()) {
    $id_siswa = $siswa_data['id_siswa'];
    $query2 = "SELECT peraturan.poin_peraturan FROM pelanggaran ";
    $query2 .= "LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan ";
    $query2 .= "WHERE pelanggaran.id_siswa='$id_siswa'";
    $result2 = $connect->query($query2);
    $poincount = 0;
    while(($poin = $result2->fetch_assoc())) {
        $poincount += $poin['poin_peraturan'];
    }
    $total_poin += $poincount;
}
$ratarata = $total_poin / $siswa;
//---
$query = "SELECT * FROM peraturan";
$result = $connect->query($query);
$peraturan = $result->num_rows;
//---
$query = "SELECT siswa.id_siswa, kelas.nama_kelas FROM pelanggaran ";
$query .= "LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa ";
$query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
$query .= "WHERE kelas.nama_kelas='".$_SESSION['kelas']."'";
$result = $connect->query($query);
$pelanggaran = $result->num_rows;
?>
<div class="row mt-3">
<div class="col">
    <div class="small-box bg-success">
      <div class="inner">
          <h3> <?= $siswa ?> </h3>
          <p class='text-light'> Jumlah Siswa <?= $_SESSION['kelas'] ?> </p>
      </div>
      <i class="icon fas fa-users"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-primary">
      <div class="inner">
          <h3> <?= $peraturan ?> </h3>
          <p class='text-light'> Jumlah Peraturan </p>
      </div>
      <i class="icon fas fa-chalkboard-teacher"></i></i>
    </div>
  </div>
  
  <div class="col">
    <div class="small-box bg-warning">
      <div class="inner">
          <h3> <?= $ratarata ?> </h3>
          <p class='text-light'> Rata Rata Poin Siswa <?= $_SESSION['kelas'] ?> </p>
      </div>
      <i class="icon fas fa-book"></i></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-danger">
      <div class="inner">
          <h3> <?= $pelanggaran ?> </h3>
          <p class='text-light'> Jumlah Pelanggaran Siswa <?= $_SESSION['kelas'] ?> </p>
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
            $query .= "WHERE kelas.nama_kelas='".$_SESSION['kelas']."' ";
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