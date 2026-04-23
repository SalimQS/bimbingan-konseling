<?php

require_once __DIR__ . '/student_sync.php';

function app_boot(mysqli $connect): void
{
    // Student import is now triggered manually from the upload page.
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

    return 'guru:guru';
}

function app_route_definitions(): array
{
    return [
        'admin:petugas' => [
            '' => ['file' => 'dashboard_admin/index.php', 'title' => 'Dashboard', 'scripts' => ['assets/js/dashboard.js']],
            'kelas' => [
                'actions' => [
                    '' => ['file' => 'kelas_admin/index.php', 'title' => 'Data Kelas', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/kelas.js']],
                    'edit' => ['file' => 'kelas_admin/index.php', 'title' => 'Ubah Kelas', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/kelas.js']],
                    'detail' => ['file' => 'kelas/shared_detail.php', 'title' => 'Detail Kelas', 'scripts' => ['assets/js/siswa.js']],
                ],
            ],
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
                    'add' => ['file' => 'siswa_admin/add.php', 'title' => 'Upload Data Siswa', 'scripts' => ['vendors/sweetalert/sweetalert.min.js']],
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
            'peringatan' => [
                'actions' => [
                    '' => ['file' => 'peringatan/index.php', 'title' => 'Peringatan', 'scripts' => ['assets/js/peringatan.js']],
                    'setting' => ['file' => 'peringatan/settings.php', 'title' => 'Pengaturan Peringatan', 'scripts' => []],
                ],
            ],
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
            'kelas' => [
                'actions' => [
                    '' => ['file' => 'kelas_not_admin/index.php', 'title' => 'Data Kelas', 'scripts' => ['vendors/sweetalert/sweetalert.min.js', 'assets/js/kelas.js']],
                    'detail' => ['file' => 'kelas/shared_detail.php', 'title' => 'Detail Kelas', 'scripts' => ['assets/js/siswa.js']],
                ],
            ],
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
            ['label' => 'Dashboard', 'icon' => 'fa-home', 'href' => 'index.php'],
            ['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'href' => 'index.php?page=siswa'],
            ['label' => 'Guru', 'icon' => 'fa-chalkboard-teacher', 'href' => 'index.php?page=guru'],
            ['label' => 'Kelas', 'icon' => 'fa-layer-group', 'href' => 'index.php?page=kelas'],
            ['label' => 'Peraturan', 'icon' => 'fa-list-alt', 'href' => 'index.php?page=peraturan'],
            ['label' => 'Peringatan', 'icon' => 'fa-bell', 'href' => 'index.php?page=peringatan'],
            ['label' => 'Akun', 'icon' => 'fa-user-shield', 'href' => 'index.php?page=user'],
        ],
        'admin:non_petugas' => [
            ['label' => 'Dashboard', 'icon' => 'fa-home', 'href' => 'index.php'],
            ['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'href' => 'index.php?page=siswa'],
            ['label' => 'Guru', 'icon' => 'fa-chalkboard-teacher', 'href' => 'index.php?page=guru'],
            ['label' => 'Kelas', 'icon' => 'fa-layer-group', 'href' => 'index.php?page=kelas'],
            ['label' => 'Peraturan', 'icon' => 'fa-list-alt', 'href' => 'index.php?page=peraturan'],
            ['label' => 'Peringatan', 'icon' => 'fa-bell', 'href' => 'index.php?page=peringatan'],
        ],
        'guru:guru' => [
            ['label' => 'Dashboard', 'icon' => 'fa-home', 'href' => 'index.php'],
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

    return 'Guru';
}

function app_can_manage_students(array $session): bool
{
    return app_role_key($session) === 'admin:petugas';
}

function app_can_manage_warning_settings(array $session): bool
{
    return app_role_key($session) === 'admin:petugas';
}

function app_can_manage_warning_letters(array $session): bool
{
    $roleKey = app_role_key($session);

    return $roleKey === 'admin:petugas' || $roleKey === 'admin:non_petugas';
}

function app_flash_set(string $key, array $payload): void
{
    if (!isset($_SESSION['_app_flash']) || !is_array($_SESSION['_app_flash'])) {
        $_SESSION['_app_flash'] = [];
    }

    $_SESSION['_app_flash'][$key] = $payload;
}

function app_flash_get(string $key): ?array
{
    if (!isset($_SESSION['_app_flash']) || !is_array($_SESSION['_app_flash']) || !isset($_SESSION['_app_flash'][$key])) {
        return null;
    }

    $payload = $_SESSION['_app_flash'][$key];
    unset($_SESSION['_app_flash'][$key]);

    return is_array($payload) ? $payload : null;
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

    return [
        'source_file' => $snapshot['source_file'] ?? 'Belum ada upload',
        'source_path' => $snapshot['source_path'] ?? '',
        'student_count' => $snapshot['student_count'] ?? null,
        'class_count' => $snapshot['class_count'] ?? null,
        'synced_at' => $snapshot['synced_at'] ?? null,
    ];
}

function app_source_meta_chips(): array
{
    $summary = app_student_source_summary();
    $chips = ['Upload siswa: ' . $summary['source_file']];

    if (!empty($summary['student_count'])) {
        $chips[] = $summary['student_count'] . ' siswa aktif';
    }

    if (!empty($summary['class_count'])) {
        $chips[] = $summary['class_count'] . ' kelas';
    }

    if (!empty($summary['synced_at'])) {
        $chips[] = 'Upload: ' . date('d M Y H:i', strtotime($summary['synced_at']));
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
    $query = 'SELECT kelas.id_kelas AS id, kelas.nama_kelas AS kelas, COUNT(siswa.id_siswa) AS total_siswa FROM kelas LEFT JOIN siswa ON siswa.id_kelas = kelas.id_kelas';
    $where = [];

    if ($restrictClassName !== null && $restrictClassName !== '') {
        $safeClassName = $connect->real_escape_string($restrictClassName);
        $where[] = "kelas.nama_kelas = '{$safeClassName}'";
    }

    $where[] = 'siswa.id_siswa IS NOT NULL';

    if ($where !== []) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }

    $query .= ' GROUP BY kelas.id_kelas, kelas.nama_kelas ORDER BY kelas.nama_kelas';

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

function app_warning_max_points(): int
{
    return 200;
}

function app_warning_min_points(): int
{
    return 1;
}

function app_warning_default_thresholds(): array
{
    return [
        'sp1' => 150,
        'sp2' => 100,
        'sp3' => 50,
        'pemberhentian' => 1,
    ];
}

function app_warning_state_blueprints(): array
{
    return [
        'sp1' => [
            'key' => 'sp1',
            'label' => 'SP1',
            'title' => 'Peringatan 1',
            'description' => 'Surat Peringatan 1',
            'tone' => 'gold',
            'icon' => 'fa-flag',
            'badge_class' => 'badge-warning-sp1',
        ],
        'sp2' => [
            'key' => 'sp2',
            'label' => 'SP2',
            'title' => 'Peringatan 2',
            'description' => 'Surat Peringatan 2',
            'tone' => 'blue',
            'icon' => 'fa-bell',
            'badge_class' => 'badge-warning-sp2',
        ],
        'sp3' => [
            'key' => 'sp3',
            'label' => 'SP3',
            'title' => 'Peringatan 3',
            'description' => 'Surat Peringatan 3',
            'tone' => 'red',
            'icon' => 'fa-exclamation-triangle',
            'badge_class' => 'badge-warning-sp3',
        ],
        'pemberhentian' => [
            'key' => 'pemberhentian',
            'label' => 'Pemberhentian',
            'title' => 'Pemberhentian',
            'description' => 'Surat pemberhentian siswa',
            'tone' => 'red',
            'icon' => 'fa-user-slash',
            'badge_class' => 'badge-warning-pemberhentian',
        ],
    ];
}

function app_parse_warning_thresholds(array $input): array
{
    $thresholds = [];
    foreach (app_warning_default_thresholds() as $key => $defaultValue) {
        $thresholds[$key] = isset($input[$key]) ? (int) $input[$key] : $defaultValue;
    }

    return $thresholds;
}

function app_validate_warning_thresholds(array $thresholds): string
{
    $labels = [
        'sp1' => 'SP1',
        'sp2' => 'SP2',
        'sp3' => 'SP3',
        'pemberhentian' => 'Pemberhentian',
    ];
    $maxPoints = app_warning_max_points();
    $minPoints = app_warning_min_points();
    $previousValue = null;
    $previousLabel = '';

    foreach (app_warning_default_thresholds() as $key => $defaultValue) {
        $value = (int) ($thresholds[$key] ?? $defaultValue);

        if ($value < $minPoints) {
            return 'Batas poin ' . $labels[$key] . ' harus minimal ' . $minPoints . '.';
        }

        if ($value > $maxPoints) {
            return 'Batas poin ' . $labels[$key] . ' tidak boleh lebih dari ' . $maxPoints . '.';
        }

        if ($previousLabel !== '' && $previousValue !== null && $value >= $previousValue) {
            return 'Batas poin ' . $labels[$key] . ' harus lebih kecil dari ' . $previousLabel . '.';
        }

        $previousValue = $value;
        $previousLabel = $labels[$key];
    }

    return '';
}

function app_ensure_warning_settings_table(mysqli $connect): void
{
    $defaults = app_warning_default_thresholds();
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `pengaturan_peringatan` (
        `id_pengaturan` TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `sp1_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT {$defaults['sp1']},
        `sp2_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT {$defaults['sp2']},
        `sp3_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT {$defaults['sp3']},
        `pemberhentian_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT {$defaults['pemberhentian']},
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_pengaturan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $connect->query($createTableQuery);

    if (!app_table_exists($connect, 'pengaturan_peringatan')) {
        return;
    }

    $seedQuery = "INSERT INTO `pengaturan_peringatan` (`id_pengaturan`, `sp1_min_poin`, `sp2_min_poin`, `sp3_min_poin`, `pemberhentian_min_poin`)
        SELECT 1, {$defaults['sp1']}, {$defaults['sp2']}, {$defaults['sp3']}, {$defaults['pemberhentian']}
        WHERE NOT EXISTS (
            SELECT 1
            FROM `pengaturan_peringatan`
            WHERE `id_pengaturan` = 1
        )";
    $connect->query($seedQuery);
}

function app_warning_states(array $thresholds): array
{
    $states = [];

    foreach (app_warning_state_blueprints() as $key => $blueprint) {
        $states[$key] = array_merge($blueprint, [
            'min_points' => (int) ($thresholds[$key] ?? app_warning_default_thresholds()[$key]),
        ]);
    }

    return $states;
}

function app_warning_settings(mysqli $connect): array
{
    app_ensure_warning_settings_table($connect);

    $thresholds = app_warning_default_thresholds();
    $result = $connect->query('SELECT sp1_min_poin, sp2_min_poin, sp3_min_poin, pemberhentian_min_poin FROM pengaturan_peringatan WHERE id_pengaturan = 1 LIMIT 1');
    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $candidateThresholds = [
            'sp1' => (int) ($row['sp1_min_poin'] ?? $thresholds['sp1']),
            'sp2' => (int) ($row['sp2_min_poin'] ?? $thresholds['sp2']),
            'sp3' => (int) ($row['sp3_min_poin'] ?? $thresholds['sp3']),
            'pemberhentian' => (int) ($row['pemberhentian_min_poin'] ?? $thresholds['pemberhentian']),
        ];

        if (app_validate_warning_thresholds($candidateThresholds) === '') {
            $thresholds = $candidateThresholds;
        } elseif (app_validate_legacy_warning_thresholds($candidateThresholds) === '') {
            $thresholds = app_convert_legacy_warning_thresholds($candidateThresholds);
        }
    }

    return [
        'max_points' => app_warning_max_points(),
        'thresholds' => $thresholds,
        'states' => app_warning_states($thresholds),
    ];
}

function app_save_warning_settings(mysqli $connect, array $thresholds): bool
{
    if (app_validate_warning_thresholds($thresholds) !== '') {
        return false;
    }

    app_ensure_warning_settings_table($connect);

    $sp1 = (int) ($thresholds['sp1'] ?? 0);
    $sp2 = (int) ($thresholds['sp2'] ?? 0);
    $sp3 = (int) ($thresholds['sp3'] ?? 0);
    $pemberhentian = (int) ($thresholds['pemberhentian'] ?? 0);

    $query = "INSERT INTO `pengaturan_peringatan` (`id_pengaturan`, `sp1_min_poin`, `sp2_min_poin`, `sp3_min_poin`, `pemberhentian_min_poin`)
        VALUES (1, {$sp1}, {$sp2}, {$sp3}, {$pemberhentian})
        ON DUPLICATE KEY UPDATE
            `sp1_min_poin` = VALUES(`sp1_min_poin`),
            `sp2_min_poin` = VALUES(`sp2_min_poin`),
            `sp3_min_poin` = VALUES(`sp3_min_poin`),
            `pemberhentian_min_poin` = VALUES(`pemberhentian_min_poin`)";

    return (bool) $connect->query($query);
}

function app_validate_legacy_warning_thresholds(array $thresholds): string
{
    $maxPoints = app_warning_max_points();
    $previousValue = 0;

    foreach (app_warning_default_thresholds() as $key => $defaultValue) {
        $value = (int) ($thresholds[$key] ?? $defaultValue);

        if ($value < 1 || $value > $maxPoints) {
            return 'invalid';
        }

        if ($value <= $previousValue) {
            return 'invalid';
        }

        $previousValue = $value;
    }

    return '';
}

function app_convert_legacy_warning_thresholds(array $thresholds): array
{
    $maxPoints = app_warning_max_points();
    $minPoints = app_warning_min_points();
    $converted = [];

    foreach (app_warning_default_thresholds() as $key => $defaultValue) {
        $legacyValue = (int) ($thresholds[$key] ?? ($maxPoints - $defaultValue));
        $converted[$key] = max($minPoints, $maxPoints - $legacyValue);
    }

    return app_validate_warning_thresholds($converted) === ''
        ? $converted
        : app_warning_default_thresholds();
}

function app_cap_warning_points(int $points, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return max(app_warning_min_points(), min($points, $maxPoints));
}

function app_cap_warning_deduction_points(int $points, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return max(0, min($points, $maxPoints - app_warning_min_points()));
}

function app_points_after_warning_deduction(int $deductedPoints, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return app_cap_warning_points($maxPoints - app_cap_warning_deduction_points($deductedPoints, $maxPoints), $maxPoints);
}

function app_total_points_from_violations(array $violations): int
{
    $deductedPoints = 0;

    foreach ($violations as $violation) {
        $deductedPoints += (int) ($violation['poin_peraturan'] ?? 0);
    }

    return app_points_after_warning_deduction($deductedPoints);
}

function app_student_total_points(mysqli $connect, int $studentId): int
{
    if ($studentId <= 0) {
        return app_warning_max_points();
    }

    $studentId = (int) $studentId;
    $query = "SELECT COALESCE(SUM(peraturan.poin_peraturan), 0) AS deducted_points
        FROM pelanggaran
        LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan
        WHERE pelanggaran.id_siswa = '{$studentId}'";
    $result = $connect->query($query);
    $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

    return app_points_after_warning_deduction((int) ($row['deducted_points'] ?? 0));
}

function app_warning_points_remaining(int $currentPoints, ?int $maxPoints = null): int
{
    return app_warning_deductible_points($currentPoints, $maxPoints);
}

function app_warning_deductible_points(int $currentPoints, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return max(0, app_cap_warning_points($currentPoints, $maxPoints) - app_warning_min_points());
}

function app_warning_state_for_points(int $points, array $settings): ?array
{
    $maxPoints = (int) ($settings['max_points'] ?? app_warning_max_points());
    $points = app_cap_warning_points($points, $maxPoints);
    $states = $settings['states'] ?? app_warning_states(app_warning_default_thresholds());
    $activeState = null;

    foreach ($states as $state) {
        if ($points <= (int) ($state['min_points'] ?? $maxPoints)) {
            $activeState = $state;
        }
    }

    return $activeState;
}

function app_warning_state_badge_class(string $stateKey): string
{
    $states = app_warning_state_blueprints();

    return $states[$stateKey]['badge_class'] ?? 'badge-source';
}

function app_project_root(): string
{
    return dirname(__DIR__);
}

function app_warning_letter_templates(): array
{
    $baseDir = app_project_root() . '/storage/warning-letters/templates';

    return [
        'sp1' => [
            'key' => 'sp1',
            'label' => 'SP 1',
            'title' => 'Surat Peringatan 1',
            'template_path' => $baseDir . '/sp1.docx',
            'default_date_text' => 'Banjarmasin, 2 Februari 2026',
            'default_number_text' => '421.3/421/SMA.04/DIKBUD/2026',
            'search_names' => ['Siti Amaliah Ulaa', 'Siti Amalia Ulaa'],
            'search_classes' => ['kelas XI 9', 'Kelas XI'],
            'rank' => 1,
        ],
        'sp2' => [
            'key' => 'sp2',
            'label' => 'SP 2',
            'title' => 'Surat Peringatan 2',
            'template_path' => $baseDir . '/sp2.docx',
            'default_date_text' => 'Banjarmasin, 2 Februari 2026',
            'default_number_text' => '421.3/422/SMA.04/DIKBUD/2026',
            'search_names' => ['Siti Amaliah Ulaa', 'Siti Amalia Ulaa'],
            'search_classes' => ['kelas XI 9', 'Kelas XI'],
            'rank' => 2,
        ],
        'sp3' => [
            'key' => 'sp3',
            'label' => 'SP 3',
            'title' => 'Surat Peringatan 3',
            'template_path' => $baseDir . '/sp3.docx',
            'default_date_text' => 'Banjarmasin, 11 Februari 2026',
            'default_number_text' => '421.3/423/SMA.04/DIKBUD/2026',
            'search_names' => ['Siti Amaliah Ulaa', 'Siti Amalia Ulaa'],
            'search_classes' => ['kelas XI 9', 'Kelas XI'],
            'rank' => 3,
        ],
        'pemberhentian' => [
            'key' => 'pemberhentian',
            'label' => 'Pemberhentian',
            'title' => 'Surat Pemberhentian',
            'template_path' => $baseDir . '/pemberhentian.docx',
            'default_date_text' => 'Banjarmasin, 14 November 2025',
            'default_number_text' => '421.3/424/SMA.04/DIKBUD/2025',
            'search_names' => ['Siti Amalia Ulaa'],
            'rank' => 4,
        ],
    ];
}

function app_warning_letter_types_for_state(string $stateKey): array
{
    $templates = app_warning_letter_templates();
    $currentRank = (int) ($templates[$stateKey]['rank'] ?? 0);
    $availableTypes = [];

    foreach ($templates as $key => $template) {
        if ((int) ($template['rank'] ?? 0) <= $currentRank) {
            $availableTypes[$key] = $template;
        }
    }

    return $availableTypes;
}

function app_warning_letter_timezone(): DateTimeZone
{
    static $timezone = null;

    if (!$timezone instanceof DateTimeZone) {
        $timezone = new DateTimeZone('Asia/Makassar');
    }

    return $timezone;
}

function app_warning_letter_now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', app_warning_letter_timezone());
}

function app_warning_letter_month_label(int $month): string
{
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    return $months[$month] ?? 'Januari';
}

function app_warning_letter_date_text(DateTimeInterface $date): string
{
    return 'Banjarmasin, ' . $date->format('j') . ' ' . app_warning_letter_month_label((int) $date->format('n')) . ' ' . $date->format('Y');
}

function app_warning_letter_number(int $sequence, DateTimeInterface $date): string
{
    return '421.3/' . $sequence . '/SMA.04/DIKBUD/' . $date->format('Y');
}

function app_warning_letter_generated_dir(): string
{
    return app_project_root() . '/storage/warning-letters/generated';
}

function app_ensure_warning_letter_storage(): void
{
    $paths = [
        app_project_root() . '/storage/warning-letters',
        app_project_root() . '/storage/warning-letters/templates',
        app_warning_letter_generated_dir(),
    ];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }
    }
}

function app_ensure_warning_letters_table(mysqli $connect): void
{
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `surat_peringatan` (
        `id_surat` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_siswa` INT UNSIGNED NOT NULL,
        `jenis_surat` VARCHAR(32) NOT NULL,
        `state_siswa` VARCHAR(32) NOT NULL DEFAULT '',
        `no_urut_bulanan` INT UNSIGNED NOT NULL,
        `no_surat` VARCHAR(255) NOT NULL,
        `tanggal_surat` DATE NOT NULL,
        `bulan_surat` TINYINT UNSIGNED NOT NULL,
        `tahun_surat` SMALLINT UNSIGNED NOT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `created_by_name` VARCHAR(150) NOT NULL DEFAULT '',
        `created_by_role` VARCHAR(64) NOT NULL DEFAULT '',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_surat`),
        UNIQUE KEY `uniq_surat_peringatan_siswa_jenis` (`id_siswa`, `jenis_surat`),
        KEY `idx_surat_peringatan_periode` (`tahun_surat`, `bulan_surat`, `no_urut_bulanan`),
        KEY `idx_surat_peringatan_siswa` (`id_siswa`),
        CONSTRAINT `fk_surat_peringatan_siswa`
            FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $connect->query($createTableQuery);
}

function app_warning_letters_by_student_ids(mysqli $connect, array $studentIds): array
{
    app_ensure_warning_letters_table($connect);

    $studentIds = array_values(array_unique(array_filter(array_map('intval', $studentIds), static function ($value) {
        return $value > 0;
    })));

    if ($studentIds === []) {
        return [];
    }

    $idList = implode(', ', $studentIds);
    $query = "SELECT surat_peringatan.*, siswa.nama_lengkap
        FROM surat_peringatan
        LEFT JOIN siswa ON siswa.id_siswa = surat_peringatan.id_siswa
        WHERE surat_peringatan.id_siswa IN ({$idList})
        ORDER BY surat_peringatan.created_at DESC, surat_peringatan.id_surat DESC";
    $result = $connect->query($query);
    $letters = [];

    if (!$result instanceof mysqli_result) {
        return $letters;
    }

    while ($row = $result->fetch_assoc()) {
        $studentId = (int) ($row['id_siswa'] ?? 0);
        $letterType = (string) ($row['jenis_surat'] ?? '');
        if ($studentId <= 0 || $letterType === '') {
            continue;
        }

        $letters[$studentId][$letterType] = $row;
    }

    return $letters;
}

function app_warning_letter_find_by_student_and_type(mysqli $connect, int $studentId, string $letterType): ?array
{
    app_ensure_warning_letters_table($connect);

    if ($studentId <= 0 || $letterType === '') {
        return null;
    }

    $safeLetterType = $connect->real_escape_string($letterType);
    $query = "SELECT surat_peringatan.*, siswa.nama_lengkap
        FROM surat_peringatan
        LEFT JOIN siswa ON siswa.id_siswa = surat_peringatan.id_siswa
        WHERE surat_peringatan.id_siswa = '{$studentId}'
            AND surat_peringatan.jenis_surat = '{$safeLetterType}'
        LIMIT 1";
    $result = $connect->query($query);

    if (!$result instanceof mysqli_result || $result->num_rows === 0) {
        return null;
    }

    return $result->fetch_assoc();
}

function app_warning_letter_find_by_id(mysqli $connect, int $letterId): ?array
{
    app_ensure_warning_letters_table($connect);

    if ($letterId <= 0) {
        return null;
    }

    $query = "SELECT surat_peringatan.*, siswa.nama_lengkap
        FROM surat_peringatan
        LEFT JOIN siswa ON siswa.id_siswa = surat_peringatan.id_siswa
        WHERE surat_peringatan.id_surat = '{$letterId}'
        LIMIT 1";
    $result = $connect->query($query);

    if (!$result instanceof mysqli_result || $result->num_rows === 0) {
        return null;
    }

    return $result->fetch_assoc();
}

function app_warning_student_snapshot(mysqli $connect, int $studentId): ?array
{
    if ($studentId <= 0) {
        return null;
    }

    $query = "SELECT siswa.id_siswa, siswa.nisn, siswa.nama_lengkap, kelas.nama_kelas, COALESCE(SUM(peraturan.poin_peraturan), 0) AS poin_berkurang
        FROM siswa
        LEFT JOIN kelas ON kelas.id_kelas = siswa.id_kelas
        LEFT JOIN pelanggaran ON pelanggaran.id_siswa = siswa.id_siswa
        LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan
        WHERE siswa.id_siswa = '{$studentId}'
        GROUP BY siswa.id_siswa, siswa.nisn, siswa.nama_lengkap, kelas.nama_kelas
        LIMIT 1";
    $result = $connect->query($query);

    if (!$result instanceof mysqli_result || $result->num_rows === 0) {
        return null;
    }

    $row = $result->fetch_assoc();
    $warningSettings = app_warning_settings($connect);
    $row['poin_berkurang'] = app_cap_warning_deduction_points((int) ($row['poin_berkurang'] ?? 0), $warningSettings['max_points']);
    $row['total_poin'] = app_points_after_warning_deduction((int) $row['poin_berkurang'], $warningSettings['max_points']);
    $row['state'] = app_warning_state_for_points((int) $row['total_poin'], $warningSettings);

    return $row;
}

function app_warning_letter_next_monthly_sequence(mysqli $connect, int $year, int $month): int
{
    app_ensure_warning_letters_table($connect);

    $query = "SELECT COALESCE(MAX(no_urut_bulanan), 0) AS max_sequence
        FROM surat_peringatan
        WHERE tahun_surat = '{$year}'
            AND bulan_surat = '{$month}'";
    $result = $connect->query($query);
    $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

    return ((int) ($row['max_sequence'] ?? 0)) + 1;
}

function app_warning_letter_slug(string $value): string
{
    $slug = $value;

    if (function_exists('iconv')) {
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($converted !== false) {
            $slug = $converted;
        }
    }

    $slug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $slug));
    $slug = trim($slug, '-');

    return $slug !== '' ? $slug : 'siswa';
}

function app_warning_letter_absolute_path(string $filePath): string
{
    if ($filePath === '') {
        return '';
    }

    if (str_starts_with($filePath, '/')) {
        return $filePath;
    }

    return app_project_root() . '/' . ltrim($filePath, '/');
}

function app_warning_letter_download_name(array $letter): string
{
    $studentSlug = app_warning_letter_slug((string) ($letter['nama_lengkap'] ?? 'siswa'));
    $letterType = app_warning_letter_slug((string) ($letter['jenis_surat'] ?? 'surat'));
    $datePart = preg_replace('/[^0-9]/', '', (string) ($letter['tanggal_surat'] ?? ''));

    return 'surat-' . $letterType . '-' . $studentSlug . '-' . ($datePart !== '' ? $datePart : 'dokumen') . '.docx';
}

function app_warning_letter_paragraph_text(DOMXPath $xpath, DOMElement $paragraph): string
{
    $text = '';

    foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
        $text .= $textNode->textContent;
    }

    return $text;
}

function app_warning_letter_replace_first_in_paragraph(DOMXPath $xpath, DOMElement $paragraph, string $search, string $replace): bool
{
    if ($search === '') {
        return false;
    }

    $segments = [];
    $paragraphText = '';

    foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
        $nodeText = $textNode->textContent;
        $start = mb_strlen($paragraphText, 'UTF-8');
        $paragraphText .= $nodeText;
        $segments[] = [
            'node' => $textNode,
            'text' => $nodeText,
            'start' => $start,
            'end' => mb_strlen($paragraphText, 'UTF-8'),
        ];
    }

    if ($segments === []) {
        return false;
    }

    $matchStart = mb_strpos($paragraphText, $search, 0, 'UTF-8');
    if ($matchStart === false) {
        return false;
    }

    $matchEnd = $matchStart + mb_strlen($search, 'UTF-8');
    $firstIndex = null;
    $lastIndex = null;

    foreach ($segments as $index => $segment) {
        if ($segment['end'] <= $matchStart || $segment['start'] >= $matchEnd) {
            continue;
        }

        if ($firstIndex === null) {
            $firstIndex = $index;
        }

        $lastIndex = $index;
    }

    if ($firstIndex === null || $lastIndex === null) {
        return false;
    }

    if ($firstIndex === $lastIndex) {
        $segment = $segments[$firstIndex];
        $prefixLength = $matchStart - $segment['start'];
        $suffixStart = $matchEnd - $segment['start'];
        $prefix = mb_substr($segment['text'], 0, $prefixLength, 'UTF-8');
        $suffix = mb_substr($segment['text'], $suffixStart, null, 'UTF-8');
        $segment['node']->nodeValue = $prefix . $replace . $suffix;

        return true;
    }

    foreach ($segments as $index => $segment) {
        if ($index < $firstIndex || $index > $lastIndex) {
            continue;
        }

        if ($index === $firstIndex) {
            $prefixLength = $matchStart - $segment['start'];
            $prefix = mb_substr($segment['text'], 0, $prefixLength, 'UTF-8');
            $segment['node']->nodeValue = $prefix . $replace;
            continue;
        }

        if ($index === $lastIndex) {
            $suffixStart = $matchEnd - $segment['start'];
            $suffix = mb_substr($segment['text'], $suffixStart, null, 'UTF-8');
            $segment['node']->nodeValue = $suffix;
            continue;
        }

        $segment['node']->nodeValue = '';
    }

    return true;
}

function app_warning_letter_replace_all_in_document(DOMXPath $xpath, string $search, string $replace): int
{
    if ($search === '') {
        return 0;
    }

    $replacements = 0;
    foreach ($xpath->query('//w:p') as $paragraph) {
        if (!$paragraph instanceof DOMElement) {
            continue;
        }

        $paragraphText = app_warning_letter_paragraph_text($xpath, $paragraph);
        if ($paragraphText === '') {
            continue;
        }

        $occurrences = 0;
        $offset = 0;
        $searchLength = mb_strlen($search, 'UTF-8');

        while (($position = mb_strpos($paragraphText, $search, $offset, 'UTF-8')) !== false) {
            $occurrences++;
            $offset = $position + $searchLength;
        }

        for ($index = 0; $index < $occurrences; $index++) {
            if (app_warning_letter_replace_first_in_paragraph($xpath, $paragraph, $search, $replace)) {
                $replacements++;
            }
        }
    }

    return $replacements;
}

function app_warning_letter_generate_docx(array $template, array $payload): string
{
    app_ensure_warning_letter_storage();

    $templatePath = (string) ($template['template_path'] ?? '');
    if ($templatePath === '' || !is_file($templatePath)) {
        throw new RuntimeException('Template surat tidak ditemukan: ' . $templatePath);
    }

    $date = $payload['date'] ?? app_warning_letter_now();
    if (!$date instanceof DateTimeInterface) {
        throw new RuntimeException('Tanggal surat tidak valid.');
    }

    $targetDir = app_warning_letter_generated_dir() . '/' . $date->format('Y') . '/' . $date->format('m');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $studentSlug = app_warning_letter_slug((string) ($payload['student_name'] ?? 'siswa'));
    $targetRelativePath = 'storage/warning-letters/generated/' . $date->format('Y') . '/' . $date->format('m') . '/'
        . $date->format('YmdHis') . '-' . app_warning_letter_slug((string) ($template['key'] ?? 'surat')) . '-' . $studentSlug . '.docx';
    $targetPath = app_project_root() . '/' . $targetRelativePath;

    if (!copy($templatePath, $targetPath)) {
        throw new RuntimeException('Template surat gagal disalin.');
    }

    $zip = new ZipArchive();
    if ($zip->open($targetPath) !== true) {
        @unlink($targetPath);
        throw new RuntimeException('File surat hasil salinan tidak bisa dibuka.');
    }

    $documentXml = $zip->getFromName('word/document.xml');
    if ($documentXml === false) {
        $zip->close();
        @unlink($targetPath);
        throw new RuntimeException('Isi dokumen surat tidak ditemukan.');
    }

    $document = new DOMDocument();
    $document->preserveWhiteSpace = true;
    if (!$document->loadXML($documentXml)) {
        $zip->close();
        @unlink($targetPath);
        throw new RuntimeException('Template surat tidak bisa diproses.');
    }

    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    $dateReplaceCount = app_warning_letter_replace_all_in_document(
        $xpath,
        (string) ($template['default_date_text'] ?? ''),
        (string) ($payload['date_text'] ?? '')
    );
    $numberReplaceCount = app_warning_letter_replace_all_in_document(
        $xpath,
        (string) ($template['default_number_text'] ?? ''),
        (string) ($payload['letter_number'] ?? '')
    );
    $nameReplaceCount = 0;
    $classReplaceCount = 0;

    foreach ((array) ($template['search_names'] ?? []) as $searchName) {
        $nameReplaceCount += app_warning_letter_replace_all_in_document(
            $xpath,
            (string) $searchName,
            (string) ($payload['student_name'] ?? '')
        );
    }

    foreach ((array) ($template['search_classes'] ?? []) as $searchClass) {
        $replacementClass = trim((string) ($payload['student_class'] ?? ''));
        if (str_starts_with((string) $searchClass, 'kelas ')) {
            $replacementClass = 'kelas ' . $replacementClass;
        } elseif (str_starts_with((string) $searchClass, 'Kelas ')) {
            $replacementClass = 'Kelas ' . $replacementClass;
        }

        $classReplaceCount += app_warning_letter_replace_all_in_document(
            $xpath,
            (string) $searchClass,
            $replacementClass
        );
    }

    $requiresClassReplacement = ((array) ($template['search_classes'] ?? [])) !== [];
    if ($dateReplaceCount < 1 || $numberReplaceCount < 1 || $nameReplaceCount < 1 || ($requiresClassReplacement && $classReplaceCount < 1)) {
        $zip->close();
        @unlink($targetPath);
        throw new RuntimeException('Template surat berubah dan tidak bisa dipetakan otomatis.');
    }

    if ($zip->addFromString('word/document.xml', $document->saveXML()) === false) {
        $zip->close();
        @unlink($targetPath);
        throw new RuntimeException('File surat gagal diperbarui.');
    }

    $zip->close();

    return $targetRelativePath;
}

function app_warning_letter_create(mysqli $connect, int $studentId, string $letterType, array $session): array
{
    app_ensure_warning_letters_table($connect);

    $templates = app_warning_letter_templates();
    $template = $templates[$letterType] ?? null;
    if ($template === null) {
        throw new RuntimeException('Jenis surat tidak valid.');
    }

    $student = app_warning_student_snapshot($connect, $studentId);
    if ($student === null) {
        throw new RuntimeException('Data siswa tidak ditemukan.');
    }

    $studentState = $student['state'] ?? null;
    if (!is_array($studentState) || ($studentState['key'] ?? '') === '') {
        throw new RuntimeException('Siswa belum masuk state peringatan yang bisa dibuatkan surat.');
    }

    $allowedTypes = app_warning_letter_types_for_state((string) $studentState['key']);
    if (!isset($allowedTypes[$letterType])) {
        throw new RuntimeException('Surat ' . ($template['label'] ?? $letterType) . ' belum bisa dibuat untuk state siswa saat ini.');
    }

    $existingLetter = app_warning_letter_find_by_student_and_type($connect, $studentId, $letterType);
    if ($existingLetter !== null) {
        return [
            'status' => 'existing',
            'letter' => $existingLetter,
            'template' => $template,
            'student' => $student,
        ];
    }

    $date = app_warning_letter_now();
    $sequence = app_warning_letter_next_monthly_sequence($connect, (int) $date->format('Y'), (int) $date->format('n'));
    $letterNumber = app_warning_letter_number($sequence, $date);
    $dateText = app_warning_letter_date_text($date);
    $generatedPath = app_warning_letter_generate_docx($template, [
        'date' => $date,
        'date_text' => $dateText,
        'letter_number' => $letterNumber,
        'student_name' => (string) ($student['nama_lengkap'] ?? ''),
        'student_class' => trim((string) ($student['nama_kelas'] ?? '')),
    ]);

    $safeLetterType = $connect->real_escape_string($letterType);
    $safeState = $connect->real_escape_string((string) ($studentState['key'] ?? ''));
    $safeLetterNumber = $connect->real_escape_string($letterNumber);
    $safeGeneratedPath = $connect->real_escape_string($generatedPath);
    $safeCreatedByName = $connect->real_escape_string((string) ($session['nama_lengkap'] ?? ''));
    $safeCreatedByRole = $connect->real_escape_string(app_user_role_label($session));
    $tanggalSurat = $date->format('Y-m-d');
    $bulanSurat = (int) $date->format('n');
    $tahunSurat = (int) $date->format('Y');

    $insertQuery = "INSERT INTO surat_peringatan (
            id_siswa,
            jenis_surat,
            state_siswa,
            no_urut_bulanan,
            no_surat,
            tanggal_surat,
            bulan_surat,
            tahun_surat,
            file_path,
            created_by_name,
            created_by_role
        ) VALUES (
            '{$studentId}',
            '{$safeLetterType}',
            '{$safeState}',
            '{$sequence}',
            '{$safeLetterNumber}',
            '{$tanggalSurat}',
            '{$bulanSurat}',
            '{$tahunSurat}',
            '{$safeGeneratedPath}',
            '{$safeCreatedByName}',
            '{$safeCreatedByRole}'
        )";

    if (!$connect->query($insertQuery)) {
        @unlink(app_warning_letter_absolute_path($generatedPath));

        if ((int) $connect->errno === 1062) {
            $existingLetter = app_warning_letter_find_by_student_and_type($connect, $studentId, $letterType);
            if ($existingLetter !== null) {
                return [
                    'status' => 'existing',
                    'letter' => $existingLetter,
                    'template' => $template,
                    'student' => $student,
                ];
            }
        }

        throw new RuntimeException('Data surat gagal disimpan ke database.');
    }

    $createdLetter = app_warning_letter_find_by_id($connect, (int) $connect->insert_id);
    if ($createdLetter === null) {
        throw new RuntimeException('Surat berhasil dibuat, tetapi data hasil simpan tidak ditemukan.');
    }

    return [
        'status' => 'created',
        'letter' => $createdLetter,
        'template' => $template,
        'student' => $student,
    ];
}

function app_warning_letter_regenerate(mysqli $connect, int $letterId): array
{
    app_ensure_warning_letters_table($connect);

    $letter = app_warning_letter_find_by_id($connect, $letterId);
    if ($letter === null) {
        throw new RuntimeException('Surat yang akan diregenerate tidak ditemukan.');
    }

    $templates = app_warning_letter_templates();
    $letterType = (string) ($letter['jenis_surat'] ?? '');
    $template = $templates[$letterType] ?? null;
    if ($template === null) {
        throw new RuntimeException('Template surat untuk data ini tidak ditemukan.');
    }

    $studentId = (int) ($letter['id_siswa'] ?? 0);
    $student = app_warning_student_snapshot($connect, $studentId);
    if ($student === null) {
        throw new RuntimeException('Data siswa untuk surat ini tidak ditemukan.');
    }

    $letterDateValue = trim((string) ($letter['tanggal_surat'] ?? ''));
    $letterDate = DateTimeImmutable::createFromFormat('Y-m-d', $letterDateValue, app_warning_letter_timezone());
    if (!$letterDate instanceof DateTimeImmutable) {
        throw new RuntimeException('Tanggal surat lama tidak valid.');
    }

    $generatedPath = app_warning_letter_generate_docx($template, [
        'date' => $letterDate,
        'date_text' => app_warning_letter_date_text($letterDate),
        'letter_number' => (string) ($letter['no_surat'] ?? ''),
        'student_name' => (string) ($student['nama_lengkap'] ?? ''),
        'student_class' => trim((string) ($student['nama_kelas'] ?? '')),
    ]);

    $safeGeneratedPath = $connect->real_escape_string($generatedPath);
    $updateQuery = "UPDATE surat_peringatan
        SET file_path = '{$safeGeneratedPath}'
        WHERE id_surat = '{$letterId}'
        LIMIT 1";

    if (!$connect->query($updateQuery)) {
        @unlink(app_warning_letter_absolute_path($generatedPath));
        throw new RuntimeException('File surat berhasil diregenerate, tetapi data database gagal diperbarui.');
    }

    $oldPath = app_warning_letter_absolute_path((string) ($letter['file_path'] ?? ''));
    $newPath = app_warning_letter_absolute_path($generatedPath);
    if ($oldPath !== '' && $oldPath !== $newPath && is_file($oldPath)) {
        @unlink($oldPath);
    }

    $updatedLetter = app_warning_letter_find_by_id($connect, $letterId);
    if ($updatedLetter === null) {
        throw new RuntimeException('Data surat hasil regenerate tidak ditemukan.');
    }

    return [
        'status' => 'regenerated',
        'letter' => $updatedLetter,
        'template' => $template,
        'student' => $student,
    ];
}
