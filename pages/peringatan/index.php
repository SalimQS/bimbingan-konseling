<?php

$warningSettings = app_warning_settings($connect);
$warningLetterFlash = app_flash_get('warning_letter');
$selectedClass = isset($_GET['kelas']) ? (string) (int) $_GET['kelas'] : '-1';
$search = isset($_GET['cari']) ? trim((string) ($_GET['cari'] ?? '')) : '';
$where = [];
$canManageWarningLetters = app_can_manage_warning_letters($_SESSION);

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

$warningLetters = app_warning_letters_by_student_ids($connect, array_column($warningRows, 'id_siswa'));
$warningLetterModals = [];

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

<?php if (is_array($warningLetterFlash) && ($warningLetterFlash['message'] ?? '') !== '') : ?>
    <?php
    $flashType = (string) ($warningLetterFlash['type'] ?? 'info');
    $flashClass = in_array($flashType, ['success', 'danger', 'warning', 'info'], true) ? $flashType : 'info';
    ?>
    <div class="alert alert-<?= app_h($flashClass) ?> app-alert mt-3" role="alert"><?= app_h((string) $warningLetterFlash['message']) ?></div>
<?php endif; ?>

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
        <form method="get" class="toolbar-search">
            <input type="hidden" name="page" value="peringatan">
            <?php if ($selectedClass !== '' && $selectedClass !== '-1') : ?>
                <input type="hidden" name="kelas" value="<?= app_h($selectedClass) ?>">
            <?php endif; ?>
            <label class="toolbar-label" for="peringatan-cari">Cari siswa</label>
            <div class="input-group">
                <input type="text" class="form-control" id="peringatan-cari" name="cari" placeholder="Cari NISN atau nama siswa" value="<?= app_h($search) ?>">
                <button class="btn btn-primary" type="submit">
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
                        <?php
                        $photo = trim((string) ($row['foto_siswa'] ?? '')) !== '' ? $row['foto_siswa'] : 'assets/img/default.jpg';
                        $studentLetters = $warningLetters[(int) $row['id_siswa']] ?? [];
                        $availableLetterTypes = app_warning_letter_types_for_state((string) ($row['state']['key'] ?? ''));
                        $modalId = 'warningLetterModal' . (int) $row['id_siswa'];
                        ?>
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
                                    <?php if ($canManageWarningLetters) : ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= app_h($modalId) ?>">
                                            <i class="fas fa-file-signature"></i>
                                            <span><?= $studentLetters !== [] ? 'Kelola Surat' : 'Buat Surat' ?></span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($canManageWarningLetters) : ?>
                                    <span class="text-muted small mt-2 d-inline-flex">
                                        <?= $studentLetters !== [] ? app_h(count($studentLetters)) . ' surat tersimpan' : 'Belum ada surat tersimpan' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($canManageWarningLetters) : ?>
                            <?php ob_start(); ?>
                            <div class="modal fade" id="<?= app_h($modalId) ?>" tabindex="-1" aria-labelledby="<?= app_h($modalId) ?>Label" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div>
                                                <h5 class="modal-title mb-1" id="<?= app_h($modalId) ?>Label">Surat untuk <?= app_h($row['nama_lengkap']) ?></h5>
                                                <span class="text-muted small">State saat ini: <?= app_h($row['state']['label']) ?>, total <?= app_h($row['total_poin']) ?> poin</span>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="warning-letter-grid">
                                                <?php foreach ($availableLetterTypes as $letterKey => $letterTemplate) : ?>
                                                    <?php $existingLetter = $studentLetters[$letterKey] ?? null; ?>
                                                    <div class="info-card warning-letter-card">
                                                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                                            <span class="status-pill <?= app_h(app_warning_state_badge_class($letterKey)) ?>"><?= app_h($letterTemplate['label']) ?></span>
                                                            <?php if (is_array($existingLetter)) : ?>
                                                                <span class="status-pill badge-source">Tersimpan</span>
                                                            <?php else : ?>
                                                                <span class="status-pill badge-points">Belum dibuat</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h3><?= app_h($letterTemplate['title']) ?></h3>
                                                        <?php if (is_array($existingLetter)) : ?>
                                                            <p>Nomor surat: <strong><?= app_h((string) ($existingLetter['no_surat'] ?? '-')) ?></strong></p>
                                                            <p>Tanggal surat: <?= app_h((string) ($existingLetter['tanggal_surat'] ?? '-')) ?></p>
                                                            <p>Regenerate akan membangun ulang file dari template terbaru tanpa mengubah nomor dan tanggal surat.</p>
                                                        <?php else : ?>
                                                            <p>Surat akan dibuat dari template asli dengan mengganti nama siswa, nomor surat, dan tanggal sesuai bulan berjalan.</p>
                                                        <?php endif; ?>
                                                        <div class="action-stack mt-3">
                                                            <?php if (is_array($existingLetter)) : ?>
                                                                <a class="btn btn-primary" href="warning_letter.php?action=download&id=<?= app_h((string) ($existingLetter['id_surat'] ?? '0')) ?>">
                                                                    <i class="fas fa-download"></i>
                                                                    <span>Download</span>
                                                                </a>
                                                                <form method="post" action="warning_letter.php" class="d-inline-flex">
                                                                    <input type="hidden" name="action" value="regenerate">
                                                                    <input type="hidden" name="letter_id" value="<?= app_h((string) ($existingLetter['id_surat'] ?? '0')) ?>">
                                                                    <input type="hidden" name="return_kelas" value="<?= app_h($selectedClass) ?>">
                                                                    <input type="hidden" name="return_search" value="<?= app_h($search) ?>">
                                                                    <button class="btn btn-outline-secondary" type="submit">
                                                                        <i class="fas fa-sync-alt"></i>
                                                                        <span>Regenerate</span>
                                                                    </button>
                                                                </form>
                                                            <?php else : ?>
                                                                <form method="post" action="warning_letter.php" class="d-inline-flex">
                                                                    <input type="hidden" name="action" value="create">
                                                                    <input type="hidden" name="student_id" value="<?= app_h((string) $row['id_siswa']) ?>">
                                                                    <input type="hidden" name="letter_type" value="<?= app_h($letterKey) ?>">
                                                                    <input type="hidden" name="return_kelas" value="<?= app_h($selectedClass) ?>">
                                                                    <input type="hidden" name="return_search" value="<?= app_h($search) ?>">
                                                                    <button class="btn btn-outline-primary" type="submit">
                                                                        <i class="fas fa-file-plus"></i>
                                                                        <span>Buat Surat</span>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $warningLetterModals[] = trim((string) ob_get_clean()); ?>
                        <?php endif; ?>
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

<?php if ($warningLetterModals !== []) : ?>
    <?= implode("\n", $warningLetterModals) ?>
<?php endif; ?>
