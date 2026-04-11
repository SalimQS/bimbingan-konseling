<?php

$studentListConfig = [
    'title' => 'Data Siswa',
    'description' => 'Lihat roster siswa aktif, buka detail siswa, lengkapi data pendukung, dan catat pelanggaran tanpa mengubah sumber roster utama.',
    'show_class_filter' => true,
    'allow_pelanggaran' => true,
    'allow_manage' => true,
    'source_action_url' => '?page=siswa&action=add',
];

include __DIR__ . '/../siswa/shared_list.php';
