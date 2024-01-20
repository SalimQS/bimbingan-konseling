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
                    $query = "DELETE FROM admin WHERE id_admin = '". $_POST['id'] ."'";
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
                    $query = "SELECT * FROM admin ";
                    $query .= "WHERE 1 ";
                    if(isset($_POST['btn-cari'])) {
                        $cari = $_POST['cari'];
                        $query .= "AND (admin.nama_admin LIKE '%$cari%' OR admin.username_admin LIKE '%$cari%') ";
                    }
                    $query .= "ORDER BY admin.nama_admin";
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
                    <div class="col-12 col-md-8 text-right">
                        <a href="?page=user&action=add" class="btn btn-success" title='Tambah Siswa'><i class="fas fa-plus"></i> Tambah</a>
                    </div>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Level Akun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $akun = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($akun); $i++) {
                                    if($akun[$i]['username_admin'] == $_SESSION['username']) {
                                        $nama = $akun[$i]['username_admin'] . ' (Anda)';
                                    }
                                    else $nama = $akun[$i]['username_admin'];
                                    //---
                                    $level = "";
                                    if($akun[$i]['level_admin'] == 'petugas') {
                                        $level = 'Petugas';
                                    }
                                    else if($akun[$i]['level_admin'] == 'bk') {
                                        $level = 'Guru BK';
                                    }
                                    else if($akun[$i]['level_admin'] == 'kepsek') {
                                        $level = 'Kepala Sekolah';
                                    }
                                    else if($akun[$i]['level_admin'] == 'walikelas') {
                                        $level = 'Wali Kelas';
                                    }
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><?= $nama ?></td>
                                    <td class="text-center"><?= $akun[$i]['nama_admin'] ?></td>
                                    <td class="text-center"><?= $level ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div>
                                            <div style="display: inline-block;">
                                                <a href="?page=user&action=edit&id=<?= $akun[$i]['id_admin'] ?>" name='delete' class='btn btn-sm btn-primary btn-data-form' title='Ubah Data Siswa'>Ubah</a>
                                            </div>
                                            <form style="display: inline-block;" method="post" class="formDelete" nama-admin="<?= $nama?>">
                                                <input type="hidden" name="id" value="<?= $akun[$i]['id_admin'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button class='btn btn-sm btn-danger btn-data-form' title='Hapus Akun'>Hapus</button>
                                            </form>
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