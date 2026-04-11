<?php

$config = array_merge([
    'title' => 'Data Siswa',
    'description' => 'Roster siswa aktif diambil dari file sumber dan siap dipakai untuk pencarian, detail, dan pencatatan pelanggaran.',
    'restrict_class_name' => null,
    'show_class_filter' => true,
    'allow_pelanggaran' => false,
    'allow_manage' => false,
    'source_action_url' => null,
], $studentListConfig ?? []);

$search = isset($_POST['btn-cari']) ? trim((string) ($_POST['cari'] ?? '')) : '';
$selectedClass = isset($_GET['kelas']) ? trim((string) $_GET['kelas']) : '';
$where = [];

if ($config['restrict_class_name'] !== null && $config['restrict_class_name'] !== '') {
    $safeClassName = $connect->real_escape_string((string) $config['restrict_class_name']);
    $where[] = "kelas.nama_kelas = '{$safeClassName}'";
}

if ($selectedClass !== '' && $selectedClass !== '-1') {
    $safeClassId = (int) $selectedClass;
    $where[] = "kelas.id_kelas = '{$safeClassId}'";
}

if ($search !== '') {
    $safeSearch = $connect->real_escape_string($search);
    $where[] = "(siswa.nisn LIKE '%{$safeSearch}%' OR siswa.nama_lengkap LIKE '%{$safeSearch}%')";
}

$query = 'SELECT siswa.*, siswa.id_siswa AS id, kelas.nama_kelas FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas';
if ($where !== []) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY kelas.nama_kelas, siswa.nama_lengkap';

$result = $connect->query($query);
$students = $result instanceof mysqli_result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$classOptions = $config['show_class_filter']
    ? app_fetch_class_filter_options($connect, $config['restrict_class_name'])
    : [];

$actionHtml = '';
if (!empty($config['source_action_url'])) {
    $actionHtml = '<a class="btn btn-outline-primary" href="' . app_h($config['source_action_url']) . '"><i class="fas fa-file-excel"></i> Lihat Sumber Data</a>';
}

echo app_render_page_intro($config['title'], $config['description'], app_source_meta_chips(), $actionHtml);
?>

<section class="panel-card panel-spaced">
    <div class="source-note">
        <div>
            <strong>Roster siswa dikelola dari file sumber.</strong>
            <p>Perubahan identitas siswa mengikuti file Excel aktif. Data pendukung seperti foto dan nama ibu dapat dilengkapi pada halaman kelola.</p>
        </div>
        <code><?= app_h(app_student_source_summary()['source_file']) ?></code>
    </div>

    <div class="toolbar-card">
        <form method="post" class="toolbar-search">
            <label class="toolbar-label" for="cari-siswa">Cari siswa</label>
            <div class="input-group">
                <input type="text" class="form-control" id="cari-siswa" name="cari" placeholder="Cari berdasarkan NISN atau nama lengkap" value="<?= app_h($search) ?>">
                <button class="btn btn-primary" type="submit" name="btn-cari">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
            </div>
        </form>

        <?php if ($config['show_class_filter']) : ?>
            <div class="toolbar-filter">
                <label class="toolbar-label" for="kelasFilter">Filter kelas</label>
                <select class="form-select" name="kelas" id="kelasFilter">
                    <option value="-1">Semua kelas</option>
                    <?php foreach ($classOptions as $classOption) : ?>
                        <option value="<?= app_h($classOption['id']) ?>" <?= $selectedClass === (string) $classOption['id'] ? 'selected' : '' ?>>
                            <?= app_h($classOption['kelas']) ?> (<?= app_h($classOption['total_siswa']) ?> siswa)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table app-table align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NISN</th>
                    <th>Siswa</th>
                    <th>Jenis Kelamin</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students !== []) : ?>
                    <?php foreach ($students as $index => $student) : ?>
                        <?php $photo = trim((string) ($student['foto_siswa'] ?? '')) !== '' ? $student['foto_siswa'] : 'assets/img/default.jpg'; ?>
                        <tr>
                            <td><?= app_h($index + 1) ?></td>
                            <td>
                                <span class="text-strong"><?= app_h($student['nisn']) ?></span>
                            </td>
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
                            <td><?= app_h($student['nama_kelas']) ?></td>
                            <td>
                                <div class="action-stack">
                                    <a href="?page=siswa&action=lihat&id=<?= app_h($student['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <?php if ($config['allow_pelanggaran']) : ?>
                                        <a href="?page=siswa&action=pelanggaran&id=<?= app_h($student['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-plus"></i>
                                            <span>Pelanggaran</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($config['allow_manage']) : ?>
                                        <a href="?page=siswa&action=edit&id=<?= app_h($student['id']) ?>" class="btn btn-sm btn-outline-primary">
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
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <strong>Tidak ada siswa ditemukan.</strong>
                                <span>Coba ubah kata kunci pencarian atau filter kelas.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
