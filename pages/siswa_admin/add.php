<?php

$summary = app_student_source_summary();
$successMessage = '';
$errorMessage = '';
$importSummary = null;

if (isset($_POST['upload_students'])) {
    try {
        if (!isset($_FILES['student_workbook'])) {
            throw new RuntimeException('Pilih file Excel siswa terlebih dahulu.');
        }

        $storedWorkbook = app_store_uploaded_student_workbook($_FILES['student_workbook']);

        try {
            $importSummary = app_import_students_from_workbook(
                $connect,
                $storedWorkbook['path'],
                $storedWorkbook['original_name']
            );
        } catch (Throwable $exception) {
            if (is_file($storedWorkbook['path'])) {
                unlink($storedWorkbook['path']);
            }

            throw $exception;
        }

        $summary = app_student_source_summary();
        $successMessage = 'Data siswa berhasil diupload. Biodata dan pembagian kelas sudah disesuaikan dengan file Excel.';
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
    }
}

echo app_render_page_intro(
    'Upload Data Siswa',
    'Upload file Excel siswa untuk memperbarui biodata dan pembagian kelas sesuai isi workbook yang Anda kirim.',
    app_source_meta_chips(),
    '<a class="btn btn-outline-secondary" href="?page=siswa"><i class="fas fa-arrow-left"></i> <span>Kembali ke daftar siswa</span></a>'
);
?>

<?php if ($successMessage !== '') : ?>
    <div class="alert alert-success app-alert" role="alert"><?= app_h($successMessage) ?></div>
<?php endif; ?>
<?php if ($errorMessage !== '') : ?>
    <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorMessage) ?></div>
<?php endif; ?>

<section class="panel-card panel-spaced">
    <div class="section-head">
        <div>
            <span class="section-kicker">Excel Upload</span>
            <h2>Impor ulang data siswa dari workbook</h2>
        </div>
        <span class="status-pill badge-source"><?= app_h($summary['source_file']) ?></span>
    </div>

    <form method="post" enctype="multipart/form-data" class="stack-form">
        <div>
            <label class="toolbar-label" for="student_workbook">File Excel siswa</label>
            <input type="file" id="student_workbook" name="student_workbook" class="form-control" accept=".xlsx" required>
            <small class="text-muted">Format yang didukung hanya `.xlsx`.</small>
        </div>

        <div class="action-stack action-stack-inline">
            <button class="btn btn-primary" type="submit" name="upload_students">
                <i class="fas fa-file-arrow-up"></i>
                <span>Upload dan Sinkronkan</span>
            </button>
        </div>
    </form>
</section>

<section class="panel-card panel-spaced">
    <div class="info-grid">
        <div class="info-card">
            <h3>Format kolom Excel</h3>
            <p>Gunakan kolom yang sama seperti workbook lama: <code>Nama</code>, <code>NISN</code>, <code>Jenis Kelamin</code>, <code>Tempat Lahir</code>, <code>Tgl Lahir</code>, <code>Agama</code>, <code>Alamat</code>, <code>Nomor Tlp</code>, dan <code>Kelas</code>.</p>
        </div>
        <div class="info-card">
            <h3>Perilaku sinkronisasi</h3>
            <p>Siswa baru ditambahkan, biodata siswa lama diperbarui, siswa yang tidak lagi ada di file akan dikeluarkan dari daftar aktif, dan daftar kelas ikut menyesuaikan isi workbook.</p>
        </div>
        <div class="info-card">
            <h3>Data yang dipertahankan</h3>
            <p>Nama ibu kandung dan foto siswa yang sudah dilengkapi di aplikasi tetap dipertahankan selama NISN siswa tetap sama.</p>
        </div>
    </div>
</section>

<section class="panel-card panel-spaced">
    <div class="section-head">
        <div>
            <span class="section-kicker">Status Upload</span>
            <h2>Ringkasan data siswa saat ini</h2>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <h3>File terakhir</h3>
            <code><?= app_h($summary['source_file']) ?></code>
        </div>
        <div class="info-card">
            <h3>Jumlah siswa</h3>
            <p><?= app_h((string) ($summary['student_count'] ?? 0)) ?> siswa aktif</p>
        </div>
        <div class="info-card">
            <h3>Jumlah kelas</h3>
            <p><?= app_h((string) ($summary['class_count'] ?? 0)) ?> kelas aktif</p>
        </div>
        <div class="info-card">
            <h3>Upload terakhir</h3>
            <p><?= !empty($summary['synced_at']) ? app_h(date('d M Y H:i', strtotime($summary['synced_at']))) : 'Belum ada upload' ?></p>
        </div>
        <?php if ($importSummary !== null && !empty($importSummary['source_path'])) : ?>
            <div class="info-card">
                <h3>File tersimpan</h3>
                <code><?= app_h($importSummary['source_path']) ?></code>
            </div>
        <?php endif; ?>
    </div>
</section>
