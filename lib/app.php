<?php

require_once __DIR__ . '/student_sync.php';

function app_boot(mysqli $connect): void
{
    static $booted = false;

    if ($booted) {
        return;
    }

    $booted = true;
    app_sync_students_from_source($connect);
}

function app_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_current_page(array $query): string
{
    return isset($query['page']) ? trim((string) $query['page']) : '';
}

function app_current_action(array $query): string
{
    return isset($query['action']) ? trim((string) $query['action']) : '';
}

function app_role_key(array $session): string
{
    $role = $session['role'] ?? '';
    $level = $session['level'] ?? '';

    if ($role === 'admin' && $level === 'petugas') {
        return 'admin:petugas';
    }

    if ($role === 'admin' && ($level === 'bk' || $level === 'kepsek')) {
        return 'admin:non_petugas';
    }

    if ($role === 'guru' && $level === 'walikelas') {
        return 'guru:walikelas';
    }

    return 'guru:guru';
}

function app_route_definitions(): array
{
    return [
        'admin:petugas' => [
            '' => ['file' => 'dashboard_admin/index.php', 'title' => 'Dashboard', 'scripts' => ['assets/js/dashboard.js']],
            'kelas' => ['file' => 'kelas_admin/index.php', 'title' => 'Data Kelas', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/kelas.js']],
            'guru' => [
                'actions' => [
                    '' => ['file' => 'guru_admin/index.php', 'title' => 'Data Guru', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/guru.js']],
                    'add' => ['file' => 'guru_admin/add.php', 'title' => 'Tambah Data Guru', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/add_guru.js']],
                    'edit' => ['file' => 'guru_admin/edit.php', 'title' => 'Ubah Data Guru', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/edit_guru.js']],
                ],
            ],
            'siswa' => [
                'actions' => [
                    '' => ['file' => 'siswa_admin/index.php', 'title' => 'Data Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/siswa.js']],
                    'edit' => ['file' => 'siswa_admin/edit.php', 'title' => 'Kelola Data Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/edit_siswa.js']],
                    'add' => ['file' => 'siswa_admin/add.php', 'title' => 'Sumber Data Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                    'lihat' => ['file' => 'siswa_admin/review.php', 'title' => 'Detail Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                    'pelanggaran' => ['file' => 'siswa_admin/add_pelanggaran.php', 'title' => 'Tambah Pelanggaran Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/pelanggaran.js']],
                ],
            ],
            'peraturan' => [
                'actions' => [
                    '' => ['file' => 'peraturan_admin/index.php', 'title' => 'List Peraturan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/list.js']],
                    'add' => ['file' => 'peraturan_admin/add.php', 'title' => 'Tambah List Peraturan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                    'edit' => ['file' => 'peraturan_admin/edit.php', 'title' => 'Ubah List Peraturan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                ],
            ],
            'peringatan' => ['file' => 'peringatan/index.php', 'title' => 'Peringatan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/peringatan.js']],
            'user' => [
                'actions' => [
                    '' => ['file' => 'user_admin/index.php', 'title' => 'User', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/user.js']],
                    'add' => ['file' => 'user_admin/add.php', 'title' => 'Tambah User', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                    'edit' => ['file' => 'user_admin/edit.php', 'title' => 'Edit User', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                ],
            ],
        ],
        'admin:non_petugas' => [
            '' => ['file' => 'dashboard_not_admin/index.php', 'title' => 'Dashboard', 'scripts' => ['assets/js/dashboard.js']],
            'kelas' => ['file' => 'kelas_not_admin/index.php', 'title' => 'Data Kelas', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/kelas.js']],
            'guru' => ['file' => 'guru_not_admin/index.php', 'title' => 'Data Guru', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/guru.js']],
            'siswa' => [
                'actions' => [
                    '' => ['file' => 'siswa_not_admin/index.php', 'title' => 'Data Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/siswa.js']],
                    'lihat' => ['file' => 'siswa_not_admin/review.php', 'title' => 'Detail Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
                    'pelanggaran' => ['file' => 'siswa_not_admin/add_pelanggaran.php', 'title' => 'Tambah Pelanggaran Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/pelanggaran.js']],
                ],
            ],
            'peraturan' => ['file' => 'peraturan_not_admin/index.php', 'title' => 'List Peraturan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/list.js']],
            'peringatan' => ['file' => 'peringatan/index.php', 'title' => 'Peringatan', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/peringatan.js']],
        ],
        'guru:walikelas' => [
            '' => ['file' => 'dashboard_wali_kelas/index.php', 'title' => 'Dashboard', 'scripts' => []],
            'siswa' => [
                'actions' => [
                    '' => ['file' => 'siswa_wali_kelas/index.php', 'title' => 'Data Siswa', 'scripts' => ['assets/js/siswa.js']],
                    'lihat' => ['file' => 'siswa_wali_kelas/review.php', 'title' => 'Detail Siswa', 'scripts' => []],
                ],
            ],
            'peraturan' => ['file' => 'peraturan_guru/index.php', 'title' => 'Peraturan', 'scripts' => []],
            'profile' => ['file' => 'profile_guru/index.php', 'title' => 'Profil', 'scripts' => ['assets/js/profile.js']],
        ],
        'guru:guru' => [
            '' => ['file' => 'peraturan_guru/index.php', 'title' => 'Dashboard', 'scripts' => []],
            'profile' => ['file' => 'profile_guru/index.php', 'title' => 'Profil', 'scripts' => ['assets/js/profile.js']],
        ],
    ];
}

function app_resolve_route(array $session, array $query): array
{
    $routes = app_route_definitions();
    $roleKey = app_role_key($session);
    $page = app_current_page($query);
    $action = app_current_action($query);
    $roleRoutes = $routes[$roleKey] ?? $routes['guru:guru'];
    $pageRoute = $roleRoutes[$page] ?? $roleRoutes[''];

    if (isset($pageRoute['actions'])) {
        $pageRoute = $pageRoute['actions'][$action] ?? $pageRoute['actions'][''];
    }

    return [
        'file' => $pageRoute['file'],
        'title' => $pageRoute['title'],
        'scripts' => $pageRoute['scripts'] ?? [],
    ];
}

function app_sidebar_menu_items(array $session): array
{
    $menus = [
        'admin:petugas' => [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => 'index.php'],
            ['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'href' => 'index.php?page=siswa'],
            ['label' => 'Guru', 'icon' => 'fa-chalkboard-user', 'href' => 'index.php?page=guru'],
            ['label' => 'Kelas', 'icon' => 'fa-layer-group', 'href' => 'index.php?page=kelas'],
            ['label' => 'Peraturan', 'icon' => 'fa-list-check', 'href' => 'index.php?page=peraturan'],
            ['label' => 'Peringatan', 'icon' => 'fa-bell', 'href' => 'index.php?page=peringatan'],
            ['label' => 'Akun', 'icon' => 'fa-user-shield', 'href' => 'index.php?page=user'],
        ],
        'admin:non_petugas' => [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => 'index.php'],
            ['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'href' => 'index.php?page=siswa'],
            ['label' => 'Guru', 'icon' => 'fa-chalkboard-user', 'href' => 'index.php?page=guru'],
            ['label' => 'Kelas', 'icon' => 'fa-layer-group', 'href' => 'index.php?page=kelas'],
            ['label' => 'Peraturan', 'icon' => 'fa-list-check', 'href' => 'index.php?page=peraturan'],
            ['label' => 'Peringatan', 'icon' => 'fa-bell', 'href' => 'index.php?page=peringatan'],
        ],
        'guru:walikelas' => [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => 'index.php'],
            ['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'href' => 'index.php?page=siswa'],
            ['label' => 'Peraturan', 'icon' => 'fa-list-check', 'href' => 'index.php?page=peraturan'],
            ['label' => 'Profil', 'icon' => 'fa-id-card', 'href' => 'index.php?page=profile'],
        ],
        'guru:guru' => [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => 'index.php'],
            ['label' => 'Profil', 'icon' => 'fa-id-card', 'href' => 'index.php?page=profile'],
        ],
    ];

    return $menus[app_role_key($session)] ?? $menus['guru:guru'];
}

function app_user_role_label(array $session): string
{
    $role = $session['role'] ?? '';
    $level = $session['level'] ?? '';

    if ($role === 'admin' && $level === 'petugas') {
        return 'Petugas';
    }

    if ($role === 'admin' && $level === 'bk') {
        return 'Guru BK';
    }

    if ($role === 'admin' && $level === 'kepsek') {
        return 'Kepala Sekolah';
    }

    if ($role === 'guru' && $level === 'walikelas') {
        $kelas = $session['kelas'] ?? '';
        return $kelas !== '' ? 'Wali Kelas ' . $kelas : 'Wali Kelas';
    }

    return 'Guru';
}

function app_script_tags(array $scripts): string
{
    $tags = [];
    foreach ($scripts as $script) {
        $tags[] = '<script src="' . app_h($script) . '"></script>';
    }

    return implode("\n", $tags);
}

function app_student_source_summary(): array
{
    $snapshot = app_read_student_sync_snapshot();
    $sourcePath = app_student_source_path();

    return [
        'source_file' => $snapshot['source_file'] ?? basename($sourcePath),
        'source_path' => $snapshot['source_path'] ?? $sourcePath,
        'student_count' => $snapshot['student_count'] ?? null,
        'class_count' => $snapshot['class_count'] ?? null,
        'synced_at' => $snapshot['synced_at'] ?? null,
    ];
}

function app_source_meta_chips(): array
{
    $summary = app_student_source_summary();
    $chips = ['Sumber siswa: ' . $summary['source_file']];

    if (!empty($summary['student_count'])) {
        $chips[] = $summary['student_count'] . ' siswa aktif';
    }

    if (!empty($summary['class_count'])) {
        $chips[] = $summary['class_count'] . ' kelas';
    }

    if (!empty($summary['synced_at'])) {
        $chips[] = 'Sinkron: ' . date('d M Y H:i', strtotime($summary['synced_at']));
    }

    return $chips;
}

function app_render_page_intro(string $title, string $description, array $chips = [], string $actionsHtml = ''): string
{
    ob_start();
    ?>
    <section class="page-intro">
        <div class="page-intro-copy">
            <span class="page-intro-kicker">Bimbingan Konseling</span>
            <h1><?= app_h($title) ?></h1>
            <p><?= app_h($description) ?></p>
            <?php if ($chips !== []) : ?>
                <div class="inline-meta">
                    <?php foreach ($chips as $chip) : ?>
                        <span class="meta-chip"><?= app_h($chip) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($actionsHtml !== '') : ?>
            <div class="page-intro-actions">
                <?= $actionsHtml ?>
            </div>
        <?php endif; ?>
    </section>
    <?php

    return trim((string) ob_get_clean());
}

function app_render_stat_card(string $label, $value, string $icon, string $tone = 'teal', string $meta = ''): string
{
    ob_start();
    ?>
    <div class="stat-card stat-card-<?= app_h($tone) ?>">
        <div>
            <span class="stat-label"><?= app_h($label) ?></span>
            <strong class="stat-value"><?= app_h($value) ?></strong>
            <?php if ($meta !== '') : ?>
                <span class="stat-meta"><?= app_h($meta) ?></span>
            <?php endif; ?>
        </div>
        <div class="stat-icon">
            <i class="fas <?= app_h($icon) ?>"></i>
        </div>
    </div>
    <?php

    return trim((string) ob_get_clean());
}

function app_gender_label($value): string
{
    return (string) $value === '1' ? 'Perempuan' : 'Laki-Laki';
}

function app_gender_badge_class($value): string
{
    return (string) $value === '1' ? 'badge-gender-female' : 'badge-gender-male';
}

function app_religion_label($value): string
{
    $religions = [
        '0' => 'Islam',
        '1' => 'Kristen Protestan',
        '2' => 'Kristen Katholik',
        '3' => 'Hindu',
        '4' => 'Buddha',
        '5' => 'Konghucu',
    ];

    return $religions[(string) $value] ?? 'Islam';
}

function app_fetch_class_filter_options(mysqli $connect, ?string $restrictClassName = null): array
{
    $query = 'SELECT kelas.id_kelas AS id, kelas.nama_kelas AS kelas, COALESCE(guru.nama_guru, \'Belum ditetapkan\') AS nama_lengkap, COUNT(siswa.id_siswa) AS total_siswa FROM kelas LEFT JOIN siswa ON siswa.id_kelas = kelas.id_kelas LEFT JOIN guru ON guru.id_guru = kelas.id_wali_kelas';
    $where = [];

    if ($restrictClassName !== null && $restrictClassName !== '') {
        $safeClassName = $connect->real_escape_string($restrictClassName);
        $where[] = "kelas.nama_kelas = '{$safeClassName}'";
    }

    $where[] = 'siswa.id_siswa IS NOT NULL';

    if ($where !== []) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }

    $query .= ' GROUP BY kelas.id_kelas, kelas.nama_kelas, guru.nama_guru ORDER BY kelas.nama_kelas';

    $options = [];
    $result = $connect->query($query);
    if (!$result instanceof mysqli_result) {
        return $options;
    }

    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }

    return $options;
}
