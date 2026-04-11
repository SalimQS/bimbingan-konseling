<?php

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$successMessage = '';
$errorMessage = '';

if ($studentId > 0 && isset($_POST['save_supporting_data'])) {
    $motherName = trim((string) ($_POST['nama_ibu'] ?? ''));
    if ($motherName === '') {
        $errorMessage = 'Nama ibu kandung tidak boleh kosong.';
    } else {
        $safeMotherName = $connect->real_escape_string($motherName);
        $query = "UPDATE siswa SET nama_ibu = '{$safeMotherName}' WHERE id_siswa = '{$studentId}'";
        if ($connect->query($query)) {
            $successMessage = 'Nama ibu kandung berhasil diperbarui.';
        } else {
            $errorMessage = 'Nama ibu kandung gagal diperbarui.';
        }
    }
}

if ($studentId > 0 && isset($_POST['save_photo'])) {
    $photo = $_FILES['foto'] ?? null;

    if ($photo === null || (int) ($photo['size'] ?? 0) <= 0) {
        $errorMessage = 'Pilih foto terlebih dahulu.';
    } elseif ((int) $photo['size'] > 2097152) {
        $errorMessage = 'Ukuran foto maksimal 2 MB.';
    } elseif (@getimagesize($photo['tmp_name']) === false) {
        $errorMessage = 'File yang dipilih bukan gambar yang valid.';
    } else {
        $extension = strtolower(pathinfo((string) $photo['name'], PATHINFO_EXTENSION));
        $extension = in_array($extension, ['jpg', 'jpeg', 'png'], true) ? $extension : 'jpg';
        $targetPath = 'uploads/' . time() . '-' . $studentId . '.' . $extension;
        $safeTargetPath = $connect->real_escape_string($targetPath);
        $query = "UPDATE siswa SET foto_siswa = '{$safeTargetPath}' WHERE id_siswa = '{$studentId}'";

        $connect->begin_transaction();

        try {
            if (!$connect->query($query)) {
                throw new RuntimeException('Query update foto gagal dijalankan.');
            }

            if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
                throw new RuntimeException('File foto gagal diunggah.');
            }

            $connect->commit();
            $successMessage = 'Foto siswa berhasil diperbarui.';
        } catch (Throwable $exception) {
            $connect->rollback();
            $errorMessage = $exception->getMessage();
        }
    }
}

$student = null;
if ($studentId > 0) {
    $query = "SELECT kelas.nama_kelas, siswa.* FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE siswa.id_siswa = '{$studentId}' LIMIT 1";
    $result = $connect->query($query);
    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
    }
}

$actions = '<div class="action-stack action-stack-inline"><a class="btn btn-outline-secondary" href="?page=siswa"><i class="fas fa-arrow-left"></i> <span>Kembali</span></a>';
if ($student !== null) {
    $actions .= '<a class="btn btn-outline-primary" href="?page=siswa&action=lihat&id=' . app_h($studentId) . '"><i class="fas fa-eye"></i> <span>Lihat Detail</span></a>';
}
$actions .= '</div>';

echo app_render_page_intro(
    'Kelola Data Pendukung Siswa',
    'Identitas inti siswa mengikuti file sumber. Halaman ini dipakai untuk melengkapi data pendukung yang tidak berasal dari file impor.',
    app_source_meta_chips(),
    $actions
);
?>

<?php if ($student === null) : ?>
    <section class="panel-card panel-spaced">
        <div class="empty-state">
            <i class="fas fa-circle-exclamation"></i>
            <strong>Data siswa tidak ditemukan.</strong>
            <span>Kembali ke daftar siswa lalu pilih data yang ingin dikelola.</span>
        </div>
    </section>
<?php else : ?>
    <?php $photo = trim((string) ($student['foto_siswa'] ?? '')) !== '' ? $student['foto_siswa'] : 'assets/img/default.jpg'; ?>

    <?php if ($successMessage !== '') : ?>
        <div class="alert alert-success app-alert" role="alert"><?= app_h($successMessage) ?></div>
    <?php endif; ?>
    <?php if ($errorMessage !== '') : ?>
        <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorMessage) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <section class="panel-card panel-spaced h-100">
                <div class="student-profile-card student-profile-card-column">
                    <img src="<?= app_h($photo) ?>" alt="<?= app_h($student['nama_lengkap']) ?>" id="previewFoto" class="student-profile-photo">
                    <div class="text-center">
                        <h2><?= app_h($student['nama_lengkap']) ?></h2>
                        <p><?= app_h($student['nama_kelas']) ?> • <?= app_h($student['nisn']) ?></p>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data" class="stack-form">
                    <label class="toolbar-label" for="foto">Foto siswa</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/png,image/jpg,image/jpeg" onchange="handlePreview(this)">
                    <div class="action-stack action-stack-inline">
                        <button class="btn btn-primary" type="submit" name="save_photo">
                            <i class="fas fa-upload"></i>
                            <span>Simpan Foto</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <section class="panel-card panel-spaced">
                <div class="section-head">
                    <div>
                        <span class="section-kicker">Data Pendukung</span>
                        <h2>Lengkapi informasi yang tidak ada di file sumber</h2>
                    </div>
                    <span class="status-pill badge-source">Source managed</span>
                </div>

                <form method="post" class="stack-form">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="toolbar-label" for="nama_ibu">Nama ibu kandung</label>
                            <input type="text" id="nama_ibu" name="nama_ibu" class="form-control" value="<?= app_h($student['nama_ibu']) ?>" placeholder="Masukkan nama ibu kandung">
                        </div>

                        <div class="col-md-6">
                            <label class="toolbar-label">Nama lengkap</label>
                            <input type="text" class="form-control" value="<?= app_h($student['nama_lengkap']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Kelas</label>
                            <input type="text" class="form-control" value="<?= app_h($student['nama_kelas']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">NISN</label>
                            <input type="text" class="form-control" value="<?= app_h($student['nisn']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Jenis kelamin</label>
                            <input type="text" class="form-control" value="<?= app_h(app_gender_label($student['jenis_kelamin'])) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Tempat lahir</label>
                            <input type="text" class="form-control" value="<?= app_h($student['tempat_lahir']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Tanggal lahir</label>
                            <input type="text" class="form-control" value="<?= app_h($student['tanggal_lahir']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Agama</label>
                            <input type="text" class="form-control" value="<?= app_h(app_religion_label($student['agama'])) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="toolbar-label">Telepon</label>
                            <input type="text" class="form-control" value="<?= app_h($student['no_telepon']) ?>" readonly>
                        </div>
                        <div class="col-12">
                            <label class="toolbar-label">Alamat</label>
                            <textarea class="form-control" rows="3" readonly><?= app_h($student['alamat']) ?></textarea>
                        </div>
                    </div>

                    <div class="action-stack action-stack-inline">
                        <button class="btn btn-primary" type="submit" name="save_supporting_data">
                            <i class="fas fa-save"></i>
                            <span>Simpan Nama Ibu</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
<?php endif; ?>
