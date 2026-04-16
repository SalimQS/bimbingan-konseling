CREATE DATABASE IF NOT EXISTS `bimbingan_konseling`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bimbingan_konseling`;

CREATE TABLE IF NOT EXISTS `berita` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `berita` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_admin` VARCHAR(150) NOT NULL,
  `username_admin` VARCHAR(100) NOT NULL,
  `password_admin` VARCHAR(255) NOT NULL,
  `level_admin` VARCHAR(32) NOT NULL DEFAULT 'petugas',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `uniq_admin_username` (`username_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nuptk` VARCHAR(32) DEFAULT NULL,
  `nama_guru` VARCHAR(150) NOT NULL,
  `username_guru` VARCHAR(100) NOT NULL,
  `password_guru` VARCHAR(255) NOT NULL,
  `foto_guru` VARCHAR(255) NOT NULL DEFAULT 'assets/img/default.jpg',
  `jenis_kelamin` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `no_telepon` VARCHAR(32) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `uniq_guru_username` (`username_guru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kelas` VARCHAR(64) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kelas`),
  UNIQUE KEY `uniq_kelas_nama` (`nama_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `siswa` (
  `id_siswa` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_kelas` INT UNSIGNED NOT NULL,
  `nisn` VARCHAR(20) NOT NULL,
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `nama_ibu` VARCHAR(150) NOT NULL DEFAULT '',
  `foto_siswa` VARCHAR(255) NOT NULL DEFAULT 'assets/img/default.jpg',
  `jenis_kelamin` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `tempat_lahir` VARCHAR(100) NOT NULL DEFAULT '',
  `tanggal_lahir` DATE DEFAULT NULL,
  `agama` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `alamat` TEXT NOT NULL,
  `no_telepon` VARCHAR(32) NOT NULL DEFAULT '',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `uniq_siswa_nisn` (`nisn`),
  KEY `idx_siswa_kelas` (`id_kelas`),
  CONSTRAINT `fk_siswa_kelas`
    FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `peraturan` (
  `id_peraturan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenis_peraturan` TEXT NOT NULL,
  `poin_peraturan` TINYINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_peraturan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pelanggaran` (
  `id_pelanggaran` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_siswa` INT UNSIGNED NOT NULL,
  `id_peraturan` INT UNSIGNED NOT NULL,
  `tanggal_pelanggaran` DATE NOT NULL,
  `tempat_pelanggaran` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pelanggaran`),
  KEY `idx_pelanggaran_siswa` (`id_siswa`),
  KEY `idx_pelanggaran_peraturan` (`id_peraturan`),
  KEY `idx_pelanggaran_tanggal` (`tanggal_pelanggaran`),
  CONSTRAINT `fk_pelanggaran_siswa`
    FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pelanggaran_peraturan`
    FOREIGN KEY (`id_peraturan`) REFERENCES `peraturan` (`id_peraturan`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pengaturan_peringatan` (
  `id_pengaturan` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `sp1_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT 50,
  `sp2_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT 100,
  `sp3_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT 150,
  `pemberhentian_min_poin` TINYINT UNSIGNED NOT NULL DEFAULT 200,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengaturan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surat_peringatan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin` (`nama_admin`, `username_admin`, `password_admin`, `level_admin`)
SELECT 'Administrator', 'admin', 'admin123', 'petugas'
WHERE NOT EXISTS (
  SELECT 1
  FROM `admin`
  WHERE `username_admin` = 'admin'
);

INSERT INTO `guru` (`nuptk`, `nama_guru`, `username_guru`, `password_guru`, `foto_guru`, `jenis_kelamin`, `no_telepon`)
SELECT 'GURU001', 'Guru Default', 'guru', 'guru123', 'assets/img/default.jpg', 0, '081234567890'
WHERE NOT EXISTS (
  SELECT 1
  FROM `guru`
  WHERE `username_guru` = 'guru'
);

INSERT INTO `pengaturan_peringatan` (`id_pengaturan`, `sp1_min_poin`, `sp2_min_poin`, `sp3_min_poin`, `pemberhentian_min_poin`)
SELECT 1, 50, 100, 150, 200
WHERE NOT EXISTS (
  SELECT 1
  FROM `pengaturan_peringatan`
  WHERE `id_pengaturan` = 1
);
