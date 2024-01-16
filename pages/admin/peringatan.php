<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right"><b>List Peringatan Awal</b> (Poin Diatas 50)</h4>
                </div>
                <?php
                    $query = "SELECT siswa.nisn, kelas.kelas, data.nama_lengkap, data.foto, siswa.id FROM siswa ";
                    $query .= "LEFT JOIN data ON data.id_siswa = siswa.id ";
                    $query .= "LEFT JOIN kelas ON kelas.id = siswa.id_kelas ";
                    $query .= "WHERE 1";
                ?>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Foto</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 0;
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $list = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($list); $i++) {
                                    $query = "SELECT aturan.poin FROM `pelanggaran` LEFT JOIN aturan ON aturan.id = pelanggaran.id_pelanggaran WHERE `id_siswa`='" . $list[$i]['id'] . "'";
                                    $result = $connect->query($query);
                                    $poincount = 0;
                                    while(($poin = $result->fetch_assoc())) {
                                        $poincount += $poin['poin'];
                                    }
                                    $list[$i] = array_merge($list[$i], array("poin" => $poincount));
                                    
                                    if($list[$i]['poin'] >= 50 && $list[$i]['poin'] < 100) {
                                        $count++;
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $count ?></td>
                                    <td class="text-center"><?= $list[$i]['nisn'] ?></td>
                                    <td class="text-center"><img src="<?= $list[$i]['foto'] ?>" class='object-cover object-center' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $list[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><?= $list[$i]['kelas'] ?></td>
                                    <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $list[$i]['poin'] ?></span></td>
                                </tr>
                            <?php
                                    }
                                }
                            } 
                            if($count <= 0){
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
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right"><b>List Peringatan Akhir</b> (Poin Diatas 100)</h4>
                </div>
                <?php
                    $query = "SELECT siswa.nisn, kelas.kelas, data.nama_lengkap, data.foto, siswa.id FROM siswa ";
                    $query .= "LEFT JOIN data ON data.id_siswa = siswa.id ";
                    $query .= "LEFT JOIN kelas ON kelas.id = siswa.id_kelas ";
                    $query .= "WHERE 1";
                ?>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Foto</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 0;
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $list = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($list); $i++) {
                                    $query = "SELECT aturan.poin FROM `pelanggaran` LEFT JOIN aturan ON aturan.id = pelanggaran.id_pelanggaran WHERE `id_siswa`='" . $list[$i]['id'] . "'";
                                    $result = $connect->query($query);
                                    $poincount = 0;
                                    while(($poin = $result->fetch_assoc())) {
                                        $poincount += $poin['poin'];
                                    }
                                    $list[$i] = array_merge($list[$i], array("poin" => $poincount));
                                    
                                    if($list[$i]['poin'] >= 100) {
                                        $count++;
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $count ?></td>
                                    <td class="text-center"><?= $list[$i]['nisn'] ?></td>
                                    <td class="text-center"><img src="<?= $list[$i]['foto'] ?>" class='object-cover object-center' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $list[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><?= $list[$i]['kelas'] ?></td>
                                    <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $list[$i]['poin'] ?></span></td>
                                </tr>
                            <?php
                                    }
                                }
                            } 
                            if($count <= 0){
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