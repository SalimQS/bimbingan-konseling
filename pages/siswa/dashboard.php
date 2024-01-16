<?php
$query = "SELECT * FROM users ";
$query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
$query .= "WHERE siswa.id IS NOT NULL ";
$result = $connect->query($query);
$siswa = $result->num_rows;

$query = "SELECT * FROM users ";
$query .= "LEFT JOIN guru ON guru.id_user = users.id ";
$query .= "WHERE guru.id IS NOT NULL ";
$result = $connect->query($query);
$guru = $result->num_rows;

$query = "SELECT * FROM pelanggaran WHERE id_user = '" . $_SESSION['id_user'] . "'";
$result = $connect->query($query);
$pelanggaran = $result->num_rows;

$query = "SELECT aturan.poin FROM `pelanggaran` ";
$query .= "LEFT JOIN aturan ON aturan.id = pelanggaran.id_pelanggaran ";
$query .= "WHERE `id_user`='" . $_SESSION['id_user'] . "'";
$result = $connect->query($query);
$poin = 0;
while(($point = $result->fetch_assoc())) {
    $poin += $point['poin'];
}

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
          <p class='text-light'> Jumlah Pelanggaran Saya </p>
      </div>
      <i class="icon fas fa-book"></i></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-danger">
      <div class="inner">
          <h3> <?= $poin ?> Poin </h3>
          <p class='text-light'> Jumlah Poin </p>
      </div>
      <i class="icon fas fa-exclamation-circle"></i></i>
    </div>
  </div>

</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"><h4>Pengumuman Terbaru</h4></div>
      <div class="card-body">
        <?php
        $query = "SELECT * FROM berita ORDER BY created_at DESC";
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
</div>