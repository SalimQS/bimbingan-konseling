<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=siswa"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<?php
  $success = false;
  $error = false;
  $errorText = "";
  $errorPass = false;    
  $errorText = "";
  $successPass = false;
  $successText = "";
  $successFoto = false;
  $errorFoto = false;
  $errorFotoText = "";

  $query = "SELECT kelas.nama_kelas, kelas.id_kelas as kelas_id, siswa.* FROM siswa ";
  $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
  $query .= "WHERE siswa.id_siswa='" . $_GET['id'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
  } else {
    echo('<script>alert("error '.$result->num_rows.'")</script>');
  }
?>
<div class="row justify-content-md-center mt-5">
    <div class="col-md-3">
        <div>
            <table class="table table-bordered table-striped">
                <tr>
                    <td colspan="2" class="text-center">
                        <img class="border border-dark object-cover mt-4 mb-4" width="150px" height="150px" src="<?= $user['foto_siswa'] ?>" id='previewFoto'>
                    </td>
                </tr>
                <tr>
                    <td>NISN:</td>
                    <td><?= $user['nisn'] ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap:</td>
                    <td><?= $user['nama_lengkap'] ?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin:</td>
                    <td>
                        <?php 
                            if($user['jenis_kelamin'] == 0) echo 'Laki-Laki'; 
                            else if($user['jenis_kelamin'] == 1) echo 'Perempuan'; 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Tempat Lahir:</td>
                    <td><?= $user['tempat_lahir'] ?></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir:</td>
                    <td><?= $user['tanggal_lahir'] ?></td>
                </tr>
                <tr>
                    <td>Agama:</td>
                    <td>
                        <?php 
                            if($user['agama'] == 0) echo 'Islam'; 
                            else if($user['agama'] == 1) echo 'Kristen Protestan'; 
                            else if($user['agama'] == 2) echo 'Kristen Katholik'; 
                            else if($user['agama'] == 3) echo 'Hindu'; 
                            else if($user['agama'] == 4) echo 'Buddha'; 
                            else if($user['agama'] == 5) echo 'Konghucu'; 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Alamat:</td>
                    <td><?= $user['alamat'] ?></td>
                </tr>
                <tr>
                    <td>Nomer Telepon:</td>
                    <td><?= $user['no_telepon'] ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-7">
        <?php
            $query = "SELECT peraturan.jenis_peraturan, peraturan.poin_peraturan, siswa.nama_lengkap, kelas.nama_kelas, siswa.nisn, pelanggaran.tanggal_pelanggaran, pelanggaran.tempat_pelanggaran, pelanggaran.id_pelanggaran FROM pelanggaran ";
            $query .= "LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa ";
            $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
            $query .= "LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan ";
            $query .= "WHERE siswa.id_siswa='" . $_GET['id'] . "'";
        ?>
        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr class="bg-primary text-white">
                    <td>No</td>
                    <td>Aturan Yang Dilanggar</td>
                    <td>Sanksi Pelanggaran</td>
                    <td>Waktu Pelanggaran</td>
                    <td>Tempat Pelanggaran</td>
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
                        <td class="text-center"><?= $pelanggar[$i]['jenis_peraturan'] ?></td>
                        <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $pelanggar[$i]['poin_peraturan'] ?> Poin</span></td>
                        <td class="text-center"><?= $pelanggar[$i]['tanggal_pelanggaran'] ?></td>
                        <td class="text-center"><?= $pelanggar[$i]['tempat_pelanggaran'] ?></td>
                    </tr>
                <?php
                    }
                    //---
                    $query = "SELECT peraturan.poin_peraturan, pelanggaran.id_pelanggaran FROM `pelanggaran` ";
                    $query .= "LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan ";
                    $query .= "WHERE pelanggaran.id_siswa='" . $_GET['id'] . "'";

                    $result = $connect->query($query);
                    $poincount = 0;
                    while(($poin = $result->fetch_assoc())) {
                        $poincount = $poincount + $poin['poin_peraturan'];
                    }
                ?>
                    <tr><td colspan="5">Total: <span class='bg-danger rounded-pill px-2 text-white'><?= $poincount ?> Poin</span></td></tr>
                <?php
                } 
                else {
                    ?>                                
                    <tr>
                        <td colspan="5" class='text-center'>Tidak ada pelanggaran</td>
                    </tr>
                    <?php
                }
            ?>
            </tbody>
        </table>
    </div>
</div>