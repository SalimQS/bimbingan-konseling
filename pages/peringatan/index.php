<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php
                    $query = "SELECT kelas.nama_kelas, siswa.* FROM siswa ";
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
                ?>
                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <form method="post">
                            <div class="input-group">
                                <input type="text" class="form-control" name='cari' placeholder="Cari Peringatan" aria-label="Cari berdasarkan nama" aria-describedby="btn-cari" value="<?= @$_POST['cari'] ?>">
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
                        <table class="table table-bordered table-striped">
                            <thead class="bg-success text-white">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Foto</th>
                                    <th>Nama Lengkap</th>
                                    <th>Kelas</th>
                                    <th>Poin</th>
                                    <th>Penanganan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 0;
                            $result = $connect->query($query);
                            if($result->num_rows > 0) {
                                $list = $result->fetch_all(MYSQLI_ASSOC);
                                for ($i = 0; $i < count($list); $i++) {
                                    $query = "SELECT peraturan.poin_peraturan FROM `pelanggaran` LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan WHERE `id_siswa`='" . $list[$i]['id_siswa'] . "'";
                                    $result = $connect->query($query);
                                    $poincount = 0;
                                    $penanganan = "";
                                    while(($poin = $result->fetch_assoc())) {
                                        $poincount += $poin['poin_peraturan'];
                                    }
                                    //---
                                    if($poincount >= 15 && $poincount < 30) {
                                        $penanganan = "Peringatan Pertama<br>oleh Wali Kelas";
                                    }
                                    else if($poincount >= 30 && $poincount < 50) {
                                        $penanganan = "Peringatan Kedua<br>oleh Wali Kelas";
                                    }
                                    else if($poincount >= 50 && $poincount < 80) {
                                        $penanganan = "Peringatan Ketiga<br>oleh Guru BK";
                                    }
                                    else if($poincount >= 80 && $poincount < 100) {
                                        $penanganan = "Peringatan Keempat<br>oleh Guru BK";
                                    }
                                    else if($poincount >= 100) {
                                        $penanganan = "Siswa Dikeluarkan";
                                    }
                                    //---
                                    $list[$i] = array_merge($list[$i], array("poin" => $poincount));
                                    $list[$i] = array_merge($list[$i], array("penanganan" => $penanganan));
                                    
                                    if($list[$i]['poin'] >= 1) {
                                        $count++;
                            ?>                                
                                <tr>
                                    <td class="text-center"><?= $count ?></td>
                                    <td class="text-center"><?= $list[$i]['nisn'] ?></td>
                                    <td class="text-center"><img src="<?= $list[$i]['foto_siswa'] ?>" class='object-cover object-center' alt="" width="80px" height="80px"></td>
                                    <td class="text-center"><?= $list[$i]['nama_lengkap'] ?></td>
                                    <td class="text-center"><?= $list[$i]['nama_kelas'] ?></td>
                                    <td class="text-center"><span class='bg-danger rounded-pill px-2 text-white'><?= $list[$i]['poin'] ?></span></td>
                                    <td class="text-center"><b><?= $list[$i]['penanganan'] ?></b></td>
                                </tr>
                            <?php
                                    }
                                }
                            } 
                            if($count <= 0){
                                ?>                                
                                <tr>
                                    <td colspan="9" class='text-center'>Tidak ada peringatan</td>
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