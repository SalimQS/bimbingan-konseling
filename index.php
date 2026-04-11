<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('location: login.php');
    exit;
}

include_once './config/db.php';

$page = app_current_page($_GET);
$action = app_current_action($_GET);
$route = app_resolve_route($_SESSION, $_GET);
$title = $route['title'];
$file = $route['file'];
$scriptTags = app_script_tags($route['scripts']);
$roleLabel = app_user_role_label($_SESSION);
$sourceSummary = app_student_source_summary();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">

    <link href="./vendors/bootstrap-5.0.0-beta3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">

    <script defer src="./vendors/jQuery-3.6.0/jQuery.min.js"></script>
    <script defer src="./vendors/bootstrap-5.0.0-beta3-dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="./vendors/fontawesome-free-5.15.3-web/js/all.min.js"></script>
    <script defer src="./assets/js/script.js"></script>

    <title><?= app_h($title) ?> | Bimbingan Konseling</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <?= $scriptTags ?>
  </head>
  <body class="app-body">
    <div class="app-shell">
      <?php include './components/sidebar.php'; ?>

      <div id="main">
        <?php include './components/navbar.php'; ?>

        <main class="content-shell">
          <section class="hero-banner">
            <div class="hero-copy">
              <span class="hero-kicker">Dashboard Konseling</span>
              <h1>Selamat datang, <?= app_h($_SESSION['nama_lengkap']) ?></h1>
              <p>Anda masuk sebagai <strong><?= app_h($roleLabel) ?></strong>. Data siswa aktif mengikuti file sumber yang disinkronkan otomatis agar daftar, kelas, dan pencarian tetap konsisten.</p>
              <div class="inline-meta">
                <span class="meta-chip"><?= app_h($sourceSummary['source_file']) ?></span>
                <?php if (!empty($sourceSummary['student_count'])) : ?>
                  <span class="meta-chip"><?= app_h($sourceSummary['student_count']) ?> siswa</span>
                <?php endif; ?>
                <?php if (!empty($sourceSummary['class_count'])) : ?>
                  <span class="meta-chip"><?= app_h($sourceSummary['class_count']) ?> kelas</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="hero-side">
              <div class="hero-side-card">
                <span class="hero-side-label">Sinkron terakhir</span>
                <strong><?= !empty($sourceSummary['synced_at']) ? app_h(date('d M Y H:i', strtotime($sourceSummary['synced_at']))) : 'Belum tersedia' ?></strong>
              </div>
              <div class="hero-side-card">
                <span class="hero-side-label">Halaman aktif</span>
                <strong><?= app_h($title) ?></strong>
              </div>
            </div>
          </section>

          <div id="content">
            <?php include 'pages/' . $file; ?>
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
