<div class="row mt-6">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <form method="post">
                <?php
                $errorAdd = false;
                $errorAddText = "";

                if(isset($_POST['simpan'])) {
                    $errorAdd = true;
                    $nama = $_POST['nama'];
                    $jenis = $_POST['jenis'];

                    if($nama == '0') {
                        $errorAddText = "Nama tidak boleh kosong";
                    } else if($jenis == '0') {
                        $errorAddText = "Pelanggaran tidak boleh kosong";
                    } else {
                        $errorAdd = false;
                        $connect->begin_transaction();
                        
                        try {
                            $query = "INSERT INTO sanksi (id_user, id_pelanggaran) VALUES ('$nama', '$jenis')";
                            $result = $connect->query($query);
                            if($result) {
                            $errorAdd = false;
                            $connect->commit();
                            ?>
                            <script src="vendors/sweetalert/sweetalert.min.js"></script>
                            <script type="text/javascript">
                                Swal.fire({
                                title: "Sukses!",
                                text: "Berhasil menambahkan pelanggar",
                                icon: 'success'
                                }).then(() => {    
                                window.location = "index.php?page=pelanggar";
                                });
                            </script>
                            <?php
                            } 
                            else {
                                $errorAdd = true;
                                $errorAddText = "Gagal menambahkan pelanggar : " . $connect->error;
                            }
                        } catch (exception $e) {
                            print_r($e);
                            $connect->rollback();
                            $errorAdd = true;
                            $errorAddText = $e;
                        }
                    }
                }
                ?>

                <?php if($errorAdd) {?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> <?= $errorAddText ?></div>
                </div>
                <?php } ?>

                <div class="row mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">Tambah List Pelanggaran</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="labels">Nama Pelanggar</label>
                        <select class="form-control" name="nama">
                            <option value="0" selected>Pilih Nama Pelanggar</option>
                            <?php
                                $query = "SELECT siswa.kelas, data.nama_lengkap, users.id FROM users ";
                                $query .= "LEFT JOIN data ON data.id_user = users.id ";
                                $query .= "LEFT JOIN siswa ON siswa.id_user = users.id ";
                                $query .= "WHERE siswa.id IS NOT NULL";
                                //---
                                $result = $connect->query($query);
                                if($result->num_rows > 0) {
                                    $row = $result->fetch_all(MYSQLI_ASSOC);
                                    for($i = 0; $i < count($row); $i++) {
                                        echo("<option value='" . $row[$i]['id'] . "'>" . $row[$i]['kelas'] . " - " . $row[$i]['nama_lengkap'] . "</option>");
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="labels">Jenis Pelanggaran</label>
                        <select class="form-control" name="jenis">
                            <option value="0" selected>Pilih Jenis Pelanggaran</option>
                            <?php
                                $query = "SELECT * FROM pelanggaran WHERE 1";
                                //---
                                $result = $connect->query($query);
                                if($result->num_rows > 0) {
                                    $row = $result->fetch_all(MYSQLI_ASSOC);
                                    for($i = 0; $i < count($row); $i++) {
                                        echo("<option value='" . $row[$i]['id'] . "'>" . $row[$i]['jenis'] . " - " . $row[$i]['poin'] . " Poin</option>");
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 col-md-12 text-right">
                        <button class="btn btn-primary profile-button" type="submit" name='simpan'>Simpan</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <div class="card" style="margin-top: 30px;">
            <div class="card-body">
            <?php
                $errorDelete = false;

                $successStatus = false;
                $successText = "";
                $errorStatus = false;
                $errorText = "";

                if(isset($_POST['delete'])) {
                    $query = "DELETE FROM sanksi WHERE id = '". $_POST['id'] ."'";
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
                    <div><strong>Sukses!</strong> Pelanggar Berhasil <?= $successText ?></div>
                </div>
                <?php } ?>
                <?php if($errorStatus) { ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2" width="24" height="24"></i>
                    <div><strong>Gagal!</strong> Pelanggar gagal <?= $errorText ?></div>
                </div>
                <?php } ?>

                <?php
                    $query = "SELECT pelanggaran.jenis, pelanggaran.poin, data.nama_lengkap, data.foto, siswa.kelas, siswa.nisn, sanksi.created, sanksi.updated, sanksi.id FROM sanksi ";
                    $query .= "LEFT JOIN data ON data.id_user = sanksi.id_user ";
                    $query .= "LEFT JOIN siswa ON siswa.id_user = sanksi.id_user ";
                    $query .= "LEFT JOIN pelanggaran ON pelanggaran.id = sanksi.id_pelanggaran ";
                    $query .= "WHERE siswa.id IS NOT NULL ";
                    if(isset($_POST['btn-cari'])) {
                        $cari = $_POST['cari'];
                        $query .= "AND (pelanggaran.jenis LIKE '%$cari%' OR data.nama_lengkap LIKE '%$cari%') ";
                    }
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Pelanggar" aria-label="Cari berdasarkan nama pelanggaran" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
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
                                    <th>Nama Pelanggar</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Sanksi</th>
                                    <th>Kelas</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Action</th>
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
                                    <td class="text-center"><?= $pelanggar[$i]['nisn'] ?></td>
                                    <td class="text-center"><img src="<?= $pelanggar[$i]['foto'] ?>" class='object-cover object-center' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $pelanggar[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><?= $pelanggar[$i]['jenis'] ?></td>
                                    <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $pelanggar[$i]['poin'] ?> Poin</span></td>
                                    <td class="text-center"><?= $pelanggar[$i]['kelas'] ?></td>
                                    <td class="text-center"><?= $pelanggar[$i]['created'] ?></td>
                                    <td class="text-center"><?= $pelanggar[$i]['updated'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row">
                                            <form method="post" class="formChangeStatus">
                                                <div>
                                                    <input type="hidden" name="id" value="<?= $pelanggar[$i]['id'] ?>"/>
                                                    <input type="hidden" name='change-status'/>
                                                    <a href="?page=pelanggar&action=edit&id=<?= $pelanggar[$i]['id'] ?>" name='delete' class='btn btn-sm btn-primary' title='Ubah Data Siswa'>
                                                        <i class="fa fa-pencil-alt"></i> Ubah
                                                    </a>
                                                </div>
                                            </form>
                                            <form method="post" class="formDelete">
                                                <input type="hidden" name="id" value="<?= $pelanggar[$i]['id'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button class='btn btn-sm btn-danger' title='Hapus Data Pelanggar'>
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
                                    <td colspan="10" class='text-center'>Tidak ada pelanggar</td>
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