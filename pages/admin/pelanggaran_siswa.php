<div class="row">
  <div class="col-12 col-md-6">
      <a class="btn btn-primary" href="?page=siswa"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<?php
    if(isset($_POST['submit'])) {
        $errorCount = 0;
        unset($_POST['submit']);
        for($i = 1; $i <= count($_POST['id']); $i++) {
            if(isset($_POST['checked'][$i])) {
                $id = $_POST['id'][$i];
                $id_siswa = $_GET['id'];
                //---
                $query = "INSERT INTO `pelanggaran`(`id_siswa`, `id_pelanggaran`) VALUES ('$id_siswa','$id')";
                $result = $connect->query($query);
                if(!$result) {
                    $errorCount++;
                }
            }
        }
        //---
        if($errorCount >= 1) {
        ?>
            <script src="vendors/sweetalert/sweetalert.min.js"></script>
            <script type="text/javascript">
            Swal.fire({
                title: "Gagal!",
                text: "Gagal menambahkan pelanggaran",
                icon: 'error'
            });
            </script>
        <?php
        }
        else {
        ?>
            <script src="vendors/sweetalert/sweetalert.min.js"></script>
            <script type="text/javascript">
            Swal.fire({
                title: "Berhasil!",
                text: "Berhasil menambahkan pelanggaran",
                icon: 'success'
            });
            </script>
        <?php
        }
    }

    $query = "SELECT kelas.kelas, kelas.id as kelas_id, siswa.nisn, data.* FROM siswa ";
    $query .= "LEFT JOIN data ON data.id_siswa = siswa.id ";
    $query .= "LEFT JOIN kelas ON kelas.id = siswa.id_kelas ";
    $query .= "WHERE siswa.id='" . $_GET['id'] . "'";
    $result = $connect->query($query);
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $foto = $user['foto'];
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
                        <img class="border border-dark object-cover mt-4 mb-4" width="150px" height="150px" src="<?= $user['foto'] ?>" id='previewFoto'>
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
            $query = "SELECT * FROM aturan WHERE 1";
        ?>
        <form method="post">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr class="bg-primary text-white">
                        <td>No</td>
                        <td>Peraturan</td>
                        <td>Sanksi</td>
                        <td>Tambah</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $result = $connect->query($query);
                    if($result->num_rows > 0) {
                        $peraturan = $result->fetch_all(MYSQLI_ASSOC);
                        for ($i = 0; $i < count($peraturan); $i++) {
                    ?>                                
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="text-center"><?= $peraturan[$i]['jenis'] ?></td>
                            <td class="text-center"><?= $peraturan[$i]['poin'] ?></td>
                            <td class="text-center"><input type="checkbox" class="form-check-input" name="checked[<?= $i+1 ?>]"><input type="hidden" name="id[<?= $i+1 ?>]" value="<?= $peraturan[$i]['id'] ?>"></td>
                        </tr>
                    <?php
                        }
                    } 
                    else {
                        ?>                                
                        <tr>
                            <td colspan="4" class='text-center'>Tidak ada peraturan</td>
                        </tr>
                        <?php
                    }
                ?>
                </tbody>
            </table>
            <div class="col-md-12 text-right mb-5">
                <input class="btn btn-primary" type="submit" name="submit" value="Tambah">
            </div>
        </form>
    </div>
</div>