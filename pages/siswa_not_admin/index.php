<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php
                $errorDelete = false;

                $successStatus = false;
                $successText = "";
                $errorStatus = false;
                $errorText = "";

                if(isset($_POST['delete'])) {
                    $query = "DELETE FROM siswa WHERE id = '". $_POST['id'] ."'";
                    $result = $connect->query($query);
                    if($result) {
                        $successStatus = true;
                        $successText = "dihapus";
                    } else {
                        $errorStatus = true;
                        $errorText = "dihapus";
                    }
                }

                ?>
                <?php if($successStatus) { ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Sukses!</strong> Siswa Berhasil <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Siswa gagal <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php
                    $query = "SELECT siswa.*, siswa.id_siswa as id, kelas.nama_kelas FROM siswa ";
                    $query .= "LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas ";
                    $query .= "WHERE 1 ";
                    if(isset($_GET['kelas'])) {
                        $kelas = $_GET['kelas'];
                        $query .= "AND kelas.id_kelas='$kelas' ";
                    }
                    if(isset($_POST['btn-cari'])) {
                        $cari = $_POST['cari'];
                        $query .= "AND (siswa.nisn LIKE '%$cari%' OR siswa.nama_lengkap LIKE '%$cari%') ";
                    }
                    $query .= "ORDER BY siswa.nama_lengkap";
                ?>
                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <form method="post">
                            <div class="input-group">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Nama (NISN atau Nama Siswa)" aria-label="Cari berdasarkan nama" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-3 text-left">
                        <select class="form-select" name="kelas" id="kelasFilter">
                            <?php
                            $query2 = "SELECT id_kelas as id, kelas.nama_kelas as kelas, guru.nama_guru as nama_lengkap FROM kelas LEFT JOIN guru ON guru.id_guru = kelas.id_wali_kelas WHERE 1";
                            $result2 = $connect->query($query2);
                            if($result2->num_rows > 0) {
                                echo('<option value="-1">Semua Kelas</option>');
                                while($kelas = $result2->fetch_assoc()) {
                                    if(isset($_GET['kelas'])) {
                                        if($_GET['kelas'] == $kelas['id']) {
                                            echo('<option value="'.$kelas['id'].'" selected>'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                                        }
                                        else echo('<option value="'.$kelas['id'].'">'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                                    }
                                    else echo('<option value="'.$kelas['id'].'">'.$kelas['kelas'].' (Wali Kelas: '.$kelas['nama_lengkap'].')</option>');
                                }
                            }
                            else echo('<option value="-1">Kelas Kosong</option>');
                            ?>
                        </select>
                    </div> 
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Foto</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $siswa = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($siswa); $i++) {
                                    $nama = $siswa[$i]['nama_lengkap'];
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><?= $siswa[$i]['nisn'] ?></td>
                                    <td class="text-center"><img src="<?= $siswa[$i]['foto_siswa'] ?>" class='object-cover object-center border border-dark' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $siswa[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><span class='<?= $siswa[$i]['jenis_kelamin'] ? 'bg-danger' : 'bg-primary' ?> rounded-pill px-2 text-white'><?= $siswa[$i]['jenis_kelamin'] ? 'Perempuan' : 'Laki-Laki' ?></span></td>
                                    <td class="text-center"><?= $siswa[$i]['nama_kelas'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div>
                                            <div>
                                                <a href="?page=siswa&action=lihat&id=<?= $siswa[$i]['id'] ?>" name='delete' class='btn btn-sm btn-success btn-data-form' title='Lihat Data Siswa'>Lihat</a>
                                            </div>
                                            <div class="mt-2">
                                                <a href="?page=siswa&action=pelanggaran&id=<?= $siswa[$i]['id'] ?>" name='delete' class='btn btn-sm btn-warning btn-data-form' title='Tambah Pelanggaran Siswa'>Tambah</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                }
                            } 
                            else {
                                ?>                                
                                <tr>
                                    <td colspan="9" class='text-center'>Tidak ada siswa</td>
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