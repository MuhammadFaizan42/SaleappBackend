<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// ⚠️ Apna folder name yahan set karein
//$path = str_replace("/oracle-api", "", $path);

// Health route
if ($path === "/api/health") {
    echo json_encode(["success" => true, "message" => "API Working"]);
    exit;
}

// Users route
if ($path === "/api/users") {
    require_once "users.php";
    exit;
}

// Login route
if ($path === "/api/login") {
    require_once "login.php";
    exit;
}

http_response_code(404);
echo json_encode(["success" => false, "message" => "Route not found"]);
