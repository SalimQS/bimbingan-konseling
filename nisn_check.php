<?php
session_start();
if(isset($_SESSION['id_user'])) {
  header("location: index.php");
}
include_once('./config/db.php');

if(isset($_POST['submit'])) {
    $nisn = $_POST['nisn'];
    $nama_ibu = $_POST['nama_ibu'];
    header("Location: nisn_check.php?nisn=$nisn&ibu=$nama_ibu");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Main Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="./vendors/bootstrap-5.0.0-beta3-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">

    <!-- jQuery 3.6.0 -->
    <script defer src="./vendors/jQuery-3.6.0/jQuery.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script defer src="./vendors/bootstrap-5.0.0-beta3-dist/js/bootstrap.bundle.min.js"></script>
    <!-- FontAwesome -->
    <script defer src="./vendors/fontawesome-free-5.15.3-web/js/all.min.js"></script>
    <!-- Script JS -->
    <script defer src="./assets/js/script.js"></script>

</head>
<body class="login-bg">
    <div class="m-3">
        <a class="btn btn-primary shadow-lg" href="login.php"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="container">
        <?php
            if(!isset($_GET['nisn'])) {
        ?>
        <div class="row justify-content-center main-container">
            <div class="card p-0 card-login" style="width: 25vw;">
                <img src="assets/img/bg.jpg" class="card-img-top img-fluid" alt="...">
                <div class="card-body">
                    <?php if(isset($_GET['error'])) { ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                        <div><strong>Gagal!</strong> Data tidak ditemukan.</div>
                    </div>
                    <?php } ?>
                    <form method="post">
                        <div class="form-group">
                            <label class="label" for="name">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN Anda..." required>
                        </div>
                        <div class="form-group mt-3 mb-2">
                            <label class="label" for="name">Nama Ibu Kandung</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="Masukkan Nama Ibu Kandung Anda..." required>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="form-control btn rounded submit submit-btn" value="Cari">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            }
            else {
                $query = "SELECT kelas.nama_kelas, siswa.* FROM siswa ";
                $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
                $query .= "WHERE siswa.nisn='" . $_GET['nisn'] . "' AND siswa.nama_ibu='" . $_GET['ibu'] . "'";
                $result = $connect->query($query);
                if($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
        ?>
            <div class="row justify-content-center main-container">
                <div class="card p-0 card-login" style="width: 25vw;">
                    <img src="assets/img/bg.jpg" class="card-img-top img-fluid" alt="...">
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
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
                                <td>Kelas:</td>
                                <td><?= $user['nama_kelas'] ?></td>
                            </tr>
                            <tr>
                                <td>Jumlah Poin:</td>
                                <td>
                                    <?php 
                                        $query = "SELECT peraturan.poin_peraturan, pelanggaran.id_pelanggaran, siswa.nisn FROM `pelanggaran` ";
                                        $query .= "LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa ";
                                        $query .= "LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan ";
                                        $query .= "WHERE siswa.nisn='" . $_GET['nisn'] . "'";
                    
                                        $result = $connect->query($query);
                                        $poincount = 0;
                                        while(($poin = $result->fetch_assoc())) {
                                            $poincount += $poin['poin_peraturan'];
                                        }

                                        echo($poincount.' Poin');
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <div class="text-right">
                            <a class="btn btn-primary" href="nisn_check.php">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php   
                }
                else header("Location: nisn_check.php?error=1");
            }
        ?>
    </div>
</body>
</html>