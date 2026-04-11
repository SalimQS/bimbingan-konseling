<?php

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = null;
$rules = [];
$warningSettings = app_warning_settings($connect);
$maxPoints = $warningSettings['max_points'];
$currentPoints = 0;
$remainingPoints = $maxPoints;
$warningState = null;
$successMessage = '';
$errorMessage = '';

$studentQuery = "SELECT kelas.nama_kelas, kelas.id_kelas AS kelas_id, siswa.* FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE siswa.id_siswa = '{$studentId}' LIMIT 1";
$studentResult = $connect->query($studentQuery);
if ($studentResult instanceof mysqli_result && $studentResult->num_rows > 0) {
    $student = $studentResult->fetch_assoc();
    $currentPoints = app_student_total_points($connect, $studentId);
    $remainingPoints = app_warning_points_remaining($currentPoints, $maxPoints);
    $warningState = app_warning_state_for_points($currentPoints, $warningSettings);
}

$ruleQuery = 'SELECT * FROM peraturan ORDER BY poin_peraturan DESC, jenis_peraturan ASC';
$ruleResult = $connect->query($ruleQuery);
if ($ruleResult instanceof mysqli_result) {
    $rules = $ruleResult->fetch_all(MYSQLI_ASSOC);
}

$rulesById = [];
foreach ($rules as $rule) {
    $rulesById[(int) $rule['id_peraturan']] = $rule;
}

if (isset($_POST['submit']) && $student !== null) {
    $tanggal = trim((string) ($_POST['tanggal'] ?? ''));
    $tempat = trim((string) ($_POST['tempat'] ?? ''));
    $selectedRuleIds = [];

    if (isset($_POST['checked'], $_POST['id']) && is_array($_POST['checked']) && is_array($_POST['id'])) {
        foreach ($_POST['checked'] as $index => $checkedValue) {
            if (isset($_POST['id'][$index])) {
                $selectedRuleIds[] = (int) $_POST['id'][$index];
            }
        }
    }

    $selectedRuleIds = array_values(array_unique(array_filter($selectedRuleIds)));
    $selectedPoints = 0;

    foreach ($selectedRuleIds as $ruleId) {
        if (isset($rulesById[$ruleId])) {
            $selectedPoints += (int) ($rulesById[$ruleId]['poin_peraturan'] ?? 0);
        }
    }

    if ($tanggal === '') {
        $errorMessage = 'Tanggal pelanggaran wajib diisi.';
    } elseif ($tempat === '') {
        $errorMessage = 'Tempat pelanggaran wajib diisi.';
    } elseif ($selectedRuleIds === [] || $selectedPoints <= 0) {
        $errorMessage = 'Pilih minimal satu pelanggaran.';
    } elseif ($remainingPoints <= 0) {
        $errorMessage = 'Siswa sudah mencapai batas maksimum ' . $maxPoints . ' poin.';
    } elseif (($currentPoints + $selectedPoints) > $maxPoints) {
        $errorMessage = 'Total poin melebihi batas maksimum ' . $maxPoints . '. Sisa poin yang masih bisa ditambahkan hanya ' . $remainingPoints . '.';
    } else {
        $statement = $connect->prepare('INSERT INTO pelanggaran (id_siswa, id_peraturan, tanggal_pelanggaran, tempat_pelanggaran) VALUES (?, ?, ?, ?)');

        if ($statement === false) {
            $errorMessage = 'Query tambah pelanggaran gagal disiapkan.';
        } else {
            $connect->begin_transaction();

            try {
                foreach ($selectedRuleIds as $ruleId) {
                    $statement->bind_param('iiss', $studentId, $ruleId, $tanggal, $tempat);
                    if (!$statement->execute()) {
                        throw new RuntimeException('Gagal menambahkan pelanggaran.');
                    }
                }

                $connect->commit();
                $statement->close();

                $successMessage = 'Pelanggaran berhasil ditambahkan.';
                $currentPoints = app_cap_warning_points($currentPoints + $selectedPoints, $maxPoints);
                $remainingPoints = app_warning_points_remaining($currentPoints, $maxPoints);
                $warningState = app_warning_state_for_points($currentPoints, $warningSettings);
            } catch (Throwable $throwable) {
                $connect->rollback();
                $statement->close();
                $errorMessage = 'Gagal menambahkan pelanggaran.';
            }
        }
    }
}

$actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=siswa"><i class="fas fa-arrow-left"></i> <span>Kembali</span></a></div>';
$chips = app_source_meta_chips();
if ($student !== null) {
    $chips[] = $student['nama_kelas'];
    $chips[] = $currentPoints . '/' . $maxPoints . ' poin';
    $chips[] = $warningState !== null ? $warningState['label'] : 'Belum SP1';
}

echo app_render_page_intro(
    'Tambah Pelanggaran Siswa',
    'Tambahkan catatan pelanggaran baru tanpa melewati batas maksimum poin siswa.',
    $chips,
    $actions
);
?>

<?php if ($successMessage !== '') : ?>
    <div class="alert alert-success app-alert" role="alert"><?= app_h($successMessage) ?></div>
<?php endif; ?>
<?php if ($errorMessage !== '') : ?>
    <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorMessage) ?></div>
<?php endif; ?>

<?php if ($student === null) : ?>
    <section class="panel-card panel-spaced">
        <div class="empty-state">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Data siswa tidak ditemukan.</strong>
            <span>Kembali ke daftar siswa lalu pilih siswa yang ingin dicatat pelanggarannya.</span>
        </div>
    </section>
<?php else : ?>
    <?php $photo = trim((string) ($student['foto_siswa'] ?? '')) !== '' ? $student['foto_siswa'] : 'assets/img/default.jpg'; ?>
    <div class="row g-4 mt-1">
        <div class="col-12 col-xl-4">
            <section class="panel-card h-100">
                <div class="student-profile-card student-profile-card-column">
                    <img src="<?= app_h($photo) ?>" alt="<?= app_h($student['nama_lengkap']) ?>" class="student-profile-photo">
                    <div class="text-center">
                        <h2><?= app_h($student['nama_lengkap']) ?></h2>
                        <p><?= app_h($student['nama_kelas']) ?> • <?= app_h($student['nisn']) ?></p>
                    </div>
                </div>

                <div class="profile-data-list">
                    <div>
                        <span>Total poin saat ini</span>
                        <strong><?= app_h($currentPoints) ?> dari <?= app_h($maxPoints) ?> poin</strong>
                    </div>
                    <div>
                        <span>Sisa poin yang dapat ditambahkan</span>
                        <strong><?= app_h($remainingPoints) ?> poin</strong>
                    </div>
                    <div>
                        <span>State aktif</span>
                        <strong><?= app_h($warningState['title'] ?? 'Belum masuk SP1') ?></strong>
                    </div>
                    <div>
                        <span>Jenis kelamin</span>
                        <strong><?= app_h(app_gender_label($student['jenis_kelamin'])) ?></strong>
                    </div>
                    <div>
                        <span>Agama</span>
                        <strong><?= app_h(app_religion_label($student['agama'])) ?></strong>
                    </div>
                    <div>
                        <span>Tempat, tanggal lahir</span>
                        <strong><?= app_h($student['tempat_lahir']) ?>, <?= app_h($student['tanggal_lahir']) ?></strong>
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
                        <span class="section-kicker">Input Pelanggaran</span>
                        <h2>Pilih pelanggaran siswa</h2>
                    </div>
                    <?php if ($warningState !== null) : ?>
                        <span class="status-pill <?= app_h(app_warning_state_badge_class($warningState['key'])) ?>"><?= app_h($warningState['label']) ?></span>
                    <?php else : ?>
                        <span class="status-pill badge-source">Belum SP1</span>
                    <?php endif; ?>
                </div>

                <form method="post" class="stack-form mt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="toolbar-label" for="pelanggaran-tanggal">Tanggal pelanggaran</label>
                            <input id="pelanggaran-tanggal" type="date" class="form-control" name="tanggal" value="<?= app_h($_POST['tanggal'] ?? '') ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="toolbar-label" for="pelanggaran-tempat">Tempat pelanggaran</label>
                            <input id="pelanggaran-tempat" type="text" class="form-control" name="tempat" value="<?= app_h($_POST['tempat'] ?? '') ?>" placeholder="Contoh: Ruang kelas" required>
                        </div>
                    </div>

                    <div class="info-card mt-3">
                        <h3>Aturan batas poin</h3>
                        <p>Total poin setelah submit tidak boleh melebihi <?= app_h($maxPoints) ?> poin. Sisa poin siswa saat ini: <?= app_h($remainingPoints) ?> poin.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table app-table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Tambah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rules !== []) : ?>
                                    <?php foreach ($rules as $index => $rule) : ?>
                                        <?php
                                        $ruleId = (int) $rule['id_peraturan'];
                                        $rulePoints = (int) ($rule['poin_peraturan'] ?? 0);
                                        $isDisabled = $remainingPoints <= 0 || $rulePoints > $remainingPoints;
                                        ?>
                                        <tr>
                                            <td><?= app_h($index + 1) ?></td>
                                            <td><?= app_h($rule['jenis_peraturan']) ?></td>
                                            <td><span class="status-pill badge-points"><?= app_h($rulePoints) ?> poin</span></td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="checked[<?= app_h($index + 1) ?>]"
                                                    <?= $isDisabled ? 'disabled' : '' ?>
                                                >
                                                <input type="hidden" name="id[<?= app_h($index + 1) ?>]" value="<?= app_h($ruleId) ?>">
                                                <?php if ($isDisabled) : ?>
                                                    <div class="text-muted small mt-2">Melebihi sisa poin</div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state empty-state-compact">
                                                <i class="fas fa-book-open"></i>
                                                <strong>Belum ada peraturan.</strong>
                                                <span>Tambahkan daftar pelanggaran terlebih dahulu dari menu peraturan.</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="action-stack">
                        <button class="btn btn-primary" type="submit" name="submit" <?= $remainingPoints <= 0 ? 'disabled' : '' ?>>
                            <i class="fas fa-save"></i>
                            <span>Tambah</span>
                        </button>
                        <a class="btn btn-outline-secondary" href="?page=siswa&action=lihat&id=<?= app_h($studentId) ?>">
                            <i class="fas fa-eye"></i>
                            <span>Lihat Detail</span>
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
<?php endif; ?>
