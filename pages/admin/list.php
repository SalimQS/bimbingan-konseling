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
                    $query = "DELETE FROM pelanggaran WHERE id = '". $_POST['id'] ."'";
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
                  $query = "SELECT * FROM pelanggaran WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND (jenis LIKE '%$cari%') ";
                  }
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Pelanggaran" aria-label="Cari berdasarkan nama pelanggaran" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
                                <button class="btn btn-primary" type="submit" name='btn-cari' id="btn-cari" title='Cari'><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>           
                    <div class="col-12 col-md-8 text-right">
                        <a href="?page=list&action=add" class="btn btn-primary"  title='Tambah Pelanggaran'>Tambah Pelanggaran</a>
                    </div>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Sanksi</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $list = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($list); $i++) {
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><?= $list[$i]['jenis'] ?></td>
                                    <td class="text-center"><?= $list[$i]['poin'] ?> Poin</td>
                                    <td class="text-center"><?= $list[$i]['created'] ?></td>
                                    <td class="text-center"><?= $list[$i]['updated'] ?></td>
                                    <td class="text-center" style="min-width:10px">
                                        <div class="row">
                                            <form method="post" class="col-md-5 formChangeStatus">
                                                <div>
                                                    <input type="hidden" name="id" value="<?= $list[$i]['id'] ?>"/>
                                                    <input type="hidden" name='change-status'/>
                                                    <a href="?page=list&action=edit&id=<?= $list[$i]['id'] ?>" name='delete' class='btn btn-sm btn-primary' title='Ubah Data Siswa'>
                                                        <i class="fa fa-pencil-alt"></i> Ubah
                                                    </a>
                                                </div>
                                            </form>
                                            <form method="post" class="col-md-6 formDelete">
                                                <input type="hidden" name="id" value="<?= $list[$i]['id'] ?>"/>
                                                <input type="hidden" name='delete'/>
                                                <button class='btn btn-sm btn-danger' title='Hapus Data list'>
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
                                    <td colspan="9" class='text-center'>Tidak ada list</td>
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