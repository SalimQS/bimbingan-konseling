<?php
$query = "SELECT id FROM users WHERE id_role = 2";
$result = $connect->query($query);
$siswa = $result->num_rows;

$query = "SELECT id FROM mata_kuliah";
$result = $connect->query($query);
$mata_kuliah = $result->num_rows;

$query = "SELECT id FROM prodi";
$result = $connect->query($query);
$prodi = $result->num_rows;

$query = "SELECT id FROM fakultas";
$result = $connect->query($query);
$fakultas = $result->num_rows;

?>
<div class="row mt-3">
  <div class="col">
    <div class="small-box bg-primary">
      <div class="inner">
          <h3> <?= $siswa ?> </h3>
          <p class='text-light'> Siswa </p>
      </div>
      <i class="icon fas fa-users"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-success">
      <div class="inner">
          <h3> <?= $mata_kuliah ?> </h3>
          <p class='text-light'> Mata Kuliah </p>
      </div>
      <i class="icon fas fa-book"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-warning">
      <div class="inner">
          <h3> <?= $prodi ?> </h3>
          <p class='text-light'> Program Studi </p>
      </div>
      <i class="icon fas fa-clipboard-list"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-danger">
      <div class="inner">
          <h3> <?= $fakultas ?> </h3>
          <p class='text-light'> Fakultas </p>
      </div>
      <i class="icon fas fa-graduation-cap"></i>
    </div>
  </div>

</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"><h3>Pengumuman Terbaru</h3></div>
      <div class="card-body">
      <?php
      $query = "SELECT * FROM berita";
      $result = $connect->query($query);
      if ($result->num_rows > 0) {
        $berita = $result->fetch_all(MYSQLI_ASSOC);
        for ($i = 0; $i < count($berita); $i++) {
      ?>                     
      
      <div class='border-bottom mb-4'>
      <h5><?=$berita[$i]['judul']?></h5>
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