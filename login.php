<?php
session_start();

if (isset($_SESSION['id_user'])) {
    header('location: index.php');
    exit;
}

include_once './config/db.php';

$errorText = '';
$username = trim((string) ($_POST['username'] ?? ''));
$loginAs = isset($_POST['login_as']) ? (int) $_POST['login_as'] : 0;

if (isset($_POST['submit'])) {
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errorText = 'Masukkan username dan password.';
    } elseif ($loginAs === 0) {
        if (!app_table_exists($connect, 'guru')) {
            $errorText = 'Database aktif belum memiliki tabel guru. Periksa DB_NAME atau import schema aplikasi yang sesuai.';
        } else {
            try {
                $statement = $connect->prepare('SELECT * FROM guru WHERE username_guru = ? LIMIT 1');
                if ($statement) {
                    $statement->bind_param('s', $username);
                    $statement->execute();
                    $result = $statement->get_result();
                    $user = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

                    if ($user === null) {
                        $errorText = 'Akun ini tidak terdaftar sebagai guru.';
                    } elseif ((string) $user['password_guru'] !== $password) {
                        $errorText = 'Password salah.';
                    } else {
                        $kelas = null;
                        if (app_table_exists($connect, 'kelas')) {
                            $kelasStatement = $connect->prepare('SELECT * FROM kelas WHERE id_wali_kelas = ? LIMIT 1');
                            if ($kelasStatement) {
                                $kelasStatement->bind_param('i', $user['id_guru']);
                                $kelasStatement->execute();
                                $kelasResult = $kelasStatement->get_result();
                                $kelas = $kelasResult instanceof mysqli_result ? $kelasResult->fetch_assoc() : null;
                            }
                        }

                        $_SESSION['id_user'] = $user['id_guru'];
                        $_SESSION['username'] = $user['username_guru'];
                        $_SESSION['nama_lengkap'] = $user['nama_guru'];
                        $_SESSION['role'] = 'guru';
                        $_SESSION['level'] = $kelas ? 'walikelas' : 'guru';

                        if ($kelas) {
                            $_SESSION['kelas'] = $kelas['nama_kelas'];
                        } else {
                            unset($_SESSION['kelas']);
                        }

                        header('location: index.php');
                        exit;
                    }
                } else {
                    $errorText = 'Proses login guru tidak dapat dijalankan.';
                }
            } catch (mysqli_sql_exception $exception) {
                $errorText = 'Proses login guru tidak dapat dijalankan. Periksa struktur database yang digunakan.';
            }
        }
    } else {
        if (!app_table_exists($connect, 'admin')) {
            $errorText = 'Database aktif belum memiliki tabel admin. Periksa DB_NAME atau import schema aplikasi yang sesuai.';
        } else {
            try {
                $statement = $connect->prepare('SELECT * FROM admin WHERE username_admin = ? LIMIT 1');
                if ($statement) {
                    $statement->bind_param('s', $username);
                    $statement->execute();
                    $result = $statement->get_result();
                    $user = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

                    if ($user === null) {
                        $errorText = 'Akun ini tidak terdaftar sebagai admin.';
                    } elseif ((string) $user['password_admin'] !== $password) {
                        $errorText = 'Password salah.';
                    } else {
                        $_SESSION['id_user'] = $user['id_admin'];
                        $_SESSION['username'] = $user['username_admin'];
                        $_SESSION['nama_lengkap'] = $user['nama_admin'];
                        $_SESSION['role'] = 'admin';
                        $_SESSION['level'] = $user['level_admin'];
                        unset($_SESSION['kelas']);

                        header('location: index.php');
                        exit;
                    }
                } else {
                    $errorText = 'Proses login admin tidak dapat dijalankan.';
                }
            } catch (mysqli_sql_exception $exception) {
                $errorText = 'Proses login admin tidak dapat dijalankan. Periksa struktur database yang digunakan.';
            }
        }
    }
}

$summary = app_student_source_summary();
?>
<!doctype html>
<html lang="id">
<head>
    <title>Login | Bimbingan Konseling</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

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
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-showcase">
                <span class="auth-kicker">Student Care</span>
                <h1>Masuk ke sistem bimbingan konseling</h1>
                <p>Dashboard ini memusatkan pemantauan siswa, poin pelanggaran, dan data kelas yang disinkronkan dari file sumber aktif.</p>
                <div class="inline-meta">
                    <span class="meta-chip"><?= app_h($summary['source_file']) ?></span>
                    <?php if (!empty($summary['student_count'])) : ?>
                        <span class="meta-chip"><?= app_h($summary['student_count']) ?> siswa</span>
                    <?php endif; ?>
                </div>
                <a class="btn btn-light auth-secondary-btn" href="nisn_check.php">
                    <i class="fas fa-id-card"></i>
                    <span>Cek Data dengan NISN</span>
                </a>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-head">
                    <h2>Login</h2>
                    <p>Masukkan akun sesuai peran yang digunakan pada sistem.</p>
                </div>

                <?php if ($errorText !== '') : ?>
                    <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorText) ?></div>
                <?php endif; ?>

                <form method="post" class="stack-form">
                    <div>
                        <label class="toolbar-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" value="<?= app_h($username) ?>" required>
                    </div>

                    <div>
                        <label class="toolbar-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <div>
                        <label class="toolbar-label" for="login_as">Login sebagai</label>
                        <select class="form-select" name="login_as" id="login_as">
                            <option value="0" <?= $loginAs === 0 ? 'selected' : '' ?>>Guru</option>
                            <option value="1" <?= $loginAs === 1 ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-right-to-bracket"></i>
                        <span>Masuk</span>
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
