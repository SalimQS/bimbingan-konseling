<?php

$studentListConfig = [
    'title' => 'Data Siswa',
    'description' => 'Pantau data siswa aktif per kelas dan akses detail siswa untuk keperluan konseling serta pencatatan pelanggaran.',
    'show_class_filter' => true,
    'allow_pelanggaran' => true,
    'allow_manage' => false,
];

include __DIR__ . '/../siswa/shared_list.php';
