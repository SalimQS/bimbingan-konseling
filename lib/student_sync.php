<?php

function app_student_upload_directory(): string
{
    return dirname(__DIR__) . '/storage/student-uploads';
}

function app_peraturan_upload_directory(): string
{
    return dirname(__DIR__) . '/storage/peraturan-uploads';
}

function app_student_sync_snapshot_path(): string
{
    return dirname(__DIR__) . '/storage/cache/student-import.json';
}

function app_legacy_student_sync_snapshot_path(): string
{
    return dirname(__DIR__) . '/storage/cache/student-sync.json';
}

function app_read_student_sync_snapshot(): array
{
    $snapshotPaths = [
        app_student_sync_snapshot_path(),
        app_legacy_student_sync_snapshot_path(),
    ];

    foreach ($snapshotPaths as $snapshotPath) {
        if (!is_file($snapshotPath)) {
            continue;
        }

        $raw = file_get_contents($snapshotPath);
        if ($raw === false || $raw === '') {
            continue;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return [];
}

function app_store_uploaded_student_workbook(array $uploadedFile): array
{
    if (!isset($uploadedFile['error']) || (int) $uploadedFile['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload file siswa gagal. Pilih ulang file Excel yang valid.');
    }

    $originalName = trim((string) ($uploadedFile['name'] ?? ''));
    if ($originalName === '') {
        throw new RuntimeException('Nama file upload tidak valid.');
    }

    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== 'xlsx') {
        throw new RuntimeException('File siswa harus berformat .xlsx.');
    }

    $tmpName = (string) ($uploadedFile['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Berkas upload tidak dikenali oleh server.');
    }

    $uploadDir = app_student_upload_directory();
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Folder upload siswa tidak dapat dibuat.');
    }

    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $baseName);
    $safeBaseName = trim((string) $safeBaseName, '-');
    if ($safeBaseName === '') {
        $safeBaseName = 'data-siswa';
    }

    $targetPath = $uploadDir . '/' . date('Ymd-His') . '-' . $safeBaseName . '.xlsx';
    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('File Excel gagal disimpan ke server.');
    }

    return [
        'path' => $targetPath,
        'original_name' => $originalName,
    ];
}

function app_store_uploaded_peraturan_workbook(array $uploadedFile): array
{
    if (!isset($uploadedFile['error']) || (int) $uploadedFile['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload file peraturan gagal. Pilih ulang file Excel yang valid.');
    }

    $originalName = trim((string) ($uploadedFile['name'] ?? ''));
    if ($originalName === '') {
        throw new RuntimeException('Nama file upload tidak valid.');
    }

    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== 'xlsx') {
        throw new RuntimeException('File peraturan harus berformat .xlsx.');
    }

    $tmpName = (string) ($uploadedFile['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Berkas upload tidak dikenali oleh server.');
    }

    $uploadDir = app_peraturan_upload_directory();
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Folder upload peraturan tidak dapat dibuat.');
    }

    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $baseName);
    $safeBaseName = trim((string) $safeBaseName, '-');
    if ($safeBaseName === '') {
        $safeBaseName = 'data-peraturan';
    }

    $targetPath = $uploadDir . '/' . date('Ymd-His') . '-' . $safeBaseName . '.xlsx';
    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('File Excel gagal disimpan ke server.');
    }

    return [
        'path' => $targetPath,
        'original_name' => $originalName,
    ];
}

function app_import_students_from_workbook(mysqli $connect, string $workbookPath, ?string $originalName = null): array
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('Ekstensi ZipArchive tidak tersedia di server.');
    }

    if (!is_file($workbookPath)) {
        throw new RuntimeException('File workbook siswa tidak ditemukan.');
    }

    if (!app_table_exists($connect, 'siswa') || !app_table_exists($connect, 'kelas')) {
        throw new RuntimeException('Tabel siswa atau kelas belum tersedia di database.');
    }

    $students = app_parse_student_source($workbookPath);
    if ($students === []) {
        throw new RuntimeException('Tidak ada data siswa valid yang ditemukan di file Excel.');
    }

    $sourceHash = hash_file('sha256', $workbookPath) ?: '';
    app_apply_student_sync($connect, $students);

    return app_write_student_sync_snapshot($workbookPath, $students, $sourceHash, $originalName);
}

function app_import_peraturan_from_workbook(mysqli $connect, string $workbookPath, ?string $originalName = null): array
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('Ekstensi ZipArchive tidak tersedia di server.');
    }

    if (!is_file($workbookPath)) {
        throw new RuntimeException('File workbook peraturan tidak ditemukan.');
    }

    if (!app_table_exists($connect, 'peraturan')) {
        throw new RuntimeException('Tabel peraturan belum tersedia di database.');
    }

    $rules = app_parse_peraturan_source($workbookPath);
    if ($rules === []) {
        throw new RuntimeException('Tidak ada data peraturan valid yang ditemukan di file Excel.');
    }

    $sourceHash = hash_file('sha256', $workbookPath) ?: '';
    $summary = app_apply_peraturan_sync($connect, $rules);

    return [
        'source_path' => $workbookPath,
        'original_name' => $originalName,
        'source_hash' => $sourceHash,
        'summary' => $summary,
    ];
}

function app_parse_student_source(string $sourcePath): array
{
    $zip = new ZipArchive();
    if ($zip->open($sourcePath) !== true) {
        return [];
    }

    $sharedStrings = app_xlsx_shared_strings($zip);
    $worksheetPath = app_xlsx_first_sheet_path($zip);
    if ($worksheetPath === null) {
        $zip->close();
        return [];
    }

    $worksheetXml = $zip->getFromName($worksheetPath);
    $zip->close();

    if ($worksheetXml === false) {
        return [];
    }

    $worksheet = @simplexml_load_string($worksheetXml);
    if ($worksheet === false) {
        return [];
    }

    $worksheet->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $rows = $worksheet->xpath('//main:sheetData/main:row');
    if ($rows === false || $rows === []) {
        return [];
    }

    $headers = [];
    $students = [];

    foreach ($rows as $rowIndex => $row) {
        $row->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $cells = $row->xpath('main:c');
        if ($cells === false) {
            continue;
        }

        $valuesByColumn = [];
        foreach ($cells as $cell) {
            $cellReference = (string) ($cell['r'] ?? '');
            $columnReference = preg_replace('/\d+/', '', $cellReference);
            if ($columnReference === '') {
                continue;
            }

            $valuesByColumn[$columnReference] = app_xlsx_cell_value($cell, $sharedStrings);
        }

        if ($rowIndex === 0) {
            foreach ($valuesByColumn as $columnReference => $header) {
                $headers[$columnReference] = app_normalize_source_header($header);
            }
            continue;
        }

        $rawRow = [];
        foreach ($headers as $columnReference => $header) {
            if ($header === '') {
                continue;
            }

            $rawRow[$header] = trim((string) ($valuesByColumn[$columnReference] ?? ''));
        }

        $student = app_normalize_student_row($rawRow);
        if ($student === null) {
            continue;
        }

        $students[$student['nisn']] = $student;
    }

    return array_values($students);
}

function app_parse_peraturan_source(string $sourcePath): array
{
    $zip = new ZipArchive();
    if ($zip->open($sourcePath) !== true) {
        return [];
    }

    $sharedStrings = app_xlsx_shared_strings($zip);
    $worksheetPath = app_xlsx_first_sheet_path($zip);
    if ($worksheetPath === null) {
        $zip->close();
        return [];
    }

    $worksheetXml = $zip->getFromName($worksheetPath);
    $zip->close();

    if ($worksheetXml === false) {
        return [];
    }

    $worksheet = @simplexml_load_string($worksheetXml);
    if ($worksheet === false) {
        return [];
    }

    $worksheet->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $rows = $worksheet->xpath('//main:sheetData/main:row');
    if ($rows === false || $rows === []) {
        return [];
    }

    $headers = [];
    $rules = [];
    $dedupe = [];

    foreach ($rows as $rowIndex => $row) {
        $row->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $cells = $row->xpath('main:c');
        if ($cells === false) {
            continue;
        }

        $valuesByColumn = [];
        foreach ($cells as $cell) {
            $cellReference = (string) ($cell['r'] ?? '');
            $columnReference = preg_replace('/\d+/', '', $cellReference);
            if ($columnReference === '') {
                continue;
            }

            $valuesByColumn[$columnReference] = app_xlsx_cell_value($cell, $sharedStrings);
        }

        if ($rowIndex === 0) {
            foreach ($valuesByColumn as $columnReference => $header) {
                $headers[$columnReference] = app_normalize_peraturan_header($header);
            }
            continue;
        }

        $rawRow = [];
        foreach ($headers as $columnReference => $header) {
            if ($header === '') {
                continue;
            }

            $rawRow[$header] = trim((string) ($valuesByColumn[$columnReference] ?? ''));
        }

        $rule = app_normalize_peraturan_row($rawRow);
        if ($rule === null) {
            continue;
        }

        $key = strtolower(trim($rule['jenis_peraturan']));
        if ($key === '') {
            continue;
        }

        $dedupe[$key] = $rule;
    }

    return array_values($dedupe);
}

function app_normalize_peraturan_header(string $header): string
{
    $header = strtolower(trim($header));
    $header = str_replace(['(', ')', '.', '-', '/'], ' ', $header);
    $header = preg_replace('/\s+/', ' ', $header);

    $map = [
        'jenis peraturan' => 'jenis_peraturan',
        'nama peraturan' => 'jenis_peraturan',
        'peraturan' => 'jenis_peraturan',
        'pelanggaran' => 'jenis_peraturan',
        'jenis pelanggaran' => 'jenis_peraturan',
        'sanksi' => 'poin_peraturan',
        'poin' => 'poin_peraturan',
        'poin pengurang' => 'poin_peraturan',
        'pengurang poin' => 'poin_peraturan',
        'nilai' => 'poin_peraturan',
        'nilai poin' => 'poin_peraturan',
    ];

    return $map[$header] ?? '';
}

function app_normalize_peraturan_row(array $row): ?array
{
    $ruleName = trim((string) ($row['jenis_peraturan'] ?? ''));
    $pointValue = trim((string) ($row['poin_peraturan'] ?? ''));

    if ($ruleName === '' || $pointValue === '') {
        return null;
    }

    $pointValue = preg_replace('/[^0-9]+/', '', $pointValue);
    if ($pointValue === '') {
        return null;
    }

    $points = (int) $pointValue;
    if ($points < 1) {
        return null;
    }

    return [
        'jenis_peraturan' => $ruleName,
        'poin_peraturan' => $points,
    ];
}

function app_apply_peraturan_sync(mysqli $connect, array $rules): array
{
    $connect->begin_transaction();

    try {
        $existingRules = [];
        $result = $connect->query('SELECT id_peraturan, jenis_peraturan FROM peraturan');
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $existingRules[strtolower(trim((string) ($row['jenis_peraturan'] ?? '')))] = (int) ($row['id_peraturan'] ?? 0);
            }
        }

        $insert = $connect->prepare('INSERT INTO peraturan (jenis_peraturan, poin_peraturan) VALUES (?, ?)');
        $update = $connect->prepare('UPDATE peraturan SET poin_peraturan = ? WHERE id_peraturan = ?');
        if (!$insert || !$update) {
            throw new RuntimeException('Persiapan query peraturan gagal.');
        }

        $added = 0;
        $updated = 0;
        foreach ($rules as $rule) {
            $existingId = $existingRules[strtolower(trim($rule['jenis_peraturan']))] ?? 0;
            if ($existingId > 0) {
                $update->bind_param('ii', $rule['poin_peraturan'], $existingId);
                $update->execute();
                if ($update->affected_rows >= 0) {
                    $updated++;
                }
                continue;
            }

            $insert->bind_param('si', $rule['jenis_peraturan'], $rule['poin_peraturan']);
            $insert->execute();
            if ($insert->affected_rows > 0) {
                $added++;
            }
        }

        $connect->commit();

        return ['added' => $added, 'updated' => $updated, 'total' => count($rules)];
    } catch (Throwable $exception) {
        $connect->rollback();
        throw $exception;
    }
}

function app_apply_student_sync(mysqli $connect, array $students): void
{
    $connect->begin_transaction();

    try {
        $classMap = app_sync_classes($connect, $students);
        $existingStudents = app_existing_students_by_nisn($connect);
        $hasViolationTable = app_table_exists($connect, 'pelanggaran');

        $insertStudent = $connect->prepare(
            'INSERT INTO siswa (id_kelas, nisn, nama_lengkap, nama_ibu, foto_siswa, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, no_telepon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $updateStudent = $connect->prepare(
            'UPDATE siswa SET id_kelas = ?, nisn = ?, nama_lengkap = ?, nama_ibu = ?, foto_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, agama = ?, alamat = ?, no_telepon = ? WHERE id_siswa = ?'
        );

        if (!$insertStudent || !$updateStudent) {
            throw new RuntimeException('Query sinkronisasi siswa gagal disiapkan.');
        }

        $deleteViolation = null;
        if ($hasViolationTable) {
            $deleteViolation = $connect->prepare('DELETE FROM pelanggaran WHERE id_siswa = ?');
            if (!$deleteViolation) {
                throw new RuntimeException('Query hapus pelanggaran gagal disiapkan.');
            }
        }

        $deleteStudent = $connect->prepare('DELETE FROM siswa WHERE id_siswa = ?');
        if (!$deleteStudent) {
            throw new RuntimeException('Query hapus siswa gagal disiapkan.');
        }

        $activeStudentIds = [];
        foreach ($students as $student) {
            $classId = $classMap[$student['kelas']] ?? null;
            if ($classId === null) {
                continue;
            }

            $existingStudent = $existingStudents[$student['nisn']] ?? null;
            $motherName = trim((string) ($existingStudent['nama_ibu'] ?? ''));
            $photoPath = $existingStudent['foto_siswa'] ?? '';

            if (
                $motherName === '' ||
                in_array(
                    strtolower($motherName),
                    ['belum tersedia di file sumber', 'belum diisi saat upload'],
                    true
                )
            ) {
                $motherName = 'Belum diisi saat upload';
            }

            if ($photoPath === '') {
                $photoPath = 'assets/img/default.jpg';
            }

            if ($existingStudent !== null) {
                $studentId = (int) $existingStudent['id_siswa'];
                $activeStudentIds[] = $studentId;

                $desired = [
                    'id_kelas' => (int) $classId,
                    'nisn' => $student['nisn'],
                    'nama_lengkap' => $student['nama_lengkap'],
                    'nama_ibu' => $motherName,
                    'foto_siswa' => $photoPath,
                    'jenis_kelamin' => (int) $student['jenis_kelamin'],
                    'tempat_lahir' => $student['tempat_lahir'],
                    'tanggal_lahir' => $student['tanggal_lahir'],
                    'agama' => (int) $student['agama'],
                    'alamat' => $student['alamat'],
                    'no_telepon' => $student['no_telepon'],
                ];

                if (!app_student_needs_update($existingStudent, $desired)) {
                    continue;
                }

                $updateStudent->bind_param(
                    'issssississi',
                    $desired['id_kelas'],
                    $desired['nisn'],
                    $desired['nama_lengkap'],
                    $desired['nama_ibu'],
                    $desired['foto_siswa'],
                    $desired['jenis_kelamin'],
                    $desired['tempat_lahir'],
                    $desired['tanggal_lahir'],
                    $desired['agama'],
                    $desired['alamat'],
                    $desired['no_telepon'],
                    $studentId
                );
                if (!$updateStudent->execute()) {
                    throw new RuntimeException('Data siswa gagal diperbarui.');
                }
                continue;
            }

            $insertStudent->bind_param(
                'issssississ',
                $classId,
                $student['nisn'],
                $student['nama_lengkap'],
                $motherName,
                $photoPath,
                $student['jenis_kelamin'],
                $student['tempat_lahir'],
                $student['tanggal_lahir'],
                $student['agama'],
                $student['alamat'],
                $student['no_telepon']
            );
            if (!$insertStudent->execute()) {
                throw new RuntimeException('Data siswa gagal ditambahkan.');
            }

            $activeStudentIds[] = (int) $connect->insert_id;
        }

        foreach ($existingStudents as $existingStudent) {
            $studentId = (int) $existingStudent['id_siswa'];
            if (in_array($studentId, $activeStudentIds, true)) {
                continue;
            }

            if ($deleteViolation instanceof mysqli_stmt) {
                $deleteViolation->bind_param('i', $studentId);
                if (!$deleteViolation->execute()) {
                    throw new RuntimeException('Pelanggaran siswa gagal dihapus.');
                }
            }

            $deleteStudent->bind_param('i', $studentId);
            if (!$deleteStudent->execute()) {
                throw new RuntimeException('Data siswa lama gagal dihapus.');
            }
        }

        app_prune_classes($connect, $students);
        $connect->commit();
    } catch (Throwable $exception) {
        $connect->rollback();
        throw $exception;
    }
}

function app_sync_classes(mysqli $connect, array $students): array
{
    $classMap = [];

    $result = $connect->query('SELECT id_kelas, nama_kelas FROM kelas');
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $classMap[$row['nama_kelas']] = (int) $row['id_kelas'];
        }
    }

    $classNames = [];
    foreach ($students as $student) {
        $classNames[$student['kelas']] = true;
    }

    if ($classNames === []) {
        return $classMap;
    }

    $statement = $connect->prepare('INSERT INTO kelas (nama_kelas) VALUES (?)');
    if (!$statement) {
        throw new RuntimeException('Query kelas gagal disiapkan.');
    }

    foreach (array_keys($classNames) as $className) {
        if (isset($classMap[$className])) {
            continue;
        }

        $statement->bind_param('s', $className);
        if (!$statement->execute()) {
            throw new RuntimeException('Kelas baru gagal ditambahkan.');
        }
        $classMap[$className] = (int) $connect->insert_id;
    }

    return $classMap;
}

function app_existing_students_by_nisn(mysqli $connect): array
{
    $students = [];
    $query = 'SELECT id_siswa, id_kelas, nisn, nama_lengkap, nama_ibu, foto_siswa, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, no_telepon FROM siswa';
    $result = $connect->query($query);

    if (!$result instanceof mysqli_result) {
        return $students;
    }

    while ($row = $result->fetch_assoc()) {
        $students[$row['nisn']] = $row;
    }

    return $students;
}

function app_student_needs_update(array $current, array $desired): bool
{
    foreach ($desired as $key => $value) {
        if ((string) ($current[$key] ?? '') !== (string) $value) {
            return true;
        }
    }

    return false;
}

function app_prune_classes(mysqli $connect, array $students): void
{
    $activeClassNames = [];
    foreach ($students as $student) {
        $className = trim((string) ($student['kelas'] ?? ''));
        if ($className !== '') {
            $activeClassNames[$className] = true;
        }
    }

    $result = $connect->query('SELECT id_kelas, nama_kelas FROM kelas');
    if (!$result instanceof mysqli_result) {
        return;
    }

    $deleteClass = $connect->prepare('DELETE FROM kelas WHERE id_kelas = ?');
    if (!$deleteClass) {
        throw new RuntimeException('Query hapus kelas lama gagal disiapkan.');
    }

    while ($row = $result->fetch_assoc()) {
        $className = trim((string) ($row['nama_kelas'] ?? ''));
        if (isset($activeClassNames[$className])) {
            continue;
        }

        $classId = (int) ($row['id_kelas'] ?? 0);
        $deleteClass->bind_param('i', $classId);
        if (!$deleteClass->execute()) {
            throw new RuntimeException('Kelas lama gagal dihapus.');
        }
    }
}

function app_write_student_sync_snapshot(string $sourcePath, array $students, string $sourceHash, ?string $originalName = null): array
{
    $snapshotPath = app_student_sync_snapshot_path();
    $snapshotDir = dirname($snapshotPath);
    if (!is_dir($snapshotDir)) {
        mkdir($snapshotDir, 0777, true);
    }

    $classNames = [];
    foreach ($students as $student) {
        $classNames[$student['kelas']] = true;
    }

    $payload = [
        'source_file' => $originalName !== null && $originalName !== '' ? $originalName : basename($sourcePath),
        'source_path' => $sourcePath,
        'source_hash' => $sourceHash,
        'student_count' => count($students),
        'class_count' => count($classNames),
        'synced_at' => date('Y-m-d H:i:s'),
    ];

    file_put_contents(
        $snapshotPath,
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    return $payload;
}

function app_xlsx_first_sheet_path(ZipArchive $zip): ?string
{
    $workbookXml = $zip->getFromName('xl/workbook.xml');
    $relationsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
    if ($workbookXml === false || $relationsXml === false) {
        return null;
    }

    $workbook = @simplexml_load_string($workbookXml);
    $relations = @simplexml_load_string($relationsXml);
    if ($workbook === false || $relations === false) {
        return null;
    }

    $workbook->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $workbook->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
    $relations->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

    $sheet = $workbook->xpath('//main:sheets/main:sheet[1]');
    $relationshipList = $relations->xpath('//rel:Relationship');
    if ($sheet === false || $relationshipList === false || $sheet === []) {
        return null;
    }

    $sheetId = (string) ($sheet[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'] ?? '');
    if ($sheetId === '') {
        return null;
    }

    foreach ($relationshipList as $relationship) {
        if ((string) ($relationship['Id'] ?? '') !== $sheetId) {
            continue;
        }

        $target = (string) ($relationship['Target'] ?? '');
        if ($target === '') {
            return null;
        }

        return str_starts_with($target, 'xl/') ? $target : 'xl/' . ltrim($target, '/');
    }

    return null;
}

function app_xlsx_shared_strings(ZipArchive $zip): array
{
    $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedStringsXml === false) {
        return [];
    }

    $document = @simplexml_load_string($sharedStringsXml);
    if ($document === false) {
        return [];
    }

    $document->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $items = $document->xpath('//main:si');
    if ($items === false) {
        return [];
    }

    $sharedStrings = [];
    foreach ($items as $item) {
        $item->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $texts = $item->xpath('.//main:t');
        if ($texts === false) {
            $sharedStrings[] = '';
            continue;
        }

        $value = '';
        foreach ($texts as $textNode) {
            $value .= (string) $textNode;
        }

        $sharedStrings[] = $value;
    }

    return $sharedStrings;
}

function app_xlsx_cell_value(SimpleXMLElement $cell, array $sharedStrings): string
{
    $type = (string) ($cell['t'] ?? '');
    $value = isset($cell->v) ? (string) $cell->v : '';

    if ($type === 's') {
        return $sharedStrings[(int) $value] ?? '';
    }

    if ($type === 'inlineStr') {
        return isset($cell->is->t) ? (string) $cell->is->t : '';
    }

    return $value;
}

function app_normalize_source_header(string $header): string
{
    $header = strtolower(trim($header));
    $header = str_replace(['(', ')', '.', '-', '/'], ' ', $header);
    $header = preg_replace('/\s+/', ' ', $header);

    $map = [
        'nomor' => 'nomor',
        'nama' => 'nama',
        'nis' => 'nis',
        'jenis kelamin' => 'jenis_kelamin',
        'nisn' => 'nisn',
        'tempat lahir' => 'tempat_lahir',
        'tgl lahir' => 'tgl_lahir',
        'agama' => 'agama',
        'alamat' => 'alamat',
        'nomor tlp' => 'nomor_tlp',
        'kelas' => 'kelas',
    ];

    return $map[$header] ?? '';
}

function app_normalize_student_row(array $row): ?array
{
    $name = trim((string) ($row['nama'] ?? ''));
    $nisn = preg_replace('/\s+/', '', (string) ($row['nisn'] ?? ''));
    $className = trim((string) ($row['kelas'] ?? ''));

    if ($name === '' || $nisn === '' || $className === '') {
        return null;
    }

    return [
        'nama_lengkap' => $name,
        'nisn' => $nisn,
        'jenis_kelamin' => app_map_gender((string) ($row['jenis_kelamin'] ?? '')),
        'tempat_lahir' => trim((string) ($row['tempat_lahir'] ?? '')),
        'tanggal_lahir' => app_normalize_source_date((string) ($row['tgl_lahir'] ?? '')),
        'agama' => app_map_religion((string) ($row['agama'] ?? '')),
        'alamat' => trim((string) ($row['alamat'] ?? '')),
        'no_telepon' => trim((string) ($row['nomor_tlp'] ?? '')),
        'kelas' => $className,
    ];
}

function app_map_gender(string $gender): int
{
    $gender = strtolower(trim($gender));
    return str_contains($gender, 'perempuan') ? 1 : 0;
}

function app_map_religion(string $religion): int
{
    $religion = strtolower(trim($religion));
    $map = [
        'islam' => 0,
        'kristen protestan' => 1,
        'protestan' => 1,
        'kristen katholik' => 2,
        'katolik' => 2,
        'hindu' => 3,
        'buddha' => 4,
        'budha' => 4,
        'konghucu' => 5,
    ];

    return $map[$religion] ?? 0;
}

function app_normalize_source_date(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '2000-01-01';
    }

    if (is_numeric($value)) {
        $days = (int) round((float) $value);
        $date = new DateTime('1899-12-30');
        $date->modify('+' . $days . ' days');
        return $date->format('Y-m-d');
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return '2000-01-01';
    }

    return date('Y-m-d', $timestamp);
}

function app_table_exists(mysqli $connect, string $tableName): bool
{
    $safeTableName = $connect->real_escape_string($tableName);
    $result = $connect->query("SHOW TABLES LIKE '{$safeTableName}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function app_table_columns(mysqli $connect, string $tableName): array
{
    $columns = [];
    $result = $connect->query("SHOW COLUMNS FROM `{$tableName}`");
    if (!$result instanceof mysqli_result) {
        return $columns;
    }

    while ($column = $result->fetch_assoc()) {
        $columns[$column['Field']] = [
            'type' => $column['Type'] ?? '',
            'null' => $column['Null'] ?? '',
            'default' => $column['Default'] ?? null,
        ];
    }

    return $columns;
}
