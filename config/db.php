<?php
  require_once __DIR__ . '/../lib/env.php';
  require_once __DIR__ . '/../lib/app.php';

  app_load_env(dirname(__DIR__) . '/.env');

  $dbHost = app_env('DB_HOST', '127.0.0.1');
  $dbPort = (int) app_env('DB_PORT', 3306);
  $dbName = app_env('DB_NAME', 'konseling2');
  $dbUser = app_env('DB_USER', 'root');
  $dbPass = app_env('DB_PASS', '');
  $dbSocket = app_env('DB_SOCKET', '');

  $connect = null;
  $connectionErrors = [];
  $connectionHosts = [$dbHost];

  if ($dbSocket === '' && $dbHost === 'localhost') {
    $connectionHosts[] = '127.0.0.1';
  }

  foreach (array_values(array_unique($connectionHosts)) as $host) {
    try {
      if ($dbSocket !== '') {
        $candidate = new mysqli($host, $dbUser, $dbPass, $dbName, $dbPort, $dbSocket);
      } else {
        $candidate = new mysqli($host, $dbUser, $dbPass, $dbName, $dbPort);
      }
    } catch (mysqli_sql_exception $exception) {
      $connectionErrors[] = $host . ': ' . $exception->getMessage();
      continue;
    }

    if ($candidate->connect_errno) {
      $connectionErrors[] = $host . ': ' . $candidate->connect_error;
      $candidate->close();
      continue;
    }

    $connect = $candidate;
    break;
  }

  if (!$connect instanceof mysqli) {
    $lastError = $connectionErrors !== [] ? end($connectionErrors) : 'Unknown database connection error.';
    die('Connection failed: ' . $lastError);
  }

  $connect->set_charset('utf8mb4');

  app_boot($connect);
?>
