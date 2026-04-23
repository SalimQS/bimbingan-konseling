<?php

$warningSettings = app_warning_settings($connect);
$thresholdValues = $warningSettings['thresholds'];
$successMessage = '';
$errorMessage = '';

if (isset($_POST['simpan'])) {
    $thresholdValues = app_parse_warning_thresholds($_POST);
    $errorMessage = app_validate_warning_thresholds($thresholdValues);

    if ($errorMessage === '') {
        if (app_save_warning_settings($connect, $thresholdValues)) {
            $successMessage = 'Pengaturan state peringatan berhasil diperbarui.';
            $warningSettings = app_warning_settings($connect);
            $thresholdValues = $warningSettings['thresholds'];
        } else {
            $errorMessage = 'Pengaturan state peringatan gagal disimpan.';
        }
    }
}

$statePreview = app_warning_states($thresholdValues);
$chips = app_source_meta_chips();
$chips[] = 'Poin awal ' . $warningSettings['max_points'];
$actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=peringatan"><i class="fas fa-arrow-left"></i> <span>Kembali ke peringatan</span></a></div>';

echo app_render_page_intro(
    'Pengaturan Peringatan',
    'Atur batas poin tersisa untuk memicu SP1, SP2, SP3, dan pemberhentian. Urutan state harus selalu menurun.',
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

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <section class="panel-card panel-spaced">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Threshold State</span>
                    <h2>Form pengaturan poin</h2>
                </div>
                <span class="status-pill badge-points">Awal <?= app_h($warningSettings['max_points']) ?> poin</span>
            </div>

            <form method="post" class="stack-form mt-4">
                <div class="row g-3">
                    <?php foreach ($statePreview as $state) : ?>
                        <div class="col-12 col-md-6">
                            <div class="info-card h-100">
                                <span class="status-pill <?= app_h(app_warning_state_badge_class($state['key'])) ?>"><?= app_h($state['label']) ?></span>
                                <h3><?= app_h($state['title']) ?></h3>
                                <p><?= app_h($state['description']) ?></p>
                                <label class="toolbar-label mt-3" for="threshold-<?= app_h($state['key']) ?>">Aktif saat poin tersisa maksimal</label>
                                <input
                                    type="number"
                                    class="form-control mt-2"
                                    id="threshold-<?= app_h($state['key']) ?>"
                                    name="<?= app_h($state['key']) ?>"
                                    value="<?= app_h($thresholdValues[$state['key']] ?? '') ?>"
                                    min="1"
                                    max="<?= app_h($warningSettings['max_points']) ?>"
                                    required
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="info-card mt-4">
                    <h3>Aturan validasi</h3>
                    <p>Nilai `SP1` harus lebih besar dari `SP2`, `SP2` lebih besar dari `SP3`, dan `SP3` lebih besar dari `Pemberhentian`. Poin siswa dimulai dari 200 lalu berkurang sampai batas bawah 1 poin.</p>
                </div>

                <div class="action-stack mt-4">
                    <button class="btn btn-primary" type="submit" name="simpan">
                        <i class="fas fa-save"></i>
                        <span>Simpan Setting</span>
                    </button>
                    <a class="btn btn-outline-secondary" href="?page=peringatan">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </section>
    </div>

    <div class="col-12 col-xl-5">
        <section class="panel-card panel-spaced">
            <div class="section-head">
                <div>
                    <span class="section-kicker">Preview</span>
                    <h2>Urutan state aktif</h2>
                </div>
            </div>

            <div class="profile-data-list mt-4">
                <?php foreach ($statePreview as $state) : ?>
                    <div>
                        <span><?= app_h($state['description']) ?></span>
                        <strong><?= app_h($state['label']) ?> aktif saat poin tersisa <= <?= app_h($state['min_points']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div>
                    <span>Batas sistem</span>
                    <strong>Poin siswa dimulai dari <?= app_h($warningSettings['max_points']) ?> dan tidak turun di bawah <?= app_h(app_warning_min_points()) ?></strong>
                </div>
            </div>
        </section>
    </div>
</div>
