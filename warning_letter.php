<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header('location: login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

if (!app_can_manage_warning_letters($_SESSION)) {
    http_response_code(403);
    echo 'Akses ditolak.';
    exit;
}

function warning_letter_return_url(array $request): string
{
    $params = ['page' => 'peringatan'];
    $kelas = isset($request['return_kelas']) ? (string) $request['return_kelas'] : '';
    $cari = isset($request['return_search']) ? trim((string) $request['return_search']) : '';

    if ($kelas !== '' && $kelas !== '-1') {
        $params['kelas'] = (string) (int) $kelas;
    }

    if ($cari !== '') {
        $params['cari'] = $cari;
    }

    return 'index.php?' . http_build_query($params);
}

$action = isset($_REQUEST['action']) ? trim((string) $_REQUEST['action']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $redirectUrl = warning_letter_return_url($_POST);
    $studentId = isset($_POST['student_id']) ? (int) $_POST['student_id'] : 0;
    $letterType = isset($_POST['letter_type']) ? trim((string) $_POST['letter_type']) : '';

    try {
        $result = app_warning_letter_create($connect, $studentId, $letterType, $_SESSION);
        $letter = $result['letter'] ?? null;
        $template = $result['template'] ?? null;
        $templateLabel = is_array($template) ? (string) ($template['label'] ?? $letterType) : $letterType;

        if (($result['status'] ?? '') === 'existing' && is_array($letter)) {
            app_flash_set('warning_letter', [
                'type' => 'info',
                'message' => 'Surat ' . $templateLabel . ' sudah tersedia. Silakan download file yang tersimpan.',
            ]);
        } elseif (is_array($letter)) {
            app_flash_set('warning_letter', [
                'type' => 'success',
                'message' => 'Surat ' . $templateLabel . ' berhasil dibuat dengan nomor ' . (string) ($letter['no_surat'] ?? '-') . '.',
            ]);
        } else {
            app_flash_set('warning_letter', [
                'type' => 'success',
                'message' => 'Surat berhasil dibuat.',
            ]);
        }
    } catch (Throwable $exception) {
        app_flash_set('warning_letter', [
            'type' => 'danger',
            'message' => $exception->getMessage(),
        ]);
    }

    header('Location: ' . $redirectUrl);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'regenerate') {
    $redirectUrl = warning_letter_return_url($_POST);
    $letterId = isset($_POST['letter_id']) ? (int) $_POST['letter_id'] : 0;

    try {
        $result = app_warning_letter_regenerate($connect, $letterId);
        $letter = $result['letter'] ?? null;
        $template = $result['template'] ?? null;
        $templateLabel = is_array($template) ? (string) ($template['label'] ?? 'surat') : 'surat';

        if (is_array($letter)) {
            app_flash_set('warning_letter', [
                'type' => 'success',
                'message' => 'Surat ' . $templateLabel . ' berhasil diregenerate. Nomor ' . (string) ($letter['no_surat'] ?? '-') . ' tetap dipertahankan.',
            ]);
        } else {
            app_flash_set('warning_letter', [
                'type' => 'success',
                'message' => 'Surat berhasil diregenerate.',
            ]);
        }
    } catch (Throwable $exception) {
        app_flash_set('warning_letter', [
            'type' => 'danger',
            'message' => $exception->getMessage(),
        ]);
    }

    header('Location: ' . $redirectUrl);
    exit;
}

if ($action === 'download') {
    $letterId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $letter = app_warning_letter_find_by_id($connect, $letterId);

    if ($letter === null) {
        http_response_code(404);
        echo 'Surat tidak ditemukan.';
        exit;
    }

    $absolutePath = app_warning_letter_absolute_path((string) ($letter['file_path'] ?? ''));
    if ($absolutePath === '' || !is_file($absolutePath)) {
        http_response_code(404);
        echo 'File surat tidak ditemukan.';
        exit;
    }

    $downloadName = app_warning_letter_download_name($letter);
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
    header('Content-Length: ' . (string) filesize($absolutePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($absolutePath);
    exit;
}

http_response_code(400);
echo 'Permintaan surat tidak valid.';
