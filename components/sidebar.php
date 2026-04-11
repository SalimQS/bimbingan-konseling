<?php
$menuItems = app_sidebar_menu_items($_SESSION);
$sourceSummary = app_student_source_summary();
?>
<nav id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-brand">
            <span class="sidebar-brand-mark">BK</span>
            <div>
                <h1>Student Care</h1>
                <p>Sistem bimbingan konseling</p>
            </div>
        </a>
    </div>

    <ul class="sidebar-menu">
        <?php foreach ($menuItems as $menuItem) : ?>
            <?php
            $targetPage = '';
            $targetQuery = parse_url($menuItem['href'], PHP_URL_QUERY);
            if ($targetQuery) {
                parse_str($targetQuery, $targetParams);
                $targetPage = $targetParams['page'] ?? '';
            }

            $isActive = ($targetPage === '' && $page === '') || ($targetPage !== '' && $page === $targetPage);
            ?>
            <li class="<?= $isActive ? 'active' : '' ?>">
                <a href="<?= app_h($menuItem['href']) ?>">
                    <i class="fas <?= app_h($menuItem['icon']) ?>"></i>
                    <span><?= app_h($menuItem['label']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-footer">
        <span>Roster aktif</span>
        <strong><?= app_h($sourceSummary['source_file']) ?></strong>
    </div>
</nav>
