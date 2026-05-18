<?php

$successMessage = '';
$errorMessage = '';
$importSummary = null;

if (isset($_POST['upload_peraturan'])) {
    try {
        if (!isset($_FILES['peraturan_workbook'])) {
            throw new RuntimeException('Pilih file Excel peraturan terlebih dahulu.');
        }

        $storedWorkbook = app_store_uploaded_peraturan_workbook($_FILES['peraturan_workbook']);
        $importSummary = app_import_peraturan_from_workbook(
            $connect,
            $storedWorkbook['path'],
            $storedWorkbook['original_name']
        );

        $successMessage = 'Data peraturan berhasil diupload. Peraturan baru ditambahkan dan nilai poin peraturan diperbarui sesuai file.';
    } catch (Throwable $exception) {
        $errorMessage = $exception->getMessage();
    }
}
?>

<div class="row">
    <div class="col-12 col-md-6">
        <a class="btn btn-primary" href="?page=peraturan"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row justify-content-md-center mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Upload Excel Peraturan</h4>

                <?php if ($successMessage !== '') : ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle bi flex-shrink-0 me-2"></i>
                        <div>
                            <strong>Sukses!</strong> <?= app_h($successMessage) ?>
                            <?php if (is_array($importSummary['summary'] ?? null)) : ?>
                                <div class="mt-2">
                                    <strong>Ringkasan:</strong>
                                    <ul class="mb-0">
                                        <li>Baris diproses: <?= app_h((string) ($importSummary['summary']['total'] ?? 0)) ?></li>
                                        <li>Ditambahkan: <?= app_h((string) ($importSummary['summary']['added'] ?? 0)) ?></li>
                                        <li>Diperbarui: <?= app_h((string) ($importSummary['summary']['updated'] ?? 0)) ?></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle bi flex-shrink-0 me-2"></i>
                        <div><strong>Gagal!</strong> <?= app_h($errorMessage) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="stack-form">
                    <div class="mb-3">
                        <label class="toolbar-label" for="peraturan_workbook">File Excel peraturan</label>
                        <input type="file" id="peraturan_workbook" name="peraturan_workbook" class="form-control" accept=".xlsx" required>
                        <small class="text-muted">Format yang didukung hanya <code>.xlsx</code>.</small>
                    </div>

                    <div class="action-stack action-stack-inline">
                        <button class="btn btn-primary" type="submit" name="upload_peraturan">
                            <i class="fas fa-file-upload"></i>
                            <span>Upload dan Sinkronkan</span>
                        </button>
                        <a class="btn btn-outline-secondary" href="?page=peraturan&action=download_template">Download contoh format</a>
                    </div>
                </form>

                <section class="mt-4">
                    <h5>Format kolom Excel</h5>
                    <p>Gunakan setidaknya dua kolom berikut pada baris pertama:</p>
                    <ul>
                        <li><code>Jenis Peraturan</code> atau <code>Peraturan</code></li>
                        <li><code>Poin Pengurang</code> atau <code>Poin</code></li>
                    </ul>
                    <p>Baris selanjutnya akan diproses sebagai data peraturan. Nilai poin harus berupa angka bulat positif.</p>
                </section>
            </div>
        </div>
    </div>
</div>