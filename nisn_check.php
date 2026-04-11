<?php
session_start();

if (isset($_SESSION['id_user'])) {
    header('location: index.php');
    exit;
}

include_once './config/db.php';

$nisn = trim((string) ($_GET['nisn'] ?? ''));
$motherNameInput = trim((string) ($_GET['ibu'] ?? ''));
$errorText = '';
$student = null;
$totalPoints = 0;

if (isset($_GET['submit'])) {
    $nisn = trim((string) ($_GET['nisn'] ?? ''));
    $motherNameInput = trim((string) ($_GET['ibu'] ?? ''));

    if ($nisn === '') {
        $errorText = 'NISN wajib diisi.';
    } else {
        $statement = $connect->prepare('SELECT kelas.nama_kelas, siswa.* FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE siswa.nisn = ? LIMIT 1');
        if ($statement) {
            $statement->bind_param('s', $nisn);
            $statement->execute();
            $result = $statement->get_result();
            $candidate = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

            if ($candidate === null) {
                $errorText = 'Data siswa tidak ditemukan.';
            } else {
                $storedMotherName = trim((string) ($candidate['nama_ibu'] ?? ''));
                $isSourcePlaceholder = $storedMotherName === '' || in_array(
                    strtolower($storedMotherName),
                    ['belum tersedia di file sumber', 'belum diisi saat upload'],
                    true
                );
                $isValidMotherName = $isSourcePlaceholder || strcasecmp($storedMotherName, $motherNameInput) === 0;

                if (!$isValidMotherName) {
                    $errorText = 'Nama ibu kandung tidak sesuai.';
                } else {
                    $student = $candidate;

                    $pointsStatement = $connect->prepare('SELECT peraturan.poin_peraturan FROM pelanggaran LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan WHERE siswa.nisn = ?');
                    if ($pointsStatement) {
                        $pointsStatement->bind_param('s', $nisn);
                        $pointsStatement->execute();
                        $pointsResult = $pointsStatement->get_result();

                        if ($pointsResult instanceof mysqli_result) {
                            while ($pointRow = $pointsResult->fetch_assoc()) {
                                $totalPoints += (int) ($pointRow['poin_peraturan'] ?? 0);
                            }
                        }
                    }
                }
            }
        } else {
            $errorText = 'Pengecekan NISN tidak dapat dijalankan.';
        }
    }
}

$summary = app_student_source_summary();
?>
<!doctype html>
<html lang="id">
<head>
    <title>Cek NISN | Bimbingan Konseling</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">

    <link href="./vendors/bootstrap-5.0.0-beta3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">

    <script defer src="./vendors/jQuery-3.6.0/jQuery.min.js"></script>
    <script defer src="./vendors/bootstrap-5.0.0-beta3-dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="./vendors/fontawesome-free-5.15.3-web/js/all.min.js"></script>
    <script defer src="./assets/js/script.js"></script>
</head>
<body class="auth-body">
    <main class="auth-shell auth-shell-wide">
        <section class="auth-card auth-card-compact">
            <div class="auth-showcase">
                <span class="auth-kicker">Layanan Publik</span>
                <h1>Cek data siswa dengan NISN</h1>
                <p>Masukkan NISN dan nama ibu kandung untuk melihat ringkasan data siswa serta total poin pelanggaran pada sistem.</p>
                <div class="inline-meta">
                    <span class="meta-chip"><?= app_h($summary['source_file']) ?></span>
                    <?php if (!empty($summary['synced_at'])) : ?>
                        <span class="meta-chip">Sinkron <?= app_h(date('d M Y H:i', strtotime($summary['synced_at']))) ?></span>
                    <?php endif; ?>
                </div>
                <a class="btn btn-light auth-secondary-btn" href="login.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke login</span>
                </a>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h2>Verifikasi NISN</h2>
                    <p>Jika nama ibu belum dilengkapi saat upload data, pengecekan tetap akan mencoba menampilkan data berdasarkan NISN.</p>
                </div>

                <?php if ($errorText !== '') : ?>
                    <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorText) ?></div>
                <?php endif; ?>

                <?php if ($student === null) : ?>
                    <form method="get" class="stack-form">
                        <input type="hidden" name="submit" value="1">

                        <div>
                            <label class="toolbar-label" for="nisn">NISN</label>
                            <input type="text" id="nisn" name="nisn" class="form-control" placeholder="Masukkan NISN" value="<?= app_h($nisn) ?>" required>
                        </div>

                        <div>
                            <label class="toolbar-label" for="ibu">Nama ibu kandung</label>
                            <input type="text" id="ibu" name="ibu" class="form-control" placeholder="Masukkan nama ibu kandung" value="<?= app_h($motherNameInput) ?>">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i>
                            <span>Cari Data</span>
                        </button>
                    </form>
                <?php else : ?>
                    <div class="panel-card panel-spaced panel-flat">
                        <div class="profile-data-list">
                            <div>
                                <span>NISN</span>
                                <strong><?= app_h($student['nisn']) ?></strong>
                            </div>
                            <div>
                                <span>Nama lengkap</span>
                                <strong><?= app_h($student['nama_lengkap']) ?></strong>
                            </div>
                            <div>
                                <span>Jenis kelamin</span>
                                <strong><?= app_h(app_gender_label($student['jenis_kelamin'])) ?></strong>
                            </div>
                            <div>
                                <span>Kelas</span>
                                <strong><?= app_h($student['nama_kelas']) ?></strong>
                            </div>
                            <div>
                                <span>Jumlah poin</span>
                                <strong><?= app_h($totalPoints) ?> poin</strong>
                            </div>
                        </div>

                        <div class="action-stack action-stack-inline">
                            <a class="btn btn-outline-secondary" href="nisn_check.php">
                                <i class="fas fa-rotate-left"></i>
                                <span>Cek lagi</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
