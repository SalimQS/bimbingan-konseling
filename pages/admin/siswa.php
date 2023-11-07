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
                if(isset($_POST['change-status'])) {
                    $status = $_POST['status'] ? 0 : 1;
                    $query = "UPDATE data SET status = '$status'  WHERE id_user = '". $_POST['id'] ."'";
                    $result = $connect->query($query);
                    if($result) {
                        $successStatus = true;
                        $successText = $_POST['status'] ? "dinonaktifkan" : "diaktifkan";
                    } else {
                        $errorStatus = true;
                        $errorText = $_POST['status'] ? "dinonaktifkan" : "diaktifkan";
                    }
                }

                if(isset($_POST['delete'])) {
                    $query = "DELETE FROM users WHERE id = '". $_POST['id'] ."'";
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
                  $query = "SELECT users.id, users.username, data.nama_lengkap, data.foto, data.jenis_kelamin, data.status, siswa.kelas, siswa.nisn FROM users ";
                  $query .= "LEFT JOIN data ON data.id_user = users.id ";
                  $query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
                  $query .= "WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND (users.username LIKE '%$cari%' OR data.nama_lengkap LIKE '%$cari%') ";
                  }
                  $query .= "ORDER BY data.nama_lengkap";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Nama" aria-label="Cari berdasarkan nama" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>           
                    <div class="col-12 col-md-8 text-right">
                        <a href="?page=siswa&action=add" class="btn btn-primary"  title='Tambah Siswa'>Tambah Siswa</a>
                    </div>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Foto</th>
                                    <th>Nickname</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
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
                                    <td class="text-center"><img src="<?= $siswa[$i]['foto'] ?>" class='object-cover object-center' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $siswa[$i]['username'] ?></td>
                                    <td class="text-center"><?= $siswa[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><span class='<?= $siswa[$i]['jenis_kelamin'] ? 'bg-danger' : 'bg-primary' ?> rounded-pill px-2 text-white'><?= $siswa[$i]['jenis_kelamin'] ? 'Perempuan' : 'Laki-Laki' ?></span></td>
                                    <td class="text-center"><?= $siswa[$i]['kelas'] ?></td>
                                    <td class="text-center"><?= $siswa[$i]['status'] ? 'Aktif' : 'Tidak Aktif' ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row">
                                            <form method="post" class="col-12 col-md-8 formChangeStatus" nama-siswa="<?= $nama?>" status="<?= $siswa[$i]['status'] ?>">
                                                <div class="row">
                                                    <input type="hidden" name="id" value="<?= $siswa[$i]['id'] ?>"/>
                                                    <input type="hidden" name="status" value="<?= $siswa[$i]['status'] ?>"/>
                                                    <input type="hidden" name='change-status'/>
                                                    <div class="col-12 col-md-6">
                                                        <a href="?page=siswa&action=edit&id=<?= $siswa[$i]['id'] ?>"name='delete' class='btn btn-sm btn-primary' title='Ubah Data Siswa'>
                                                            <i class="fa fa-pencil-alt"></i> Ubah
                                                        </a>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <?php
                                                            if($siswa[$i]['status']) {
                                                        ?>
                                                        <button class='btn btn-sm btn-warning text-white' title='Nonaktifkan Siswa'>
                                                            <i class="fa fa-times"></i> Non-aktif
                                                        </button>
                                                        <?php
                                                            } else {
                                                        ?>
                                                        <button class='btn btn-sm btn-success text-white' title='Aktifkan Siswa'>
                                                            <i class="fa fa-check"></i> Aktifkan
                                                        </button>
                                                        <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </form>
                                            <form method="post" class="col-12 col-md-4 formDelete" nama-siswa="<?= $nama?>">
                                                <input type="hidden" name="id" value="<?= $siswa[$i]['id'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button class='btn btn-sm btn-danger' title='Hapus Datasiswa'>
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
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