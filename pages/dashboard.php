<?php
$query = "SELECT semester FROM siswa WHERE id_user = '". $_SESSION['id_user'] ."'";
$result = $connect->query($query);
$semester = $result->fetch_assoc();

$query = "SELECT id FROM jadwal_siswa WHERE id_user = '". $_SESSION['id_user'] ."'";
$result = $connect->query($query);
$jadwal_kuliah = $result->num_rows;

?>

<div class="row mt-3">
  <div class="col">
    <div class="small-box bg-primary">
      <div class="inner">
          <h3> <?= $jadwal_kuliah ?> </h3>
          <p class='text-light'> Jadwal Kuliah </p>
      </div>
      <i class="icon fas fa-clipboard-list"></i>
    </div>
  </div>

  <div class="col">
    <div class="small-box bg-warning">
      <div class="inner">
          <h3> <?= $semester['semester'] ?> </h3>
          <p class='text-light'> Semester </p>
      </div>
      <i class="icon fas fa-bookmark"></i>
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