<?php

$warningSettings = app_warning_settings($connect);
$selectedClass = isset($_GET['kelas']) ? (string) (int) $_GET['kelas'] : '-1';
$search = isset($_POST['btn-cari']) ? trim((string) ($_POST['cari'] ?? '')) : '';
$where = [];

if ($selectedClass !== '' && $selectedClass !== '-1') {
    $where[] = "kelas.id_kelas = '" . (int) $selectedClass . "'";
}

if ($search !== '') {
    $safeSearch = $connect->real_escape_string($search);
    $where[] = "(siswa.nisn LIKE '%{$safeSearch}%' OR siswa.nama_lengkap LIKE '%{$safeSearch}%')";
}

$query = 'SELECT siswa.id_siswa, siswa.nisn, siswa.nama_lengkap, siswa.foto_siswa, kelas.nama_kelas, COALESCE(SUM(peraturan.poin_peraturan), 0) AS total_poin
    FROM siswa
    LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas
    LEFT JOIN pelanggaran ON pelanggaran.id_siswa = siswa.id_siswa
    LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan';
if ($where !== []) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' GROUP BY siswa.id_siswa, siswa.nisn, siswa.nama_lengkap, siswa.foto_siswa, kelas.nama_kelas ORDER BY total_poin DESC, siswa.nama_lengkap ASC';

$warningResult = $connect->query($query);
$warningRows = [];
$stateCounts = [];
foreach ($warningSettings['states'] as $state) {
    $stateCounts[$state['key']] = 0;
}

if ($warningResult instanceof mysqli_result) {
    while ($row = $warningResult->fetch_assoc()) {
        $row['total_poin'] = app_cap_warning_points((int) ($row['total_poin'] ?? 0), $warningSettings['max_points']);
        $row['state'] = app_warning_state_for_points((int) $row['total_poin'], $warningSettings);

        if ($row['state'] === null) {
            continue;
        }

        $stateCounts[$row['state']['key']]++;
        $warningRows[] = $row;
    }
}

$classOptions = app_fetch_class_filter_options($connect);
$chips = app_source_meta_chips();
$chips[] = 'Maksimal ' . $warningSettings['max_points'] . ' poin';
foreach ($warningSettings['states'] as $state) {
    $chips[] = $state['label'] . ' mulai ' . $state['min_points'] . ' poin';
}

$actions = '';
if (app_can_manage_warning_settings($_SESSION)) {
    $actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=peringatan&action=setting"><i class="fas fa-sliders-h"></i> <span>Setting State</span></a></div>';
}

echo app_render_page_intro(
    'Peringatan',
    'Pantau siswa yang sudah memasuki state SP1, SP2, SP3, atau pemberhentian berdasarkan akumulasi poin pelanggaran.',
    $chips,
    $actions
);
?>

<div class="row g-3 mt-1">
    <?php foreach ($warningSettings['states'] as $state) : ?>
        <div class="col-12 col-md-6 col-xl-3">
            <?= app_render_stat_card($state['label'], $stateCounts[$state['key']] ?? 0, $state['icon'], $state['tone'], 'Mulai ' . $state['min_points'] . ' poin') ?>
        </div>
    <?php endforeach; ?>
</div>

<section class="panel-card panel-spaced">
    <div class="section-head">
        <div>
            <span class="section-kicker">Monitoring State</span>
            <h2>Daftar siswa dalam peringatan</h2>
        </div>
        <span class="status-pill badge-points"><?= app_h(count($warningRows)) ?> siswa</span>
    </div>

    <div class="toolbar-card">
        <form method="post" class="toolbar-search">
            <label class="toolbar-label" for="peringatan-cari">Cari siswa</label>
            <div class="input-group">
                <input type="text" class="form-control" id="peringatan-cari" name="cari" placeholder="Cari NISN atau nama siswa" value="<?= app_h($search) ?>">
                <button class="btn btn-primary" type="submit" name="btn-cari">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
            </div>
        </form>

        <div class="toolbar-filter">
            <label class="toolbar-label" for="kelasFilter">Filter kelas</label>
            <select class="form-select" id="kelasFilter" name="kelas">
                <option value="-1">Semua kelas</option>
                <?php foreach ($classOptions as $classOption) : ?>
                    <option value="<?= app_h($classOption['id']) ?>" <?= $selectedClass === (string) $classOption['id'] ? 'selected' : '' ?>>
                        <?= app_h($classOption['kelas']) ?> (<?= app_h($classOption['total_siswa']) ?> siswa)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table app-table align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Poin</th>
                    <th>State</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($warningRows !== []) : ?>
                    <?php foreach ($warningRows as $index => $row) : ?>
                        <?php $photo = trim((string) ($row['foto_siswa'] ?? '')) !== '' ? $row['foto_siswa'] : 'assets/img/default.jpg'; ?>
                        <tr>
                            <td><?= app_h($index + 1) ?></td>
                            <td><span class="text-strong"><?= app_h($row['nisn']) ?></span></td>
                            <td>
                                <div class="student-cell">
                                    <img src="<?= app_h($photo) ?>" class="student-avatar" alt="<?= app_h($row['nama_lengkap']) ?>">
                                    <div>
                                        <strong><?= app_h($row['nama_lengkap']) ?></strong>
                                        <span><?= app_h($row['state']['title']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= app_h($row['nama_kelas']) ?></td>
                            <td><span class="status-pill badge-points"><?= app_h($row['total_poin']) ?> poin</span></td>
                            <td>
                                <div class="d-grid gap-2">
                                    <span class="status-pill <?= app_h(app_warning_state_badge_class($row['state']['key'])) ?>">
                                        <?= app_h($row['state']['label']) ?>
                                    </span>
                                    <span class="text-muted small"><?= app_h($row['state']['description']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="action-stack">
                                    <a href="?page=siswa&action=lihat&id=<?= app_h($row['id_siswa']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <?php if (app_can_manage_students($_SESSION)) : ?>
                                        <a href="?page=siswa&action=pelanggaran&id=<?= app_h($row['id_siswa']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-plus"></i>
                                            <span>Pelanggaran</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-shield-alt"></i>
                                <strong>Belum ada siswa yang masuk state peringatan.</strong>
                                <span>Siswa akan muncul di sini saat total poinnya sudah mencapai batas minimal SP1.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
