<?php

$studentListConfig = [
    'title' => 'Data Siswa',
    'description' => 'Lihat data siswa aktif, buka detail, lengkapi data pendukung, dan perbarui biodata melalui upload Excel dari halaman ini.',
    'show_class_filter' => true,
    'allow_pelanggaran' => true,
    'allow_manage' => true,
    'source_action_url' => '?page=siswa&action=add',
];

include __DIR__ . '/../siswa/shared_list.php';
