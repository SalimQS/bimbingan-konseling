<?php
$className = $_SESSION['kelas'] ?? '';
$safeClassName = $connect->real_escape_string($className);

$studentResult = $connect->query("SELECT siswa.id_siswa FROM siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE kelas.nama_kelas = '{$safeClassName}'");
$students = $studentResult instanceof mysqli_result ? $studentResult->fetch_all(MYSQLI_ASSOC) : [];
$studentCount = count($students);
$totalPoints = 0;

foreach ($students as $studentRow) {
    $studentId = (int) $studentRow['id_siswa'];
    $pointsResult = $connect->query("SELECT peraturan.poin_peraturan FROM pelanggaran LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan WHERE pelanggaran.id_siswa = '{$studentId}'");
    if ($pointsResult instanceof mysqli_result) {
        while ($pointRow = $pointsResult->fetch_assoc()) {
            $totalPoints += (int) ($pointRow['poin_peraturan'] ?? 0);
        }
    }
}

$averagePoints = $studentCount > 0 ? round($totalPoints / $studentCount, 1) : 0;
$ruleCount = ($connect->query('SELECT COUNT(*) AS total FROM peraturan')->fetch_assoc()['total'] ?? 0);
$violationCount = ($connect->query("SELECT COUNT(*) AS total FROM pelanggaran LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas WHERE kelas.nama_kelas = '{$safeClassName}'")->fetch_assoc()['total'] ?? 0);

$search = isset($_POST['btn-cari']) ? trim((string) ($_POST['cari'] ?? '')) : '';
$where = ["kelas.nama_kelas = '{$safeClassName}'"];

if ($search !== '') {
    $safeSearch = $connect->real_escape_string($search);
    $where[] = "(peraturan.jenis_peraturan LIKE '%{$safeSearch}%' OR siswa.nama_lengkap LIKE '%{$safeSearch}%')";
}

$query = 'SELECT siswa.id_siswa, peraturan.jenis_peraturan, peraturan.poin_peraturan, siswa.nama_lengkap, kelas.nama_kelas, siswa.nisn, pelanggaran.tanggal_pelanggaran, pelanggaran.tempat_pelanggaran FROM pelanggaran LEFT JOIN siswa ON siswa.id_siswa = pelanggaran.id_siswa LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan WHERE ' . implode(' AND ', $where) . ' ORDER BY pelanggaran.tanggal_pelanggaran DESC';
$violationResult = $connect->query($query);
$violations = $violationResult instanceof mysqli_result ? $violationResult->fetch_all(MYSQLI_ASSOC) : [];
?>

<div class="row g-3 mt-1">
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah siswa kelas', $studentCount, 'fa-user-graduate', 'teal', $className) ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah peraturan', $ruleCount, 'fa-book-open', 'blue', 'Aturan aktif sekolah') ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Rata-rata poin', $averagePoints, 'fa-chart-line', 'gold', 'Poin per siswa di kelas') ?>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <?= app_render_stat_card('Jumlah pelanggaran', $violationCount, 'fa-triangle-exclamation', 'red', 'Pelanggaran siswa kelas') ?>
  </div>
</div>

<section class="panel-card panel-spaced">
  <div class="section-head">
    <div>
      <span class="section-kicker">Wali Kelas</span>
      <h2>Ringkasan pelanggaran <?= app_h($className) ?></h2>
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
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <i class="fas fa-shield-heart"></i>
                <strong>Belum ada data pelanggaran untuk kelas ini.</strong>
                <span>Catatan pelanggaran akan tampil setelah data dibuat pada sistem.</span>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
