<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php      
                  $query = "SELECT kelas.*, guru.nama_guru FROM kelas ";
                  $query .= "LEFT JOIN guru ON guru.id_guru = kelas.id_wali_kelas ";
                  $query .= "WHERE 1 ";
                  if(isset($_POST['btn-cari'])) {
                    $cari = $_POST['cari'];
                    $query .= "AND kelas.nama_kelas LIKE '%$cari%' OR guru.nama_guru LIKE '%$cari%' ";
                  }
                  $query .= "ORDER BY kelas.nama_kelas DESC";
                ?>
                <div class="row">     
                    <div class="col-12 col-md-4 ">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Kelas (Kelas atau Wali Kelas)" aria-label="Cari" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
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
                                    <th>Nama Wali Kelas</th>
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
                                    <td class="text-center"><?= $kelas[$i]['nama_guru'] ?></td>
                                </tr>
                                <?php
                                }
                            } else {
                                ?>                                
                                <tr>
                                    <td colspan="6" class='text-center'>Tidak ada Kelas</td>
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