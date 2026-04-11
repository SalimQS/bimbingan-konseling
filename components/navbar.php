<?php
$sourceSummary = app_student_source_summary();
?>
<nav class="navbar topbar">
  <div class="container-fluid">
    <div class="topbar-left">
      <button type="button" id="sidebarCollapse" class="btn btn-icon">
        <i class="fas fa-bars"></i>
      </button>
      <div class="topbar-context">
        <span class="topbar-label">Akses saat ini</span>
        <strong><?= app_h(app_user_role_label($_SESSION)) ?></strong>
      </div>
    </div>

    <div class="topbar-right">
      <?php if (!empty($sourceSummary['synced_at'])) : ?>
        <span class="topbar-badge">
                    <i class="fas fa-cloud-download-alt"></i>
          Upload <?= app_h(date('d M H:i', strtotime($sourceSummary['synced_at']))) ?>
        </span>
      <?php endif; ?>

      <div class="dropdown">
        <button class="btn user-menu" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="user-menu-avatar"><?= app_h(strtoupper(substr((string) $_SESSION['nama_lengkap'], 0, 1))) ?></span>
          <span><?= app_h($_SESSION['nama_lengkap']) ?></span>
          <i class="fas fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li class="dropdown-item-text">
            <small><?= app_h(app_user_role_label($_SESSION)) ?></small>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
