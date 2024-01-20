<nav id="sidebar">
    <div class="sidebar-header">
        <a href="index.php">
            <h1><span class='text-white fw-bold'>MY</span>Point</h1>
            <strong><span class='text-white'>M</span>P</strong>
        </a>
    </div>
    <ul class="list-unstyled components">
        <li class="<?= !isset($page) ? 'active' : $page === ('' ? 'active' : '') ?>">
            <a href="index.php">
            <div class="row">
                <div class='col-12 col-md-2 sidebar-item-icon' >
                    <i class="fas fa-home"></i>
                </div>
                <span class="col-10">Dashboard</span>
            </div>
            </a>
        </li>
        <?php
            $menuAdmin = [
                [
                    "label" => "Siswa",
                    "icon" => "fa-graduation-cap",
                    "href" => "siswa",
                ],
                [
                    "label" => "Guru",
                    "icon" => "fa-graduation-cap",
                    "href" => "guru",
                ],
                [
                    "label" => "Kelas",
                    "icon" => "fa-graduation-cap",
                    "href" => "kelas",
                ],
                [
                    "label" => "Peraturan",
                    "icon" => "fa-list-ul",
                    "href" => "peraturan",
                ],
                [
                    "label" => "Peringatan",
                    "icon" => "fa-exclamation-circle",
                    "href" => "peringatan",
                ],
                [
                    "label" => "Account",
                    "icon" => "fa-user",
                    "href" => "user",
                ],
            ];
            if($_SESSION['role'] == 'admin' && $_SESSION['level'] == 'petugas') {
                foreach ($menuAdmin as $menu) {
        ?>
        <li class="<?= $page === $menu['href'] ? 'active' : '' ?>">
            <a href="index.php?page=<?=$menu['href']?>">
                <div class="row">
                    <div class='col-12 col-md-2 sidebar-item-icon' >
                        <i class="fas <?=$menu['icon']?>"></i>
                    </div>
                    <span class="col-10"><?=$menu['label']?></span>
                </div>
            </a>
        </li>
        
        <?php
                }
            }
            $menuNotAdmin = [
                [
                    "label" => "Siswa",
                    "icon" => "fa-graduation-cap",
                    "href" => "siswa",
                ],
                [
                    "label" => "Guru",
                    "icon" => "fa-graduation-cap",
                    "href" => "guru",
                ],
                [
                    "label" => "Kelas",
                    "icon" => "fa-graduation-cap",
                    "href" => "kelas",
                ],
                [
                    "label" => "Peraturan",
                    "icon" => "fa-list-ul",
                    "href" => "peraturan",
                ],
                [
                    "label" => "Peringatan",
                    "icon" => "fa-exclamation-circle",
                    "href" => "peringatan",
                ],
            ];
            if($_SESSION['role'] == 'admin' && ($_SESSION['level'] == 'kepsek' || $_SESSION['level'] == 'bk')) {
                foreach ($menuNotAdmin as $menu) {
        ?>
        <li class="<?= $page === $menu['href'] ? 'active' : '' ?>">
            <a href="index.php?page=<?=$menu['href']?>">
                <div class="row">
                    <div class='col-12 col-md-2 sidebar-item-icon' >
                        <i class="fas <?=$menu['icon']?>"></i>
                    </div>
                    <span class="col-10"><?=$menu['label']?></span>
                </div>
            </a>
        </li>
        
        <?php
                }
            }
            $menuWaliKelas = [
                [
                    "label" => "Siswa",
                    "icon" => "fa-graduation-cap",
                    "href" => "siswa",
                ],
                [
                    "label" => "Peraturan",
                    "icon" => "fa-list-ul",
                    "href" => "peraturan",
                ],
                [
                    "label" => "Data Saya",
                    "icon" => "fa-user",
                    "href" => "profile",
                ],
            ];
            if($_SESSION['role'] == 'guru' && $_SESSION['level'] == 'walikelas') {
                foreach ($menuWaliKelas as $menu) {
        ?>
        <li class="<?= $page === $menu['href'] ? 'active' : '' ?>">
            <a href="index.php?page=<?=$menu['href']?>">
                <div class="row">
                    <div class='col-12 col-md-2 sidebar-item-icon' >
                        <i class="fas <?=$menu['icon']?>"></i>
                    </div>
                    <span class="col-10"><?=$menu['label']?></span>
                </div>
            </a>
        </li>
        
        <?php
                }
            }
            $menuGuru = [
                [
                    "label" => "Data Saya",
                    "icon" => "fa-user",
                    "href" => "profile",
                ],
            ];
            if($_SESSION['role'] == 'guru' && $_SESSION['level'] == 'guru') {
                foreach ($menuGuru as $menu) {
        ?>
        <li class="<?= $page === $menu['href'] ? 'active' : '' ?>">
            <a href="index.php?page=<?=$menu['href']?>">
                <div class="row">
                    <div class='col-12 col-md-2 sidebar-item-icon' >
                        <i class="fas <?=$menu['icon']?>"></i>
                    </div>
                    <span class="col-10"><?=$menu['label']?></span>
                </div>
            </a>
        </li>
        
        <?php
                }
            }
        ?>
    </ul>

</nav>