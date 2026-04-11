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

function app_warning_default_thresholds(): array
{
    return [
        'sp1' => 50,
        'sp2' => 100,
        'sp3' => 150,
        'pemberhentian' => 200,
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
    $previousValue = 0;
    $previousLabel = '';

    foreach (app_warning_default_thresholds() as $key => $defaultValue) {
        $value = (int) ($thresholds[$key] ?? $defaultValue);

        if ($value < 1) {
            return 'Batas poin ' . $labels[$key] . ' harus minimal 1.';
        }

        if ($value > $maxPoints) {
            return 'Batas poin ' . $labels[$key] . ' tidak boleh lebih dari ' . $maxPoints . '.';
        }

        if ($previousLabel !== '' && $value <= $previousValue) {
            return 'Batas poin ' . $labels[$key] . ' harus lebih besar dari ' . $previousLabel . '.';
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

function app_cap_warning_points(int $points, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return max(0, min($points, $maxPoints));
}

function app_total_points_from_violations(array $violations): int
{
    $totalPoints = 0;

    foreach ($violations as $violation) {
        $totalPoints += (int) ($violation['poin_peraturan'] ?? 0);
    }

    return app_cap_warning_points($totalPoints);
}

function app_student_total_points(mysqli $connect, int $studentId): int
{
    if ($studentId <= 0) {
        return 0;
    }

    $studentId = (int) $studentId;
    $query = "SELECT COALESCE(SUM(peraturan.poin_peraturan), 0) AS total_points
        FROM pelanggaran
        LEFT JOIN peraturan ON peraturan.id_peraturan = pelanggaran.id_peraturan
        WHERE pelanggaran.id_siswa = '{$studentId}'";
    $result = $connect->query($query);
    $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

    return app_cap_warning_points((int) ($row['total_points'] ?? 0));
}

function app_warning_points_remaining(int $currentPoints, ?int $maxPoints = null): int
{
    $maxPoints = $maxPoints ?? app_warning_max_points();

    return max(0, $maxPoints - app_cap_warning_points($currentPoints, $maxPoints));
}

function app_warning_state_for_points(int $points, array $settings): ?array
{
    $maxPoints = (int) ($settings['max_points'] ?? app_warning_max_points());
    $points = app_cap_warning_points($points, $maxPoints);
    $states = $settings['states'] ?? app_warning_states(app_warning_default_thresholds());
    $activeState = null;

    foreach ($states as $state) {
        if ($points >= (int) ($state['min_points'] ?? 0)) {
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
