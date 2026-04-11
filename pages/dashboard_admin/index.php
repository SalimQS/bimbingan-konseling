<?php
$studentCount = ($connect->query('SELECT COUNT(*) AS total FROM siswa')->fetch_assoc()['total'] ?? 0);
$teacherCount = ($connect->query('SELECT COUNT(*) AS total FROM guru')->fetch_assoc()['total'] ?? 0);
$ruleCount = ($connect->query('SELECT COUNT(*) AS total FROM peraturan')->fetch_assoc()['total'] ?? 0);
$violationCount = ($connect->query('SELECT COUNT(*) AS total FROM pelanggaran')->fetch_assoc()['total'] ?? 0);

$successMessage = '';
$errorMessage = '';

if (isset($_POST['delete'])) {
    $violationId = (int) ($_POST['id'] ?? 0);
    if ($violationId > 0 && $connect->query("DELETE FROM pelanggaran WHERE id_pelanggaran = '{$violationId}'")) {
        $successMessage = 'Data pelanggaran berhasil dihapus.';
    } else {
        $errorMessage = 'Data pelanggaran gagal dihapus.';
    }
}

$search = isset($_POST['btn-cari']) ? trim((string) ($_POST['cari'] ?? '')) : '';
$selectedClass = isset($_GET['kelas']) ? trim((string) $_GET['kelas']) : '';
$where = [];

if ($selectedClass !== '' && $selectedClass !== '-1') {
    $where[] = "kelas.id_kelas = '" . (int) $selectedClass . "'";
}

if ($search !== '') {
    $safeSearch = $connect->real_escape_string($search);
    $where[] = "(peraturan.jenis_peraturan LIKE '%{$safeSearch}%' OR siswa.nama_lengkap LIKE '%{$safeSearch}%')";
}

$query = 'SELECT siswa.id_siswa, peraturan.jenis_peraturan, peraturan.poin_peraturan, siswa.nama_lengkap, kelas.nama_kelas, siswa.nisn, pelanggaran.tanggal_pelanggaran, pelanggaran.tempat_pelanggaran, pelanggaran.id_pelanggaran FROM pelanggaran LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan';
if ($where !== []) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY pelanggaran.tanggal_pelanggaran DESC';

$violationResult = $connect->query($query);
$violations = $violationResult instanceof mysqli_result ? $violationResult->fetch_all(MYSQLI_ASSOC) : [];
$classOptions = app_fetch_class_filter_options($connect);
?>

<div class="row g-3 mt-1">
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah siswa aktif', $studentCount, 'fa-user-graduate', 'teal', 'Data dari upload terakhir') ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah guru', $teacherCount, 'fa-chalkboard-teacher', 'blue', 'Akun pendidik aktif') ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('List peraturan', $ruleCount, 'fa-book-open', 'gold', 'Acuan poin pelanggaran') ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah pelanggaran', $violationCount, 'fa-exclamation-triangle', 'red', 'Total catatan pelanggaran') ?>
  </div>
</div>

<?php if ($successMessage !== '') : ?>
  <div class="alert alert-success app-alert" role="alert"><?= app_h($successMessage) ?></div>
<?php endif; ?>
<?php if ($errorMessage !== '') : ?>
  <div class="alert alert-danger app-alert" role="alert"><?= app_h($errorMessage) ?></div>
<?php endif; ?>

<section class="panel-card panel-spaced">
  <div class="section-head">
    <div>
      <span class="section-kicker">Aktivitas Terbaru</span>
      <h2>Daftar pelanggaran siswa</h2>
    </div>
  </div>

  <div class="toolbar-card">
    <form method="post" class="toolbar-search">
      <label class="toolbar-label" for="dashboard-cari">Cari pelanggaran</label>
      <div class="input-group">
        <input type="text" class="form-control" id="dashboard-cari" name="cari" placeholder="Cari nama siswa atau jenis pelanggaran" value="<?= app_h($search) ?>">
        <button class="btn btn-primary" type="submit" name="btn-cari">
          <i class="fas fa-search"></i>
          <span>Cari</span>
        </button>
      </div>
    </form>

    <div class="toolbar-filter">
      <label class="toolbar-label" for="dashboardKelasFilter">Filter kelas</label>
      <select class="form-select kelasFilter" id="dashboardKelasFilter" name="kelas">
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
          <th>Pelanggaran</th>
          <th>Poin</th>
          <th>Tanggal</th>
          <th>Tempat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($violations !== []) : ?>
          <?php foreach ($violations as $index => $violation) : ?>
            <tr>
              <td><?= app_h($index + 1) ?></td>
              <td><?= app_h($violation['nisn']) ?></td>
              <td><a href="index.php?page=siswa&action=lihat&id=<?= app_h($violation['id_siswa']) ?>" class="text-strong"><?= app_h($violation['nama_lengkap']) ?></a></td>
              <td><?= app_h($violation['nama_kelas']) ?></td>
              <td><?= app_h($violation['jenis_peraturan']) ?></td>
              <td><span class="status-pill badge-points"><?= app_h($violation['poin_peraturan']) ?> poin</span></td>
              <td><?= app_h($violation['tanggal_pelanggaran']) ?></td>
              <td><?= app_h($violation['tempat_pelanggaran']) ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="id" value="<?= app_h($violation['id_pelanggaran']) ?>">
                  <input type="hidden" name="delete" value="1">
                  <button class="btn btn-sm btn-outline-secondary" type="submit">
                    <i class="fas fa-trash"></i>
                    <span>Hapus</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <i class="fas fa-shield-alt"></i>
                <strong>Belum ada data pelanggaran.</strong>
                <span>Catatan pelanggaran siswa akan muncul di sini.</span>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
