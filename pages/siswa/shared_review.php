<?php

$config = array_merge([
    'allow_manage' => false,
], $studentReviewConfig ?? []);

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = null;
$violations = [];
$totalPoints = 0;
$warningSettings = app_warning_settings($connect);
$warningState = null;

if ($studentId > 0) {
    $query = "SELECT kelas.nama_kelas, kelas.id_kelas AS kelas_id, siswa.* FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE siswa.id_siswa = '{$studentId}' LIMIT 1";
    $result = $connect->query($query);
    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
    }

    if ($student !== null) {
        $violationQuery = "SELECT peraturan.jenis_peraturan, peraturan.poin_peraturan, pelanggaran.tanggal_pelanggaran, pelanggaran.tempat_pelanggaran FROM pelanggaran LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan WHERE pelanggaran.id_siswa = '{$studentId}' ORDER BY pelanggaran.tanggal_pelanggaran DESC";
        $violationResult = $connect->query($violationQuery);
        if ($violationResult instanceof mysqli_result) {
            $violations = $violationResult->fetch_all(MYSQLI_ASSOC);
        }

        $totalPoints = app_total_points_from_violations($violations);
        $warningState = app_warning_state_for_points($totalPoints, $warningSettings);
    }
}

$actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=siswa"><i class="fas fa-arrow-left"></i> <span>Kembali</span></a>';
if ($config['allow_manage'] && $student !== null) {
    $actions .= '<a class="btn btn-primary" href="?page=siswa&action=edit&id=' . app_h($studentId) . '"><i class="fas fa-pen"></i> <span>Kelola</span></a>';
}
$actions .= '</div>';

echo app_render_page_intro(
    'Detail Siswa',
    'Lihat identitas siswa, data pelanggaran, dan ringkasan poin tersisa secara ringkas dalam satu halaman.',
    app_source_meta_chips(),
    $actions
);
?>

<?php if ($student === null) : ?>
    <section class="panel-card panel-spaced">
        <div class="empty-state">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Data siswa tidak ditemukan.</strong>
            <span>Periksa kembali tautan yang dibuka atau sinkronisasi data siswa.</span>
        </div>
    </section>
<?php else : ?>
    <?php $photo = trim((string) ($student['foto_siswa'] ?? '')) !== '' ? $student['foto_siswa'] : 'assets/img/default.jpg'; ?>
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <section class="panel-card h-100">
                <div class="student-profile-card">
                    <img src="<?= app_h($photo) ?>" alt="<?= app_h($student['nama_lengkap']) ?>" class="student-profile-photo">
                    <div>
                        <h2><?= app_h($student['nama_lengkap']) ?></h2>
                        <p><?= app_h($student['nama_kelas']) ?> • <?= app_h($student['nisn']) ?></p>
                    </div>
                </div>

                <div class="profile-data-list">
                    <div>
                        <span>NISN</span>
                        <strong><?= app_h($student['nisn']) ?></strong>
                    </div>
                    <div>
                        <span>Nama Ibu</span>
                        <strong><?= app_h($student['nama_ibu']) ?></strong>
                    </div>
                    <div>
                        <span>Jenis Kelamin</span>
                        <strong><?= app_h(app_gender_label($student['jenis_kelamin'])) ?></strong>
                    </div>
                    <div>
                        <span>Tempat, Tanggal Lahir</span>
                        <strong><?= app_h($student['tempat_lahir']) ?>, <?= app_h($student['tanggal_lahir']) ?></strong>
                    </div>
                    <div>
                        <span>Agama</span>
                        <strong><?= app_h(app_religion_label($student['agama'])) ?></strong>
                    </div>
                    <div>
                        <span>Telepon</span>
                        <strong><?= app_h($student['no_telepon']) ?></strong>
                    </div>
                    <div>
                        <span>Alamat</span>
                        <strong><?= app_h($student['alamat']) ?></strong>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <section class="panel-card panel-spaced">
                <div class="section-head">
                    <div>
                        <span class="section-kicker">Riwayat Pelanggaran</span>
                        <h2>Ringkasan poin tersisa</h2>
                    </div>
                    <div class="action-stack">
                        <span class="status-pill badge-points"><?= app_h($totalPoints) ?> poin</span>
                        <?php if ($warningState !== null) : ?>
                            <span class="status-pill <?= app_h(app_warning_state_badge_class($warningState['key'])) ?>">
                                <?= app_h($warningState['label']) ?>
                            </span>
                        <?php else : ?>
                            <span class="status-pill badge-source">Belum SP1</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table app-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggaran</th>
                                <th>Poin</th>
                                <th>Tanggal</th>
                                <th>Tempat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($violations !== []) : ?>
                                <?php foreach ($violations as $index => $violation) : ?>
                                    <tr>
                                        <td><?= app_h($index + 1) ?></td>
                                        <td><?= app_h($violation['jenis_peraturan']) ?></td>
                                        <td>
                                            <span class="status-pill badge-points"><?= app_h($violation['poin_peraturan']) ?> poin</span>
                                        </td>
                                        <td><?= app_h($violation['tanggal_pelanggaran']) ?></td>
                                        <td><?= app_h($violation['tempat_pelanggaran']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state empty-state-compact">
                                            <i class="fas fa-shield-alt"></i>
                                            <strong>Belum ada riwayat pelanggaran.</strong>
                                            <span>Siswa ini belum memiliki catatan pengurangan poin pada sistem.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
<?php endif; ?>
