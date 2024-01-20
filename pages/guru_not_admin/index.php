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
                    $query = "DELETE FROM guru WHERE id = '". $_POST['id'] ."'";
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
                    <div><strong>Sukses!</strong> Guru Berhasil <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Guru gagal <?= $errorText ?></div>
                </div>
                <?php } ?>
                <?php
                  $query = "SELECT guru.*, kelas.nama_kelas FROM guru ";
                  $query .= "LEFT JOIN kelas ON kelas.id_wali_kelas = guru.id_guru ";
                  $query .= "WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND guru.nama_guru LIKE '%$cari%' OR (guru.nuptk LIKE '%$cari%') ";
                  }
                  $query .= "ORDER BY guru.nama_guru";
                ?>
                <div class="row">
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Nama (Nama atau Pengenal)" aria-label="Cari berdasarkan nama" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>NUPTK</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>No Telepon</th>
                                    <th>Wali Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $guru = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($guru); $i++) {
                                    $nama = $guru[$i]['nama_guru'];
                            ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><img src="<?= $guru[$i]['foto_guru'] ?>" class='object-cover object-center border border-dark' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><b><?= $guru[$i]['nuptk'] ? ($guru[$i]['nuptk']) : ('Empty') ?></b></td>
                                    <td class="text-center"><?= $guru[$i]['nama_guru'] ?></td>
                                    <td class="text-center"><span class='<?= $guru[$i]['jenis_kelamin'] ? 'bg-danger' : 'bg-primary' ?> rounded-pill px-2 text-white'><?= $guru[$i]['jenis_kelamin'] ? 'Perempuan' : 'Laki-Laki' ?></span></td>
                                    <td class="text-center"><?= $guru[$i]['no_telepon'] ?></td>
                                    <td class="text-center"><b><?= $guru[$i]['nama_kelas'] ? $guru[$i]['nama_kelas'] : 'Bukan<br>Wali Kelas' ?></b></td>
                                </tr>
                            <?php
                                }
                            } 
                            else {
                                ?>                                
                                <tr>
                                    <td colspan="9" class='text-center'>Tidak ada data Guru</td>
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