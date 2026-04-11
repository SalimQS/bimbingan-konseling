<?php

$classId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$classroom = null;
$students = [];

if ($classId > 0) {
    $classQuery = "SELECT kelas.id_kelas, kelas.nama_kelas, COUNT(siswa.id_siswa) AS total_siswa FROM kelas LEFT JOIN siswa ON siswa.id_kelas = kelas.id_kelas WHERE kelas.id_kelas = '{$classId}' GROUP BY kelas.id_kelas, kelas.nama_kelas LIMIT 1";
    $classResult = $connect->query($classQuery);
    if ($classResult instanceof mysqli_result && $classResult->num_rows > 0) {
        $classroom = $classResult->fetch_assoc();
    }

    if ($classroom !== null) {
        $studentQuery = "SELECT siswa.id_siswa, siswa.nisn, siswa.nama_lengkap, siswa.foto_siswa, siswa.jenis_kelamin, siswa.tempat_lahir, siswa.tanggal_lahir FROM siswa WHERE siswa.id_kelas = '{$classId}' ORDER BY siswa.nama_lengkap";
        $studentResult = $connect->query($studentQuery);
        $students = $studentResult instanceof mysqli_result ? $studentResult->fetch_all(MYSQLI_ASSOC) : [];
    }
}

$actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=kelas"><i class="fas fa-arrow-left"></i> <span>Kembali ke daftar kelas</span></a></div>';
$chips = app_source_meta_chips();
if ($classroom !== null) {
    $chips[] = $classroom['nama_kelas'];
    $chips[] = $classroom['total_siswa'] . ' siswa';
}

echo app_render_page_intro(
    'Detail Kelas',
    'Lihat daftar murid aktif dalam satu kelas beserta akses cepat ke detail siswa.',
    $chips,
    $actions
);
?>

<?php if ($classroom === null) : ?>
    <section class="panel-card panel-spaced">
        <div class="empty-state">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Data kelas tidak ditemukan.</strong>
            <span>Kembali ke daftar kelas lalu pilih kelas yang ingin dibuka.</span>
        </div>
    </section>
<?php else : ?>
    <section class="panel-card panel-spaced">
        <div class="section-head">
            <div>
                <span class="section-kicker">Ruang Kelas</span>
                <h2><?= app_h($classroom['nama_kelas']) ?></h2>
            </div>
            <span class="status-pill badge-source"><?= app_h($classroom['total_siswa']) ?> siswa</span>
        </div>

        <div class="table-responsive">
            <table class="table app-table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Murid</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students !== []) : ?>
                        <?php foreach ($students as $index => $student) : ?>
                            <?php $photo = trim((string) ($student['foto_siswa'] ?? '')) !== '' ? $student['foto_siswa'] : 'assets/img/default.jpg'; ?>
                            <tr>
                                <td><?= app_h($index + 1) ?></td>
                                <td><span class="text-strong"><?= app_h($student['nisn']) ?></span></td>
                                <td>
                                    <div class="student-cell">
                                        <img src="<?= app_h($photo) ?>" class="student-avatar" alt="<?= app_h($student['nama_lengkap']) ?>">
                                        <div>
                                            <strong><?= app_h($student['nama_lengkap']) ?></strong>
                                            <span><?= app_h($student['tempat_lahir']) ?><?= !empty($student['tanggal_lahir']) ? ', ' . app_h($student['tanggal_lahir']) : '' ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill <?= app_h(app_gender_badge_class($student['jenis_kelamin'])) ?>">
                                        <?= app_h(app_gender_label($student['jenis_kelamin'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-stack">
                                        <a href="?page=siswa&action=lihat&id=<?= app_h($student['id_siswa']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                            <span>Detail</span>
                                        </a>
                                        <?php if (app_can_manage_students($_SESSION)) : ?>
                                            <a href="?page=siswa&action=pelanggaran&id=<?= app_h($student['id_siswa']) ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-plus"></i>
                                                <span>Pelanggaran</span>
                                            </a>
                                            <a href="?page=siswa&action=edit&id=<?= app_h($student['id_siswa']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-pen"></i>
                                                <span>Kelola</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <strong>Belum ada murid di kelas ini.</strong>
                                    <span>Upload data siswa terbaru jika kelas ini seharusnya sudah memiliki murid.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
