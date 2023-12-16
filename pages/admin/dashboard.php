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

$query = "SELECT * FROM pelanggaran";
$result = $connect->query($query);
$pelanggaran = $result->num_rows;

$query = "SELECT * FROM sanksi";
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
          <p class='text-light'> List Pelanggaran </p>
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
</div>