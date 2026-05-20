<?php
$menuItems = app_sidebar_menu_items($_SESSION);
?>
<nav id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-brand">
            <span class="sidebar-brand-mark">
                <img src="assets/img/icon.png" alt="Bimbingan Konseling">
            </span>
            <div>
                <h1>SMAN 4 Banjarmasin</h1>
                <p>Sistem Kesiswaan</p>
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
</nav>
