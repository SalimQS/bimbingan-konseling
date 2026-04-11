<?php

$studentListConfig = [
    'title' => 'Data Siswa Kelas',
    'description' => 'Roster kelas wali dipusatkan pada file sumber, sehingga daftar siswa tetap konsisten dengan data terbaru.',
    'restrict_class_name' => $_SESSION['kelas'] ?? '',
    'show_class_filter' => false,
    'allow_pelanggaran' => false,
    'allow_manage' => false,
];

include __DIR__ . '/../siswa/shared_list.php';
