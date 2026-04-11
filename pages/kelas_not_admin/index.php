<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php      
                  $query = "SELECT kelas.* FROM kelas ";
                  $query .= "WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND kelas.nama_kelas LIKE '%$cari%' ";
                  }
                  $query .= "ORDER BY kelas.nama_kelas DESC";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Kelas" aria-label="Cari" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
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
                                    <th>Nama Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $kelas = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($kelas); $i++) {
                                    ?>                                
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><b><?= $kelas[$i]['nama_kelas'] ?></b></td>
                                    <td class="text-center">
                                        <a href="?page=kelas&action=detail&id=<?= $kelas[$i]['id_kelas'] ?>" class='btn btn-sm btn-outline-secondary btn-data-form' title='Detail Kelas'>Detail</a>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                ?>                                
                                <tr>
                                    <td colspan="3" class='text-center'>Tidak ada Kelas</td>
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
