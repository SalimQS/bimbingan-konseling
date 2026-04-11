<?php

$studentListConfig = [
    'title' => 'Data Siswa Kelas',
    'description' => 'Daftar siswa kelas wali mengikuti hasil upload data siswa terbaru yang dilakukan admin.',
    'restrict_class_name' => $_SESSION['kelas'] ?? '',
    'show_class_filter' => false,
    'allow_pelanggaran' => false,
    'allow_manage' => false,
];

include __DIR__ . '/../siswa/shared_list.php';
