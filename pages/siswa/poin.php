<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <?php
                $query = "SELECT data.nama_lengkap, data.foto FROM users ";
                $query .= "LEFT JOIN data ON data.id_user = users.id ";
                $query .= "WHERE users.id='" . $_SESSION['id_user'] . "'";
                $result = $connect->query($query);
                $user = $result->fetch_assoc();
            ?>
            <div class="card-body">
                <h4 class="text-left">Statistik Poin Anda</h4>
                <table class="table">
                    <tr style="border: transparent;">
                        <td class="pb-3">
                            <div class="card text-center mt-3">
                                <div class="card-header"><?= $user['nama_lengkap'] ?></div>
                                <div class="card-body">
                                    <img src="<?= $user['foto'] ?>" class='object-cover object-center' alt="" width="150px" height="150px">
                                </div>
                                <div class="card-footer text-body-secondary">
                                    <?php
                                        $query = "SELECT pelanggaran.poin FROM `sanksi` ";
                                        $query .= "LEFT JOIN pelanggaran ON pelanggaran.id = sanksi.id_pelanggaran ";
                                        $query .= "WHERE `id_user`='" . $_SESSION['id_user'] . "'";

                                        $result = $connect->query($query);
                                        $poincount = 0;
                                        while(($poin = $result->fetch_assoc())) {
                                            $poincount += $poin['poin'];
                                        }
                                    ?>
                                    <span class='bg-danger rounded-pill px-2 text-white'><?= $poincount ?> Poin</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <h5 class="text-left">List Pelanggaran Anda:</h5>
                            <table class="table table-bordered table-striped">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <td>No</td>
                                        <td>Jenis Peringatan</td>
                                        <td>Jumlah Poin</td>
                                        <td>Waktu Pelanggaran</td>
                                    </tr>
                                </thead>
                                <?php
                                    $query = "SELECT pelanggaran.jenis, pelanggaran.poin, sanksi.created FROM `sanksi` ";
                                    $query .= "LEFT JOIN pelanggaran ON pelanggaran.id = sanksi.id_pelanggaran ";
                                    $query .= "WHERE `id_user`='" . $_SESSION['id_user'] . "'";
                                    
                                    $result = $connect->query($query);
                                    if($result->num_rows > 0) {
                                        $sanksi = $result->fetch_all(MYSQLI_ASSOC);
                                        for ($i = 0; $i < count($sanksi); $i++) {
                                ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= $sanksi[$i]['jenis'] ?></td>
                                        <td><?= $sanksi[$i]['poin'] ?></td>
                                        <td><?= $sanksi[$i]['created'] ?></td>
                                    </tr>
                                <?php
                                        }
                                    }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>