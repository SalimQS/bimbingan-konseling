<?php

$summary = app_student_source_summary();

echo app_render_page_intro(
    'Sumber Data Siswa',
    'Penambahan dan pembaruan roster siswa dilakukan melalui file Excel sumber agar data tetap konsisten di seluruh halaman aplikasi.',
    app_source_meta_chips(),
    '<a class="btn btn-outline-secondary" href="?page=siswa"><i class="fas fa-arrow-left"></i> <span>Kembali ke daftar siswa</span></a>'
);
?>

<section class="panel-card panel-spaced">
    <div class="section-head">
        <div>
            <span class="section-kicker">Excel Source</span>
            <h2>Roster siswa mengikuti file terlampir</h2>
        </div>
        <span class="status-pill badge-source"><?= app_h($summary['source_file']) ?></span>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <h3>Cara memperbarui roster</h3>
            <p>Ganti file sumber pada folder aplikasi, lalu buka ulang halaman ini. Sistem akan menyelaraskan data siswa, kelas, dan filter daftar secara otomatis.</p>
        </div>
        <div class="info-card">
            <h3>Lokasi file sumber</h3>
            <code><?= app_h($summary['source_path']) ?></code>
        </div>
        <div class="info-card">
            <h3>Data yang tetap bisa dilengkapi di aplikasi</h3>
            <p>Foto siswa dan nama ibu kandung tetap dapat dilengkapi melalui halaman <strong>Kelola</strong> agar kebutuhan cek NISN tetap berjalan.</p>
        </div>
    </div>
</section>
