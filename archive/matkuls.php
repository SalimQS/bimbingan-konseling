<?php
$user = [];
$query = "SELECT users.username, biodata.*, siswa.semester, YEAR(biodata.created_at) as angkatan, prodi.nama_prodi, fakultas.nama_fakultas, dosen.nama_dosen FROM users ";
$query .= "LEFT JOIN biodata ON biodata.id_user = users.id ";
$query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
$query .= "LEFT JOIN prodi ON siswa.id_prodi = prodi.id ";
$query .= "LEFT JOIN fakultas ON prodi.id_fakultas = fakultas.id ";
$query .= "LEFT JOIN dosen ON dosen.nip = siswa.id_dosen ";
$query .= "WHERE users.id='". $_SESSION['id_user'] ."'";
$result = $connect->query($query);
if($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="callout callout-info">
                    <div class="row">
                        <label class="col-md-3">NIM</label>
                        <div class="col-md-3"><?= $user['username'] ?></div>
                        <label class="col-md-3">Nama Siswa</label>
                        <div class="col-md-3"><?= $user['nama_lengkap'] ?></div>
                    </div>
                    <div class="row">
                        <label class="col-md-3">Status Siswa</label>
                        <div class="col-md-3"><?= $user['status'] ? 'Aktif' : 'Tidak Aktif' ?></div>
                        <label class="col-md-3">Fakultas</label>
                        <div class="col-md-3"><?= $user['nama_fakultas'] ?></div>
                    </div>
                    <div class="row">
                        <label class="col-md-3">Angkatan</label>
                        <div class="col-md-3"><?= $user['angkatan'] ?></div>
                        <label class="col-md-3">Program Studi</label>
                        <div class="col-md-3"><?= $user['nama_prodi'] ?></div>
                    </div>
                    <div class="row">
                        <label class="col-md-3">Pembimbing Akademik</label>
                        <div class="col-md-3"><?= $user['nama_dosen'] ?></div>
                        <label class="col-md-3">Semester</label>
                        <div class="col-md-3"><?= $user['semester'] ?></div>
                    </div>
                </div>
                <?php                    
                    $successInsertMatkul = false;
                    $errorInsertMatkul = false;
                    $successDelete = false;
                    $errorDelete = false;
                    if(isset($_POST['insertMatkul'])) {
                        $idMataKuliah = $_POST['selected_matkul'];
                        $query = "SELECT id FROM jadwal_kuliah WHERE id_mata_kuliah='".$idMataKuliah."'";
                        $result = $connect->query($query)->fetch_assoc();
                        $query = "INSERT INTO jadwal_siswa (id_jadwal_kuliah, id_user, semester) ";
                        $query .= "VALUES('".$result['id']."', '".$_SESSION['id_user']."', '".$user['semester']."')";
                        $result = $connect->query($query);
                        if($result) {
                            $successInsertMatkul = true;
                        } else {
                            $errorInsertMatkul = true;
                        }
                    }
                    if(isset($_POST['delete'])) {
                        $query = "SELECT id FROM jadwal_siswa WHERE id='" . $_POST['id'] . "'";
                        $result = $connect->query($query);
                        if($result->num_rows > 0) {
                            $query = "DELETE FROM jadwal_siswa WHERE id = '" . $_POST['id'] . "'";
                            $result = $connect->query($query);
                            if($query) {
                                $successDelete = true;
                            } else {
                                $errorDelete = true;
                            }
                        }
                    }

                ?>                    
                <?php if($successInsertMatkul) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Berhasil!</strong> Mata kuliah berhasil ditambah</div>
                </div>
                <?php } ?>
                <?php if($errorInsertMatkul) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Mata kuliah gagal ditambah</div>
                </div>
                <?php } ?>
                <?php if($successDelete) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Mata kuliah berhasil dihapus</div>
                </div>
                <?php } ?>
                <?php if($errorDelete) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Mata kuliah gagal dihapus</div>
                </div>
                <?php } ?>
                <?php                
                    $query = "SELECT mata_kuliah.id, mata_kuliah.nama_mata_kuliah, mata_kuliah.semester ";
                    $query .= "FROM jadwal_kuliah LEFT JOIN mata_kuliah ON mata_kuliah.id = jadwal_kuliah.id_mata_kuliah ";
                    $query .= "WHERE mata_kuliah.semester='". $user['semester']."'";
                    $result = $connect->query($query);
                    $matkul = [];
                    $resultMatkul = [];
                    if($result->num_rows > 0) {
                        $matkul = $result->fetch_all(MYSQLI_ASSOC);
                    }
                    $query = "SELECT mata_kuliah.id, mata_kuliah.nama_mata_kuliah, mata_kuliah.semester FROM jadwal_siswa ";
                    $query .= "LEFT JOIN jadwal_kuliah ON jadwal_kuliah.id = jadwal_siswa.id_jadwal_kuliah ";
                    $query .= "LEFT JOIN mata_kuliah ON mata_kuliah.id = jadwal_kuliah.id_mata_kuliah ";
                    $query .= "WHERE jadwal_siswa.id_user='".$_SESSION['id_user']."' AND jadwal_siswa.semester='".$user['semester']."'";
                    $result = $connect->query($query);
                    $availMatkul = [];
                    if($result->num_rows > 0) {
                        $availMatkul = $result->fetch_all(MYSQLI_ASSOC);
                    }
                    foreach ($matkul as $item) {
                        if(!in_array($item, $availMatkul)) {
                            array_push($resultMatkul, $item);
                        }
                    }
                ?>
                <form method="POST">
                    <div class="row mb-4">
                        <div class="col-9">
                            <div class="form-group">
                                <select class="form-control" name="selected_matkul" id="" <?php echo count($resultMatkul) == 0 ? "disabled" : "" ?>>
                                <?php
                                    if(count($resultMatkul) == 0) {
                                        echo "<option>Tidak ada mata kuliah</option>";
                                    }
                                    foreach ($resultMatkul as $item) {
                                        echo "<option value='".$item['id']."'>" . $item['nama_mata_kuliah'] . "</option>";
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-3 d-grid">
                            <button name="insertMatkul" id="" class="btn btn-primary" role="button" <?php echo count($resultMatkul) == 0 ? "disabled" : "" ?>>tambah</button>
                        </div>
                    </div>
                </form>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Mata kuliah</th>
                                    <th>Ruang</th>
                                    <th>SKS</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = "SELECT jadwal_siswa.id, mata_kuliah.id as id_mata_kuliah, mata_kuliah.nama_mata_kuliah, mata_kuliah.sks, jadwal_kuliah.jam_kuliah, jadwal_kuliah.hari_kuliah, jadwal_kuliah.ruang, dosen.nama_dosen ";
                            $query .= "FROM jadwal_siswa ";
                            $query .= "LEFT JOIN jadwal_kuliah ON jadwal_kuliah.id = jadwal_siswa.id_jadwal_kuliah ";
                            $query .= "LEFT JOIN mata_kuliah ON mata_kuliah.id = jadwal_kuliah.id_mata_kuliah  ";
                            $query .= "LEFT JOIN dosen ON dosen.nip = jadwal_kuliah.id_dosen ";
                            $query .= "WHERE jadwal_siswa.id_user='". $_SESSION['id_user'] ."' AND jadwal_siswa.semester='". $user['semester'] ."' ";
                            $result = $connect->query($query);
                            $totalSKS = 0;
                            if($result->num_rows > 0) {
                                $mata_kuliah = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($mata_kuliah); $i++) {
                                    $totalSKS += $mata_kuliah[$i]['sks'];
                                    ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><?= $mata_kuliah[$i]['id_mata_kuliah'] ?></td>
                                    <td><?= $mata_kuliah[$i]['nama_mata_kuliah'] ?></td>
                                    <td class="text-center"><?= $mata_kuliah[$i]['ruang'] ?></td>
                                    <td class="text-center"><?= $mata_kuliah[$i]['sks'] ?></td>
                                    <td><?= $mata_kuliah[$i]['hari_kuliah'] ?>, <?= $mata_kuliah[$i]['jam_kuliah'] ?></td>
                                    <td class="text-center">
                                        <form method="post">
                                        <input type="hidden" name="id" value="<?= $mata_kuliah[$i]['id'] ?>"/>
                                            <button 
                                                name='delete' 
                                                class='btn btn-sm btn-danger' 
                                                type='submit'
                                                onClick="javascript: return confirm('Yakin menghapus mata kuliah <?= $mata_kuliah[$i]['nama_mata_kuliah']?>?');"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                ?>                                
                                <tr>
                                    <td colspan="8" class='text-center'>Tidak ada mata kuliah</td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr class='fw-bold'>
                                <td colspan="4" class="text-right">Total SKS yang di ambil</td>
                                <td class='text-center'><?= $totalSKS ?></td>
                                <td colspan="2"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>