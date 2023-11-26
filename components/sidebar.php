<nav id="sidebar">
    <div class="sidebar-header">
        <a href="index.php">
            <h3><span class='text-white fw-bold'>Bimbingan</span> Konseling</h3>
            <strong><span class='text-white'>B</span>K</strong>
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
                    "label" => "Data Siswa",
                    "icon" => "fa-users",
                    "href" => "siswa",
                ],
                [
                    "label" => "Data Guru",
                    "icon" => "fa-chalkboard-teacher",
                    "href" => "guru",
                ],
                [
                    "label" => "List Pelanggaran",
                    "icon" => "fa-list-ul",
                    "href" => "list",
                ],
                [
                    "label" => "Log Pelanggar",
                    "icon" => "fa-exclamation-circle",
                    "href" => "pelanggar",
                ],
                [
                    "label" => "List Peringatan",
                    "icon" => "fa-exclamation-circle",
                    "href" => "peringatan",
                ],
            ];
            if($_SESSION['role'] == 'admin') {
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
            $menuGuru = [
                [
                    "label" => "Data Siswa",
                    "icon" => "fa-users",
                    "href" => "siswa",
                ],
                [
                    "label" => "List Pelanggaran",
                    "icon" => "fa-list-ul",
                    "href" => "list",
                ],
                [
                    "label" => "Log Pelanggar",
                    "icon" => "fa-exclamation-circle",
                    "href" => "pelanggar",
                ]
            ];
            if($_SESSION['role'] == 'user' && $_SESSION['posisi'] == 'guru') {
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
            $menuSiswa = [
                [
                    "label" => "Data Mata Kuliah",
                    "icon" => "fa-clipboard-list",
                    "href" => "matkul",
                ],
            ];
            if($_SESSION['role'] == 'user' && $_SESSION['posisi'] == 'murid') {
                foreach ($menuSiswa as $menu) {
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