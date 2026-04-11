<?php

function app_load_env(string $path): void
{
    static $loadedPaths = [];

    if (isset($loadedPaths[$path]) || !is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $separatorPosition = strpos($line, '=');
        if ($separatorPosition === false) {
            continue;
        }

        $key = trim(substr($line, 0, $separatorPosition));
        $value = trim(substr($line, $separatorPosition + 1));

        if ($key === '') {
            continue;
        }

        $valueLength = strlen($value);
        if (
            $valueLength >= 2 &&
            (($value[0] === '"' && $value[$valueLength - 1] === '"') || ($value[0] === "'" && $value[$valueLength - 1] === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }

    $loadedPaths[$path] = true;
}

function app_env(string $key, $default = null)
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    if (array_key_exists($key, $_SERVER)) {
        return $_SERVER[$key];
    }

    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}
