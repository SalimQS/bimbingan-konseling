<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=siswa"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<?php
  $query = "SELECT guru.*, kelas.nama_kelas FROM guru ";
  $query .= "LEFT JOIN kelas ON kelas.id_wali_kelas = guru.id_guru ";
  $query .= "WHERE guru.id_guru='" . $_SESSION['id_user'] . "'";
  $result = $connect->query($query);
  if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
  } else {
    echo('<script>alert("error '.$result->num_rows.'")</script>');
  }
?>
<div class="row justify-content-md-center mt-5">
    <div class="col-md-6">
        <div>
            <table class="table table-bordered table-striped">
                <tr>
                    <td rowspan="6" class="text-center">
                        <img class="border border-dark object-cover mt-4 mb-4" width="150px" height="150px" src="<?= $user['foto_guru'] ?>" id='previewFoto'>
                    </td>
                </tr>
                <tr>
                    <td>NUPTK:</td>
                    <td><?= $user['nuptk'] ?></td>
                </tr>
                <tr>
                    <td>Username:</td>
                    <td><?= $user['username_guru'] ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap:</td>
                    <td><?= $user['nama_guru'] ?></td>
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
                    <td>Nomer Telepon:</td>
                    <td><?= $user['no_telepon'] ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>