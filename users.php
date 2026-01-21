<?php
require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Only GET method allowed"]);
    exit;
}

try {
    $db = db();

    // ⚠️ Table name change kar sakte hain
    $sql = "SELECT * FROM USERS";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "data" => $users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Users fetch failed",
        "error" => $e->getMessage()
    ]);
}
