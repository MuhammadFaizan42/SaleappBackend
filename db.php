<?php

function env($key, $default = null)
{
    // First priority: Render / server environment variables
    $val = getenv($key);
    if ($val !== false && $val !== "") {
        return $val;
    }

    // Fallback: local .env file (for localhost testing)
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

function db()
{
    $host = env("DB_HOST");
    $port = env("DB_PORT", "1521");
    $service = env("DB_SERVICE_NAME");
    $username = env("DB_USERNAME");
    $password = env("DB_PASSWORD");

    $dsn = "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SERVICE_NAME=$service)))";

    try {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" => "DB Connection Failed",
            "error" => $e->getMessage()
        ]);
        exit;
    }
}
