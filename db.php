<?php

function env($key, $default = null)
{
    $val = getenv($key);
    if ($val !== false && $val !== "") return $val;

    $path = __DIR__ . "/.env";
    if (!file_exists($path)) return $default;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), "#")) continue;
        [$k, $v] = array_pad(explode("=", $line, 2), 2, null);
        $k = trim($k);
        $v = trim($v);
        $v = trim($v, "\"'");
        if ($k === $key) return $v;
    }

    return $default;
}

function oci_db_connect()
{
    $host = env("DB_HOST");
    $port = env("DB_PORT", "1521");
    $service = env("DB_SERVICE_NAME");
    $username = env("DB_USERNAME");
    $password = env("DB_PASSWORD");

    // Oracle EZConnect format
    $connStr = "$host:$port/$service";

    $conn = oci_connect($username, $password, $connStr, "AL32UTF8");

    if (!$conn) {
        $e = oci_error();
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" => "DB Connection Failed",
            "error" => $e["message"] ?? "Unknown Oracle error"
        ]);
        exit;
    }

    return $conn;
}
